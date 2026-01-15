<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Services\GoogleCalendarSyncService;

class CalendarController extends Controller
{
    protected $syncService;

    public function __construct(GoogleCalendarSyncService $syncService)
    {
        $this->syncService = $syncService;
    }

    public function generateToken()
    {
        $user = auth()->user();
        if (!$user->ical_token) {
            $user->ical_token = Str::random(32);
            $user->save();
        }
        return back()->with('success', 'Token de calendario generado con éxito.');
    }

    /**
     * Provide the ics feed.
     */
    public function feed($token)
    {
        $user = User::where('ical_token', $token)->firstOrFail();
        
        // Fetch confirmed and pending appointments
        $appointments = Appointment::with('user')
            ->whereIn('estado', ['confirmado', 'pendiente'])
            ->get();

        $ical = "BEGIN:VCALENDAR\r\n";
        $ical .= "VERSION:2.0\r\n";
        $ical .= "PRODID:-//NazarenaDeLuca//Calendar//ES\r\n";
        $ical .= "CALSCALE:GREGORIAN\r\n";
        $ical .= "METHOD:PUBLISH\r\n";
        $ical .= "X-WR-CALNAME:Agenda Lic. Nazarena De Luca\r\n";
        $ical .= "REFRESH-INTERVAL;VALUE=DURATION:PT15M\r\n"; // Suggest 15m refresh

        foreach ($appointments as $appt) {
            $start = $appt->fecha_hora->format('Ymd\THis');
            // Sessions duration: 45 min
            $end = $appt->fecha_hora->copy()->addMinutes(45)->format('Ymd\THis');
            $uid = "appt-{$appt->id}@nazarenadeluca.com";
            
            $statusLabel = ($appt->estado == 'pendiente') ? "[PENDIENTE] " : "";
            $summary = "{$statusLabel}Sesión: " . ($appt->user->nombre ?? 'Paciente');
            
            $meetLink = "https://meet.google.com/landing"; // Placeholder or configured
            $description = "Paciente: " . ($appt->user->nombre ?? 'N/A') . "\\n" .
                           "Modalidad: " . $appt->modalidad . "\\n" .
                           "Estado: " . $appt->estado;
            
            if ($appt->modalidad != 'presencial') {
                $description .= "\\nLink Sesión: " . $meetLink;
                $location = "SESIÓN VIRTUAL: " . $meetLink;
            } else {
                $location = "Consultorio";
            }

            $ical .= "BEGIN:VEVENT\r\n";
            $ical .= "UID:{$uid}\r\n";
            $ical .= "DTSTAMP:" . gmdate('Ymd\THis\Z') . "\r\n";
            $ical .= "DTSTART:{$start}\r\n";
            $ical .= "DTEND:{$end}\r\n";
            $ical .= "SUMMARY:{$summary}\r\n";
            $ical .= "DESCRIPTION:{$description}\r\n";
            $ical .= "LOCATION:{$location}\r\n";
            $ical .= "END:VEVENT\r\n";
        }

        $ical .= "END:VCALENDAR";

        return response($ical)
            ->header('Content-Type', 'text/calendar; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="agenda.ics"');
    }

    /**
     * Update the Google Calendar Secret URL for the admin.
     */
    public function updateGoogleUrl(Request $request)
    {
        $request->validate([
            'google_calendar_url' => ['nullable', 'url', 'regex:/basic\.ics$/']
        ], [
            'google_calendar_url.regex' => 'La URL debe ser la "Dirección secreta en formato iCal" (debe terminar en basic.ics).'
        ]);

        $user = auth()->user();
        $user->google_calendar_url = $request->google_calendar_url;
        $user->save();

        if ($user->google_calendar_url) {
            $this->syncService->sync($user);
        }

        return back()->with('success', 'Configuración de Google Calendar guardada y sincronizada.');
    }

    /**
     * Trigger manual sync.
     */
    public function syncGoogle()
    {
        $user = auth()->user();
        if ($this->syncService->sync($user)) {
            return back()->with('success', 'Calendario sincronizado con éxito.');
        }
        return back()->with('error', 'Hubo un error al sincronizar. Verificá la URL.');
    }

