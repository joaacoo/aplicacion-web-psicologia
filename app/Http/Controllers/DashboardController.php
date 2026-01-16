<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Waitlist;
use App\Models\ExternalEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Services\GoogleCalendarSyncService;

class DashboardController extends Controller
{
    protected $syncService;

    public function __construct(GoogleCalendarSyncService $syncService)
    {
        $this->syncService = $syncService;
    }
    public function patientDashboard()
    {
        $appointments = Appointment::where('usuario_id', Auth::id())
            ->with('payment')
            ->orderBy('fecha_hora', 'asc')
            ->get();

        $availabilities = \App\Models\Availability::all();
        $occupiedAppts = Appointment::where('estado', '!=', 'cancelado')
            ->where('fecha_hora', '>=', now())
            ->pluck('fecha_hora')
            ->map(fn($t) => $t->format('Y-m-d H:i:s'))
            ->toArray();

        $externalEvents = ExternalEvent::where('start_time', '>=', now())
            ->get();
            
        // Identificar cuáles son "Espacios Disponibles" (Keyword: LIBRE o DISPONIBLE)
        $googleRawEvents = $externalEvents->filter(function($e) {
            $t = strtoupper($e->title);
            return str_contains($t, 'LIBRE') || str_contains($t, 'DISPONIBLE') || str_contains($t, 'ATENCION');
        });

        // Obtener configuración del Admin
        $adminUser = \App\Models\User::where('rol', 'admin')->first();
        $sessionDuration = $adminUser->duracion_sesion ?? 45;
        $sessionInterval = $adminUser->intervalo_sesion ?? 15;

        // Expandir rangos en slots dinámicos
        $googleAvailableSlots = collect();
        foreach ($googleRawEvents as $event) {
            $current = $event->start_time->copy();
            // Mientras haya al menos la duración de la sesión disponible
            while ($current->copy()->addMinutes($sessionDuration)->lte($event->end_time)) {
                $googleAvailableSlots->push([
                    'start_time' => $current->copy(),
                    'end_time' => $current->copy()->addMinutes($sessionDuration),
                ]);
                // Saltamos duración + intervalo para el siguiente slot
                $current->addMinutes($sessionDuration + $sessionInterval);
            }
        }

        // Eventos que representan BLOQUEOS
        $busyExternal = $externalEvents->reject(function($e) {
            $t = strtoupper($e->title);
            return str_contains($t, 'LIBRE') || str_contains($t, 'DISPONIBLE') || str_contains($t, 'ATENCION');
        });

        $occupiedExternal = $busyExternal->map(fn($e) => $e->start_time->format('Y-m-d H:i:s'))->toArray();
        
        $occupiedExternal = $busyExternal->map(fn($e) => $e->start_time->format('Y-m-d H:i:s'))->toArray();
        
        // Blocked Days from Admin
        $blockedDays = \App\Models\BlockedDay::where('date', '>=', now()->toDateString())->pluck('date')->toArray();
        // We add them as "occupied" but the frontend will handle them specially if needed.
        // Actually, for full day blocking, we need to handle it in the view or here.
        // Let's pass $blockedDays to the view separately to disable the whole day in the calendar.

        $occupiedSlots = array_values(array_unique(array_merge($occupiedAppts, $occupiedExternal)));

        // Las "availabilities" de la base de datos se pasan como fallback o se ignoran si hay de Google
        // Para este nuevo modo, las pasamos pero el JS priorizará las de Google si existen.

        $nextAppointment = Appointment::where('usuario_id', Auth::id())
            ->where('estado', 'confirmado')
            ->where('fecha_hora', '>=', now())
            ->orderBy('fecha_hora', 'asc')
            ->first();

        // Materiales para el paciente o globales
        $resources = \App\Models\Resource::where(function($q) {
            $q->where('paciente_id', Auth::id())
              ->orWhereNull('paciente_id');
        })->latest()->get();

        // Documentos ("No clínicos", recibos, etc)
        $documents = \App\Models\Document::where('user_id', Auth::id())->latest()->get();

        // Obtener configuración del Admin (Asumimos que hay un solo admin o usamos el primero)
        $adminUser = \App\Models\User::where('rol', 'admin')->first();
        $blockWeekends = $adminUser ? $adminUser->block_weekends : false;

        return view('dashboard.patient', compact('appointments', 'availabilities', 'occupiedSlots', 'nextAppointment', 'resources', 'documents', 'googleAvailableSlots', 'blockedDays', 'blockWeekends'));
    }

