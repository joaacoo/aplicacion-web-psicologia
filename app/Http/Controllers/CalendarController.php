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

        $sessionDuration = $user->duracion_sesion ?? 45;

        foreach ($appointments as $appt) {
            $start = $appt->fecha_hora->format('Ymd\THis');
            // Sessions duration: dynamic
            $end = $appt->fecha_hora->copy()->addMinutes($sessionDuration)->format('Ymd\THis');
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
        
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'block_weekends' => $user->block_weekends,
                'message' => "Fines de semana {$status} correctamente."
            ]);
        }

        return back()->with('success', "Fines de semana {$status} correctamente.");
    }

    /**
     * Import Argentina Holidays 2026.
     * Sincroniza con los feriados oficiales de Argentina para el año actual.
     */
    public function importHolidays()
    {
        $year = now()->year;
        
        // Calcular carnaval y pascua (feriados móviles basados en calendario eclesiástico)
        $easter = $this->calculateEaster($year);
        $carnival1 = $easter->copy()->subDays(48);
        $carnival2 = $easter->copy()->subDays(47);
        $goodFriday = $easter->copy()->subDays(2);
        
        // Feriados fijos y móviles de Argentina
        $holidays = [
            // Fijos
            ['date' => $year . '-01-01', 'reason' => 'Año Nuevo'],
            ['date' => $year . '-03-24', 'reason' => 'Día Nacional de la Memoria por la Verdad y la Justicia'],
            ['date' => $year . '-04-02', 'reason' => 'Día del Veterano y de los Caídos en la Guerra de Malvinas'],
            ['date' => $year . '-05-01', 'reason' => 'Día del Trabajador'],
            ['date' => $year . '-05-25', 'reason' => 'Día de la Revolución de Mayo'],
            ['date' => $year . '-06-17', 'reason' => 'Paso a la Inmortalidad del General Martín Miguel de Güemes'],
            ['date' => $year . '-06-20', 'reason' => 'Paso a la Inmortalidad del General Manuel Belgrano'],
            ['date' => $year . '-07-09', 'reason' => 'Día de la Independencia'],
            ['date' => $year . '-08-17', 'reason' => 'Paso a la Inmortalidad del General José de San Martín'],
            ['date' => $year . '-10-12', 'reason' => 'Día del Respeto a la Diversidad Cultural'],
            ['date' => $year . '-11-20', 'reason' => 'Día de la Soberanía Nacional'],
            ['date' => $year . '-12-08', 'reason' => 'Inmaculada Concepción de María'],
            ['date' => $year . '-12-25', 'reason' => 'Navidad'],
            // Móviles (Carnaval y Viernes Santo)
            ['date' => $carnival1->format('Y-m-d'), 'reason' => 'Carnaval'],
            ['date' => $carnival2->format('Y-m-d'), 'reason' => 'Carnaval'],
            ['date' => $goodFriday->format('Y-m-d'), 'reason' => 'Viernes Santo'],
        ];

        $created = 0;
        $updated = 0;
        
        foreach ($holidays as $holiday) {
            $blockedDay = \App\Models\BlockedDay::updateOrCreate(
                ['date' => $holiday['date']],
                ['reason' => $holiday['reason']]
            );
            
            if ($blockedDay->wasRecentlyCreated) {
                $created++;
            } else {
                $updated++;
            }
        }

        $message = "Feriados de Argentina {$year} sincronizados. ";
        if ($created > 0) $message .= "{$created} nuevos. ";
        if ($updated > 0) $message .= "{$updated} actualizados.";
        
        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'created' => $created,
                'updated' => $updated
            ]);
        }

        return back()->with('success', $message);
    }
    
    /**
     * Calculate Easter Sunday for a given year (Algorithm by Gauss)
     */
    private function calculateEaster($year)
    {
        $a = $year % 19;
        $b = floor($year / 100);
        $c = $year % 100;
        $d = floor($b / 4);
        $e = $b % 4;
        $f = floor(($b + 8) / 25);
        $g = floor(($b - $f + 1) / 3);
        $h = (19 * $a + $b - $d - $g + 15) % 30;
        $i = floor($c / 4);
        $k = $c % 4;
        $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
        $m = floor(($a + 11 * $h + 22 * $l) / 451);
        $month = floor(($h + $l - 7 * $m + 114) / 31);
        $day = (($h + $l - 7 * $m + 114) % 31) + 1;
        
        return \Carbon\Carbon::create($year, $month, $day);
    }

    /**
     * Update session duration and interval settings.
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'duracion_sesion' => 'required|integer|min:15|max:180',
            'intervalo_sesion' => 'required|integer|min:0|max:60',
        ]);

        $user = auth()->user();
        $user->duracion_sesion = $request->duracion_sesion;
        $user->intervalo_sesion = $request->intervalo_sesion;
        $user->save();

        return back()->with('success', 'Configuración de sesiones actualizada.');
    }
}