    /**
     * Store a new base availability.
     */
    public function storeAvailability(Request $request)
    {
        $request->validate([
            'dia_semana' => 'required',
            'hora_inicio' => 'required',
            'hora_fin' => 'required'
        ]);

        if ($request->dia_semana === 'all') {
            for ($i = 0; $i <= 6; $i++) {
                \App\Models\Availability::create([
                    'dia_semana' => $i,
                    'hora_inicio' => $request->hora_inicio,
                    'hora_fin' => $request->hora_fin
                ]);
            }
            return back()->with('success', 'Horario de atención agregado para todos los días.');
        }

        $request->validate(['dia_semana' => 'integer|min:0|max:6']);
        \App\Models\Availability::create($request->all());

        return back()->with('success', 'Horario de atención agregado.');
    }

    /**
     * Remove a base availability.
     */
    public function deleteAvailability($id)
    {
        \App\Models\Availability::findOrFail($id)->delete();
        return back()->with('success', 'Horario de atención eliminado.');
    }

    /**
     * Store a specifically blocked day.
     */
    public function storeBlockedDay(Request $request)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today|unique:blocked_days,date',
            'reason' => 'nullable|string|max:255'
        ], [
            'date.unique' => 'Ese día ya está bloqueado.',
            'date.after_or_equal' => 'No podés bloquear días pasados.'
        ]);

        \App\Models\BlockedDay::create([
            'date' => $request->date,
            'reason' => $request->reason
        ]);

        return back()->with('success', 'Día bloqueado con éxito.');
    }

    /**
     * Remove a blocked day.
     */
    public function destroyBlockedDay($id)
    {
        \App\Models\BlockedDay::findOrFail($id)->delete();
        return back()->with('success', 'Día desbloqueado.');
    }

    /**
     * Toggle weekend blocking preference.
     */
    public function toggleWeekends(Request $request)
    {
        $user = auth()->user();
        $user->block_weekends = !$user->block_weekends;
        $user->save();
        
        $status = $user->block_weekends ? 'bloqueados' : 'desbloqueados';
        return back()->with('success', "Fines de semana {$status} correctamente.");
    }

    /**
     * Import Argentina Holidays 2026.
     */
    public function importHolidays()
    {
        $holidays = [
            ['date' => '2026-01-01', 'reason' => 'Año Nuevo'],
            ['date' => '2026-02-16', 'reason' => 'Carnaval'],
            ['date' => '2026-02-17', 'reason' => 'Carnaval'],
            ['date' => '2026-03-24', 'reason' => 'Día de la Memoria'],
            ['date' => '2026-04-02', 'reason' => 'Día del Veterano'],
            ['date' => '2026-04-03', 'reason' => 'Viernes Santo'],
            ['date' => '2026-05-01', 'reason' => 'Día del Trabajador'],
            ['date' => '2026-05-25', 'reason' => 'Día de la Revolución de Mayo'],
            ['date' => '2026-06-17', 'reason' => 'Paso a la Inmortalidad de Güemes'],
            ['date' => '2026-06-20', 'reason' => 'Paso a la Inmortalidad de Belgrano'],
            ['date' => '2026-07-09', 'reason' => 'Día de la Independencia'],
            ['date' => '2026-08-17', 'reason' => 'Paso a la Inmortalidad de San Martín'],
            ['date' => '2026-10-12', 'reason' => 'Día del Respeto a la Diversidad Cultural'],
            ['date' => '2026-11-20', 'reason' => 'Día de la Soberanía Nacional'],
            ['date' => '2026-12-08', 'reason' => 'Inmaculada Concepción'],
            ['date' => '2026-12-25', 'reason' => 'Navidad'],
        ];

        foreach ($holidays as $holiday) {
            \App\Models\BlockedDay::firstOrCreate(
                ['date' => $holiday['date']],
                ['reason' => $holiday['reason']]
            );
        }

        return back()->with('success', 'Feriados de Argentina 2026 importados.');
    }
}