    public function adminDashboard()
    {
        $appointments = Appointment::with(['user', 'payment'])
            ->orderBy('fecha_hora', 'asc')
            ->get();

        $todayAppointments = Appointment::with(['user', 'payment'])
            ->whereDate('fecha_hora', \Carbon\Carbon::today())
            ->orderBy('fecha_hora', 'asc')
            ->get();

        $patients = \App\Models\User::where('rol', 'paciente')
            ->orderBy('nombre', 'asc')
            ->with(['documents']) // Eager load documents
            ->get();

        // Historial de Acciones y Recursos
        $activityLogs = \App\Models\ActivityLog::with('user')->latest()->take(20)->get();
        $resources = \App\Models\Resource::with('patient')->latest()->get();
        $documents = \App\Models\Document::with('user')->latest()->take(50)->get();

        // Registros recientes para mostrar en la agenda
        $recentRegistrations = \App\Models\User::where('rol', 'paciente')
            ->where('created_at', '>=', now()->subMonths(3))
            ->get(['id', 'nombre', 'created_at']);

        // Próxima sesión (Widget Admin)
        $nextAdminAppointment = Appointment::with('user')
            ->where('estado', 'confirmado')
            ->where('fecha_hora', '>=', now())
            ->orderBy('fecha_hora', 'asc')
            ->first();

        // Lista de Espera
        $waitlist = Waitlist::latest()->get();

        // Sincronización automática de Google Calendar al cargar el dashboard
        if (auth()->user()->google_calendar_url) {
            $this->syncService->sync(auth()->user());
        }

        // Eventos externos (Google)
        $externalEvents = ExternalEvent::orderBy('start_time', 'asc')->get();

        // Disponibilidades base
        $availabilities = \App\Models\Availability::orderBy('dia_semana')->orderBy('hora_inicio')->get();

        // Días Bloqueados Manualmente
        $blockedDays = \App\Models\BlockedDay::where('date', '>=', now()->toDateString())->orderBy('date')->get();
        
        $blockWeekends = auth()->user()->block_weekends;

        return view('dashboard.admin', compact('appointments', 'todayAppointments', 'patients', 'activityLogs', 'resources', 'recentRegistrations', 'nextAdminAppointment', 'documents', 'waitlist', 'externalEvents', 'availabilities', 'blockedDays', 'blockWeekends'));
    }

    // Métodos separados para cada sección
    public function adminHome(Request $request)
    {
        // Sincronización automática de Google Calendar
        if (auth()->user()->google_calendar_url) {
            $this->syncService->sync(auth()->user());
        }

        // Turnos de hoy
        $todayAppointments = Appointment::with(['user', 'payment'])
            ->whereDate('fecha_hora', \Carbon\Carbon::today())
            ->orderBy('fecha_hora', 'asc')
            ->get();

        // Próximo turno
        $nextAdminAppointment = Appointment::with('user')
            ->where('fecha_hora', '>=', now())
            ->orderBy('fecha_hora', 'asc')
            ->first();

        // Contar turnos pendientes
        $pendingCount = Appointment::where('estado', 'pendiente')
            ->orWhere('estado', null)
            ->count();

        // Contar turnos próximos (esta semana)
        $upcomingCount = Appointment::where('fecha_hora', '>=', now())
            ->where('fecha_hora', '<=', now()->addWeek())
            ->count();

        // Total de pacientes
        $totalPatients = \App\Models\User::where('rol', 'patient')->count();

        // Welcome Message Logic (Server-Side for static randomness)
        $hour = now()->hour;
        if ($hour < 12) {
            $greeting = "¡Buen día ☀️"; // Default Icon
        } elseif ($hour < 20) {
            $greeting = "¡Buenas tardes 🌤️";
        } else {
            $greeting = "¡Buenas noches 🌙";
        }

        $frases = [
            "Cada sesión es un puente hacia el bienestar. 🌿",
            "Tu escucha transforma realidades. ✨",
            "Acompañar es un arte; hoy es un gran día para ejercerlo. 🎨",
            "Inspirando cambios genuinos, paso a paso. 🌱",
            "Tu compromiso con el otro hace la diferencia. 🧠",
            "Lista para una nueva jornada de sanación y escucha. ✨",
            "¡Hoy va a ser un gran día! 🚀",
            "El cambio comienza con una conversación honesta. 💬",
            "Cada persona que ayudás, cambia el mundo. 🌍",
            "Tu presencia es el primer paso hacia la sanación. 🤝",
            "Hoy vas a ser testigo de historias de valentía. 💪",
            "La empatía que ofrecés es un regalo invaluable. 💝",
            "Crear espacios seguros es tu superpoder. 🛡️",
            "Hoy alguien va a sentirse comprendido gracias a vos. 🌟",
            "El proceso terapéutico es un viaje compartido. 🚶‍♀️",
            "Tu trabajo planta semillas de esperanza. 🌻"
        ];
        
        $randomPhrase = $frases[array_rand($frases)];
        $welcomeMessage = "$greeting, Nazarena. $randomPhrase";

        return view('dashboard.admin.home', [
            'todayAppointments' => $todayAppointments,
            'nextAdminAppointment' => $nextAdminAppointment,
            'pendingCount' => $pendingCount,
            'upcomingCount' => $upcomingCount,
            'totalPatients' => $totalPatients,
            'isAdmin' => true,
            'welcomeMessage' => $welcomeMessage,
        ]);
    }

