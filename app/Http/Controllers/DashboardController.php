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
        // Dynamic Pagination: 3 for Mobile, 5 for Desktop
        $userAgent = request()->header('User-Agent');
        $isMobile = false;
        
        if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $userAgent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($userAgent, 0, 4))) {
            $isMobile = true;
        }

        $perPage = $isMobile ? 3 : 5;

        $appointments = Appointment::where('usuario_id', Auth::id())
            ->with('payment')
            ->orderBy('fecha_hora', 'desc')
            ->paginate($perPage);

        // Obtener configuración del Admin
        $adminUser = \App\Models\User::where('rol', 'admin')->with('profesional')->first();
        $sessionDuration = $adminUser->duracion_sesion ?? 45;
        $sessionInterval = $adminUser->intervalo_sesion ?? 15;

        $dbAvailabilities = \App\Models\Availability::all();
        $availabilities = collect();
        foreach ($dbAvailabilities as $av) {
            if (!$av->hora_inicio || !$av->hora_fin) continue;
            try {
                $start = \Carbon\Carbon::parse($av->hora_inicio);
                $end = \Carbon\Carbon::parse($av->hora_fin);
                
                // Evitar bucles infinitos si hora fin < hora inicio
                if ($end->lt($start)) continue;

                while ($start->copy()->addMinutes($sessionDuration)->lte($end)) {
                    $slotStart = $start->format('H:i');
                    $slotEnd = $start->copy()->addMinutes($sessionDuration)->format('H:i');
                    
                    $availabilities->push([
                        'dia_semana' => $av->dia_semana,
                        'hora_inicio' => $slotStart . ':00',
                        'hora_fin' => $slotEnd . ':00',
                        'label' => $slotStart . ' - ' . $slotEnd,
                        'modalidad' => $av->modalidad ?? 'presencial',
                    ]);
                    $start->addMinutes($sessionDuration + $sessionInterval);
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        $occupiedAppts = Appointment::where('estado', '!=', 'cancelado')
            ->where('fecha_hora', '>=', now())
            ->pluck('fecha_hora')
            ->map(fn($t) => $t->format('Y-m-d H:i:s'))
            ->toArray();

        // Optimized External Events Query: Limit to next 3 months to prevent loading thousands of past events
        $externalEvents = ExternalEvent::where('start_time', '>=', now())
            ->where('start_time', '<=', now()->addMonths(3))
            ->get();
            
        // Identificar cuáles son "Espacios Disponibles" (Keyword: LIBRE o DISPONIBLE)
        $googleRawEvents = $externalEvents->filter(function($e) {
            $t = strtoupper($e->title);
            return str_contains($t, 'LIBRE') || str_contains($t, 'DISPONIBLE') || str_contains($t, 'ATENCION');
        });

        // Expandir rangos en slots dinámicos
    $googleAvailableSlots = [];
    foreach ($googleRawEvents as $event) {
        $current = $event->start_time->copy();
        // Mientras haya al menos la duración de la sesión disponible
        while ($current->copy()->addMinutes($sessionDuration)->lte($event->end_time)) {
            $slotStart = $current->format('H:i');
            $slotEnd = $current->copy()->addMinutes($sessionDuration)->format('H:i');
            
            $googleAvailableSlots[] = [
                'date' => $current->format('Y-m-d'),
                'time' => $slotStart . ':00',
                'hora_inicio' => $slotStart . ':00',
                'label' => $slotStart . ' - ' . $slotEnd,
                'modalidad' => 'cualquiera'
            ];
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
        $blockedDays = \App\Models\BlockedDay::where('date', '>=', now()->toDateString())
            ->pluck('date')
            ->map(fn($d) => is_string($d) ? \Carbon\Carbon::parse($d)->format('Y-m-d') : $d->format('Y-m-d'))
            ->toArray();
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

        // Fechas donde el paciente ya tiene turno (para bloquear en calendario)
        $patientAppointmentsDates = Appointment::where('usuario_id', Auth::id())
            ->where('estado', '!=', 'cancelado')
            ->where('fecha_hora', '>=', now())
            ->get()
            ->map(fn($a) => $a->fecha_hora->format('Y-m-d'))
            ->unique()
            ->values()
            ->toArray();

        return view('dashboard.patient', compact('appointments', 'availabilities', 'occupiedSlots', 'nextAppointment', 'resources', 'documents', 'googleAvailableSlots', 'blockedDays', 'blockWeekends', 'patientAppointmentsDates'));
    }

    public function patientDocuments()
    {
        $documents = \App\Models\Document::where('user_id', Auth::id())->latest()->get();
        return view('dashboard.patient.documents', compact('documents'));
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

        // Count pending appointments (esperando_pago)
        $pendingAppointments = Appointment::where('estado', 'esperando_pago')->count();
        $confirmedAppointments = Appointment::where('estado', 'confirmado')->count();

        // Calculate Monthly Income (Real Income)
        $currentMonthIncome = Appointment::whereMonth('fecha_hora', now()->month)
            ->where('estado', 'confirmado')
            ->whereHas('payment', function($q) {
                $q->where('estado', 'verificado');
            })
            ->sum('monto_final');

        // Calculate Pending Income (appointments waiting for payment verification)
        $pendingIncome = \App\Models\Payment::where('estado', 'pendiente')->sum('monto');

        // Listado de Pacientes
        $patients = \App\Models\User::where('rol', 'paciente')
            ->orderBy('nombre', 'asc')
            ->with(['documents', 'paciente']) 
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
        // PERFORMANCE FIX: Disabled auto-sync on load to prevent slow page loads.
        // Sync should be done via background job or manual button.
        /*
        if (auth()->user()->google_calendar_url) {
            $this->syncService->sync(auth()->user());
        }
        */

        // Eventos externos (Google) - Optimizado
        $externalEvents = ExternalEvent::where('start_time', '>=', now()->subMonth())
            ->where('start_time', '<=', now()->addMonths(6))
            ->orderBy('start_time', 'asc')
            ->get();

        // Disponibilidades base
        $availabilities = \App\Models\Availability::orderBy('dia_semana')->orderBy('hora_inicio')->get();

        // Días Bloqueados Manualmente
        $blockedDays = \App\Models\BlockedDay::where('date', '>=', now()->toDateString())->orderBy('date')->get();
        
        $blockWeekends = auth()->user()->block_weekends;

        // --- ESTADÍSTICAS PARA EL DASHBOARD (admin.blade.php esperas estas variables) ---
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // 1. Ingresos Mes
        $ingresosMes = Appointment::whereYear('fecha_hora', $currentYear)
            ->whereMonth('fecha_hora', $currentMonth)
            ->whereIn('estado', ['confirmado', 'asistido', 'completado'])
            ->sum('monto_final');

        // 2. Por Cobrar
        $porCobrar = Appointment::where('fecha_hora', '<', now())
            ->where('estado', 'confirmado')
            ->sum('monto_final');

        // 3. Pacientes Nuevos
        $patientsThisMonth = Appointment::whereYear('fecha_hora', $currentYear)
            ->whereMonth('fecha_hora', $currentMonth)
            ->whereIn('estado', ['confirmado', 'asistido', 'completado'])
            ->with(['user.paciente'])
            ->get()
            ->groupBy('usuario_id');

        $nuevosPacientes = 0;
        foreach ($patientsThisMonth as $userId => $appts) {
            $patient = $appts->first()->user->paciente ?? null;
            if ($patient && $patient->tipo_paciente === 'nuevo') $nuevosPacientes++;
        }
        
        // 4. Sesiones del Mes
        $sesionesMes = Appointment::whereYear('fecha_hora', $currentYear)
            ->whereMonth('fecha_hora', $currentMonth)
            ->where('estado', '!=', 'cancelado')
            ->count();

        // 5. Comprobantes Pendientes (Para mostrar en algún lugar si se desea, aunque finanzas es el lugar principal)
        // La vista admin.blade.php NO parece tener una sección para esto, pero lo calculamos por si acaso
        // o para agregarlo después.
        
        return view('dashboard.admin', compact(
            'appointments', 'todayAppointments', 'patients', 'activityLogs', 
            'resources', 'recentRegistrations', 'nextAdminAppointment', 'documents', 
            'waitlist', 'externalEvents', 'availabilities', 'blockedDays', 'blockWeekends',
            'ingresosMes', 'porCobrar', 'nuevosPacientes', 'sesionesMes'
        ));
    }

    // Métodos separados para cada sección
    public function adminHome(Request $request)
    {
        // 1. Redirect Developer removed per user request to see psychologist screen
        // if (auth()->user()->email === 'joacooodelucaaa16@gmail.com') {
        //     return redirect()->route('admin.developer');
        // }

        // Sincronización automática de Google Calendar
        if (auth()->user()->google_calendar_url) {
            $this->syncService->sync(auth()->user());
        }

        // Turnos de hoy (Upcoming only)
        $todayAppointments = Appointment::with(['user', 'payment'])
            ->whereDate('fecha_hora', \Carbon\Carbon::today())
            ->where('fecha_hora', '>=', now()) // Filter past appointments
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
        
        // 1. Ingresos del Mes (Solo pagos APROBADOS este mes)
        $monthlyIncome = Appointment::whereYear('fecha_hora', now()->year)
            ->whereMonth('fecha_hora', now()->month)
            ->whereHas('payment', function($q) {
                $q->where('estado', 'verificado');
            })
            ->sum('monto_final');

        // 2. Por Cobrar (En realidad: DINERO EN REVISIÓN / Comprobantes Pendientes)
        // User requested COUNT of pending receipts, not amount.
        $pendingIncome = Appointment::whereHas('payment', function($q) {
                $q->where('estado', 'pendiente');
            })
            ->count();

        // 3. Pacientes Nuevos (Total registrados, o ajustar rango si se desea)
        // User reports seeing 0 when there are known new patients. Checking broader definition.
        $newPatientsCount = \App\Models\User::where('rol', 'paciente')
            ->whereYear('created_at', now()->year)
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
        $activityLogs = \App\Models\ActivityLog::with('user')
            ->whereHas('user', function($q) {
                $q->where('rol', 'admin');
            })
            ->latest()
            ->take(50)
            ->get();

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
