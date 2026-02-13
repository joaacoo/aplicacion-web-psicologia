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
            ->with(['documents', 'paciente']) // Eager load documents and paciente info
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
        // 1. Redirect Developer to Specialized Dashboard
        if (auth()->user()->email === 'joacooodelucaaa16@gmail.com') {
            return redirect()->route('admin.developer');
        }

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

        // Bienvenido logic
        $hour = now()->hour;
        if ($hour < 12) {
            $greeting = "¡Buen día ☀️"; 
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
        
        // Prevent emoji from wrapping alone by adding non-breaking space
        $welcomeMessage = preg_replace('/ (\p{So})/u', "\u{00A0}$1", $welcomeMessage);


        // --- Nuevas Métricas Financieras ---
        
        // 1. Ingresos del Mes (Confirmados/Completados/Asistidos)
        $monthlyIncome = Appointment::whereYear('fecha_hora', now()->year)
            ->whereMonth('fecha_hora', now()->month)
            ->whereIn('estado', ['confirmado', 'asistido', 'completado'])
            ->sum('monto_final');

        // 2. Pagos Pendientes (Históricos Confirmados pero 'pendientes de cobro' conceptualmente, 
        //    usando lógica de FinanceController: fecha < now y estado confirmado)
        $pendingIncome = Appointment::where('fecha_hora', '<', now())
            ->where('estado', 'confirmado')
            ->sum('monto_final');

        // 3. Pacientes Nuevos (Registrados este mes)
        $newPatientsCount = \App\Models\User::where('rol', 'paciente')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        // 4. Sesiones Facturadas y Promedio
        $sessionsThisMonth = Appointment::whereYear('fecha_hora', now()->year)
            ->whereMonth('fecha_hora', now()->month)
            ->whereIn('estado', ['confirmado', 'asistido', 'completado'])
            ->get();
            
        $sessionsCount = $sessionsThisMonth->count();
        $averageSessionPrice = $sessionsCount > 0 ? $sessionsThisMonth->avg('monto_final') : 0;

        return view('dashboard.admin.home', [
            'todayAppointments' => $todayAppointments,
            'nextAdminAppointment' => $nextAdminAppointment,
            'monthlyIncome' => $monthlyIncome,
            'pendingIncome' => $pendingIncome,
            'newPatientsCount' => $newPatientsCount,
            'sessionsCount' => $sessionsCount,
            'averageSessionPrice' => $averageSessionPrice,
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

        // Feriados/Bloqueos
        $blockedDays = \App\Models\BlockedDay::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get();

        return view('dashboard.admin.agenda', compact(
            'todayAppointments', 
            'nextAdminAppointment', 
            'appointments', 
            'externalEvents', 
            'recentRegistrations',
            'recentRegistrations',
            'currentDate', // Para la navegación en la vista
            'blockedDays' // Pass blocked days/holidays
        ));
    }

    public function adminPacientes()
    {
        $patients = \App\Models\User::where('rol', 'paciente')
            ->orderBy('nombre', 'asc')
            ->with(['documents', 'paciente'])
            ->get();

        $basePrice = \App\Models\Setting::get('precio_base_sesion', 25000);

        return view('dashboard.admin.pacientes', compact('patients', 'basePrice'));
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
        $adminUser = auth()->user();
        $pro = $adminUser->profesional; // Can be null, handle defaults

        $sessionDuration = $pro->duracion_sesion ?? 45;
        $sessionInterval = $pro->intervalo_sesion ?? 15;

        // Generate availability slots (logic remains same, just ensuring we use proper vars if used for view)
        // ... (Skipping logic details if not dependent on DB cols directly here, but passed to view)
        
        $availabilities = \App\Models\Availability::orderBy('dia_semana')
            ->orderBy('hora_inicio')
            ->get()
            ->groupBy('dia_semana');

        $blockedDays = \App\Models\BlockedDay::where('date', '>=', \Carbon\Carbon::today())
            ->orderBy('date')
            ->get();
        $blockWeekends = auth()->user()->block_weekends;

        // Separar feriados oficiales de bloqueos manuales
        $year = now()->year;
        $officialHolidayReasons = [
            'Año Nuevo', 'Carnaval', 'Día Nacional de la Memoria',
            'Día del Veterano', 'Malvinas', 'Viernes Santo',
            'Día del Trabajador', 'Revolución de Mayo',
            'Paso a la Inmortalidad', 'Güemes', 'Belgrano', 'San Martín',
            'Independencia', 'Diversidad Cultural', 'Soberanía Nacional',
            'Inmaculada Concepción', 'Navidad',
            'Feriado', 'Puente turístico', 'no laborable', 'Turístico'
        ];
        
        $holidays = $blockedDays->filter(function($bd) use ($officialHolidayReasons) {
            $reason = mb_strtolower(trim($bd->reason));
            foreach ($officialHolidayReasons as $official) {
                if (str_contains($reason, mb_strtolower($official))) {
                    return true;
                }
            }
            return false;
        });
        
        $manualBlocks = $blockedDays->diff($holidays);

        // Sincronización automática de Google Calendar
        if (auth()->user()->google_calendar_url) {
            $this->syncService->sync(auth()->user());
        }

        $externalEvents = ExternalEvent::orderBy('start_time', 'asc')->get();

        $basePrice = \App\Models\Setting::get('precio_base_sesion', 25000);

        return view('dashboard.admin.configuracion', compact(
            'availabilities', 'blockedDays', 'holidays', 'manualBlocks', 
            'blockWeekends', 'externalEvents', 'basePrice'
        ));
    }

    public function adminHistorial()
    {
        $activityLogs = \App\Models\ActivityLog::with('user')->latest()->take(50)->get();

        return view('dashboard.admin.historial', compact('activityLogs'));
    }
    /**
     * Helper to auto-sync holidays using ArgentinaDatos API.
     */
    private function ensureHolidaysAreSynced($year)
    {
        // Cache key to avoid hitting the API on every request if already synced sufficiently
        // But for this requirement, we'll check if we have data for this year roughly.
        // Or just Try/Catch the API call.
        
        // We only want to sync if we haven't synced recently or if it's a manual trigger. 
        // For simplicity: We will rely on `firstOrCreate` to avoid duplicates, but fetching every time is slow.
        // Let's use a simple cache or check count.
        
        $count = \App\Models\BlockedDay::whereYear('date', $year)->count();
        if ($count > 10) { 
             // Assuming > 10 holidays means we are likely synced. 
             // To force sync, user could clear data or we can add a 'force' param.
             // But the user complained about updates (dynamic changes), so maybe we should fetch.
             // Let's use Laravel Cache to limit API calls to once per day per year.
             if (\Illuminate\Support\Facades\Cache::has("holidays_synced_$year")) {
                 return;
             }
        }

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(5)->get("https://api.argentinadatos.com/v1/feriados/{$year}");

            if ($response->successful()) {
                $holidays = $response->json();

                foreach ($holidays as $holiday) {
                    // API returns: {"fecha": "2026-01-01", "tipo": "inamovible", "nombre": "Año Nuevo"}
                    \App\Models\BlockedDay::firstOrCreate(
                        ['date' => $holiday['fecha']],
                        ['reason' => $holiday['nombre']]
                    );
                }
                
                // Cache success for 12 hours
                \Illuminate\Support\Facades\Cache::put("holidays_synced_$year", true, now()->addHours(12));
            }
        } catch (\Exception $e) {
            \Log::error("Error syncing holidays: " . $e->getMessage());
            // Fail silently so dashboard still loads
        }
    }
}