    public function adminAgenda(Request $request)
    {
        // Sincronización automática de Google Calendar
        if (auth()->user()->google_calendar_url) {
            $this->syncService->sync(auth()->user());
        }

        // Definir mes y año (Default: Hoy, pero clamped a Enero 2026 como mínimo si se pide histórico)
        // La solicitud del usuario dice "apartir de enero de 2026 para aca adelante", implicando que ese es el inicio.
        $now = now();
        $month = $request->input('month', $now->month);
        $year = $request->input('year', $now->year);

        // Validar fecha mínima (Enero 2026) ?? No estrictamente necesario bloquearlo, pero el UI lo guiará.
        $currentDate = \Carbon\Carbon::createFromDate($year, $month, 1);
        
        // Appointments del mes seleccionado
        $appointments = Appointment::with(['user', 'payment'])
            ->whereYear('fecha_hora', $year)
            ->whereMonth('fecha_hora', $month)
            ->orderBy('fecha_hora', 'asc')
            ->get();

        // Eventos de Google del mes seleccionado
        // Filtramos en memoria o DB. Como ExternalEvent tiene start_time, usamos DB.
        $startOfMonth = $currentDate->copy()->startOfMonth();
        $endOfMonth = $currentDate->copy()->endOfMonth();

        $externalEvents = ExternalEvent::where(function($q) use ($startOfMonth, $endOfMonth) {
            $q->whereBetween('start_time', [$startOfMonth, $endOfMonth])
              ->orWhereBetween('end_time', [$startOfMonth, $endOfMonth]);
        })->orderBy('start_time', 'asc')->get();

        // Para compatibilidad con la vista de lista del día actual (si se mantiene)
        $todayAppointments = Appointment::with(['user', 'payment'])
            ->whereDate('fecha_hora', \Carbon\Carbon::today())
            ->orderBy('fecha_hora', 'asc')
            ->get();

        $nextAdminAppointment = Appointment::with('user')
            ->where('estado', 'confirmado')
            ->where('fecha_hora', '>=', now())
            ->orderBy('fecha_hora', 'asc')
            ->first();

        // Stats rápidos
        $recentRegistrations = \App\Models\User::where('rol', 'paciente')
            ->where('created_at', '>=', now()->subMonths(3))
            ->get(['id', 'nombre', 'created_at']);

        return view('dashboard.admin.agenda', compact(
            'todayAppointments', 
            'nextAdminAppointment', 
            'appointments', 
            'externalEvents', 
            'recentRegistrations',
            'currentDate' // Para la navegación en la vista
        ));
    }

    public function adminPacientes()
    {
        $patients = \App\Models\User::where('rol', 'paciente')
            ->orderBy('nombre', 'asc')
            ->with(['documents'])
            ->get();

        return view('dashboard.admin.pacientes', compact('patients'));
    }

    public function adminPagos()
    {
        $appointments = Appointment::with(['user', 'payment'])
            ->orderBy('fecha_hora', 'asc')
            ->get();

        return view('dashboard.admin.pagos', compact('appointments'));
    }

    public function adminTurnos()
    {
        $appointments = Appointment::with(['user', 'payment'])
            ->orderBy('fecha_hora', 'asc')
            ->get();

        return view('dashboard.admin.turnos', compact('appointments'));
    }

    public function adminDocumentos()
    {
        $resources = \App\Models\Resource::with('patient')->latest()->get();
        $patients = \App\Models\User::where('rol', 'paciente')
            ->orderBy('nombre', 'asc')
            ->get();

        return view('dashboard.admin.documentos', compact('resources', 'patients'));
    }

    public function adminWaitlist()
    {
        $waitlist = Waitlist::latest()->get();

        return view('dashboard.admin.waitlist', compact('waitlist'));
    }

    public function adminConfiguracion()
    {
        // Auto-sync Argentina Holidays for current year
        $this->ensureHolidaysAreSynced(now()->year);

        $availabilities = \App\Models\Availability::orderBy('dia_semana')->orderBy('hora_inicio')->get();
        $blockedDays = \App\Models\BlockedDay::where('date', '>=', now()->toDateString())->orderBy('date')->get();
        $blockWeekends = auth()->user()->block_weekends;

        // Separar feriados oficiales de bloqueos manuales
        $year = now()->year;
        $officialHolidayReasons = [
            'Año Nuevo', 'Carnaval', 'Día Nacional de la Memoria por la Verdad y la Justicia',
            'Día del Veterano y de los Caídos en la Guerra de Malvinas', 'Viernes Santo',
            'Día del Trabajador', 'Día de la Revolución de Mayo',
            'Paso a la Inmortalidad del General Martín Miguel de Güemes',
            'Paso a la Inmortalidad del General Manuel Belgrano', 'Día de la Independencia',
            'Paso a la Inmortalidad del General José de San Martín',
            'Día del Respeto a la Diversidad Cultural', 'Día de la Soberanía Nacional',
            'Inmaculada Concepción de María', 'Navidad',
            // Variaciones posibles
            'Día de la Memoria', 'Día del Veterano', 'Paso a la Inmortalidad de Güemes',
            'Paso a la Inmortalidad de Belgrano', 'Paso a la Inmortalidad de San Martín',
            'Inmaculada Concepción'
        ];
        
        $holidays = $blockedDays->filter(function($bd) use ($officialHolidayReasons) {
            return in_array($bd->reason, $officialHolidayReasons);
        });
        
        $manualBlocks = $blockedDays->filter(function($bd) use ($officialHolidayReasons) {
            return !in_array($bd->reason, $officialHolidayReasons);
        });

        // Sincronización automática de Google Calendar
        if (auth()->user()->google_calendar_url) {
            $this->syncService->sync(auth()->user());
        }

        $externalEvents = ExternalEvent::orderBy('start_time', 'asc')->get();

        return view('dashboard.admin.configuracion', compact(
            'availabilities', 'blockedDays', 'holidays', 'manualBlocks', 
            'blockWeekends', 'externalEvents'
        ));
    }

    public function adminHistorial()
    {
        $activityLogs = \App\Models\ActivityLog::with('user')->latest()->take(50)->get();

        return view('dashboard.admin.historial', compact('activityLogs'));
    }
    /**
     * Helper to auto-sync holidays without redirecting.
     */
    private function ensureHolidaysAreSynced($year)
    {
        // Calculate dynamic holidays (Easter based)
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
        
        $easter = \Carbon\Carbon::create($year, $month, $day);
        $carnival1 = $easter->copy()->subDays(48);
        $carnival2 = $easter->copy()->subDays(47);
        $goodFriday = $easter->copy()->subDays(2);
        
        // List of holidays
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
            // Móviles
            ['date' => $carnival1->format('Y-m-d'), 'reason' => 'Carnaval'],
            ['date' => $carnival2->format('Y-m-d'), 'reason' => 'Carnaval'],
            ['date' => $goodFriday->format('Y-m-d'), 'reason' => 'Viernes Santo'],
        ];

        foreach ($holidays as $holiday) {
            \App\Models\BlockedDay::firstOrCreate(
                ['date' => $holiday['date']],
                ['reason' => $holiday['reason']]
            );
        }
    }
}
