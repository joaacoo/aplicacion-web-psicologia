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
        // Detectar móvil
        $userAgent = request()->header('User-Agent');
        $isMobile = false;
        if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $userAgent)) {
            $isMobile = true;
        }

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
        
        // Blocked Days from Admin
        $blockedDays = \App\Models\BlockedDay::where('date', '>=', now()->toDateString())
            ->pluck('date')
            ->map(fn($d) => is_string($d) ? \Carbon\Carbon::parse($d)->format('Y-m-d') : $d->format('Y-m-d'))
            ->toArray();
        // We add them as "occupied" but the frontend will handle them specially if needed.
        // Actually, for full day blocking, we need to handle it in the view or here.
        // Let's pass $blockedDays to the view separately to disable the whole day in the calendar.

        $occupiedSlots = array_values(array_unique(array_merge($occupiedAppts, $occupiedExternal)));

        // Patient specific data for the view
        $resources = \App\Models\Resource::where('paciente_id', Auth::id())->latest()->get();
        $documents = \App\Models\Document::where('user_id', Auth::id())->latest()->get();
        $blockWeekends = $adminUser->block_weekends ?? false;

        // For Sequential Payment logic, we need ALL appointments to find the "next" one
        $allAppointments = Appointment::where('usuario_id', Auth::id())
            ->where('estado', '!=', 'cancelado')
            ->orderBy('fecha_hora', 'asc')
            ->get()
            ->map(function ($turno) {
                // Marcar como realizado si pasó la hora
                if ($turno->isPastSessionTime() && !$turno->isRealizado()) {
                    $turno->markAsRealizado();
                }
                
                // Agregar atributos para la vista
                $turno->is_payable = $turno->isPayable();
                $turno->payment_block_reason = $turno->getPaymentBlockReason();
                $turno->hours_until_deadline = $turno->getHoursUntilPaymentDeadline();
                $turno->payment_deadline = $turno->getPaymentDeadline();
                $turno->is_realizado = $turno->isRealizado();

                return $turno;
            });

        // Next confirmed appointment for the banner
        $nextAppointment = $allAppointments->where('fecha_hora', '>=', now())
            ->where('estado', 'confirmado')
            ->first();

        // Used by calendar and stepper
        $patientAppointmentsDates = $allAppointments->pluck('fecha_hora')->map(fn($d) => $d->format('Y-m-d'))->toArray();
        $fixedReservation = $allAppointments->firstWhere('es_recurrente', true);

        // Separar para mejor UX
        // ---------------------------------------------------------
        // LOGICA DE PAGO SECUENCIAL Y VISIBILIDAD (4 SESIONES)
        // ---------------------------------------------------------
        
        // 1. Obtener sesiones futuras o pasadas sin pago
        $allFuture = $allAppointments->filter(function($a) {
            return $a->fecha_hora->isFuture() || ($a->isPastSessionTime() && (!$a->payment || $a->payment->estado !== 'verificado'));
        });

        // 1b. Obtener sesiones pasadas que ya fueron pagadas (finalizadas)
        $completedAppointmentsCollection = $allAppointments->filter(function($a) {
            return $a->isPastSessionTime() && $a->payment && $a->payment->estado === 'verificado';
        });

        // 2. Tomar las próximas 4 para mostrar (aprox 1 mes)
        //    (Incluso si solo hay 1, tomamos esa. Si hay 0, collection vacía)
        //    Si es reserva fija, aseguramos mostrar las próximas instancias aunque no estén creadas
        
        $projectedAppointments = collect();

        if ($fixedReservation) {
            // Project up to the end of December of the year the reservation was made
            $reservationYear = $fixedReservation->fecha_hora->year;
            $endOfYear = \Carbon\Carbon::create($reservationYear, 12, 31)->endOfDay();
            
            // Start from the LATEST future appointment's date, OR now if none
            $lastDate = $allFuture->count() > 0 ? $allFuture->last()->fecha_hora : now();
            
            // Fixed details
            $frecuencia = $fixedReservation->frecuencia; // 'semanal' or 'quincenal'
            $baseDate = $fixedReservation->fecha_hora->copy(); // Keep the exact time too
            $intervalDays = ($frecuencia === 'quincenal') ? 14 : 7;
            
            // Align to the grid: find the first occurrence strictly after $lastDate
            $nextDate = $baseDate->copy();
            while ($nextDate->lte($lastDate)) {
                $nextDate->addDays($intervalDays);
            }
            
            // Generate future sessions
            while ($nextDate->lte($endOfYear)) {
                
                // Create Virtual Appointment
                $virtual = new Appointment();
                $virtual->id = null; // Virtual
                $virtual->usuario_id = Auth::id();
                $virtual->fecha_hora = $nextDate->copy();
                $virtual->estado = 'pendiente'; // Proyectado, aún no confirmado
                $virtual->es_recurrente = true;
                $virtual->frecuencia = $frecuencia;
                $virtual->modalidad = $fixedReservation->modalidad;
                $virtual->monto_final = $fixedReservation->monto_final;
                $virtual->is_virtual = true; // Flag for View
                $virtual->payment_block_reason = 'Se habilitará próximamente.';
                $virtual->ui_status = 'locked_sequential'; // Default locked
                
                $projectedAppointments->push($virtual);
                
                $nextDate->addDays($intervalDays);
            }
        }

        // Merge real and projected 
        $allFutureAndProjected = $allFuture->concat($projectedAppointments)->sortBy('fecha_hora')->values();
        
        // Re-apply Sequential Logic on the combined list
        $payableAppointment = null;
        $allFutureAndProjected->transform(function($appt, $key) use (&$payableAppointment) {
            // Is it the FIRST in this visible list?
            $isFirst = ($key === 0); 
            
            if ($isFirst && !$appt->is_virtual) {
                if ($appt->isPayable()) {
                    $appt->ui_status = 'payable';
                    $payableAppointment = $appt;
                } else {
                    $appt->ui_status = 'locked';
                }
            } else {
                $appt->ui_status = 'locked_sequential';
                $appt->payment_block_reason = 'Se habilitará al finalizar la sesión anterior.';
            }
            return $appt;
        });

        // Append completed sessions AT THE END (sorted most recent first)
        $allFutureAndProjected = $allFutureAndProjected->concat($completedAppointmentsCollection->sortByDesc('fecha_hora'))->values();

        // Apply filters if present
        if (request()->has('filter')) {
            if (request('month_filter')) {
                $allFutureAndProjected = $allFutureAndProjected->filter(function($a) {
                    return $a->fecha_hora->format('n') == request('month_filter');
                })->values();
            }
            if (request('status')) {
                $allFutureAndProjected = $allFutureAndProjected->filter(function($a) {
                    if (request('status') === 'finalizado') {
                        return $a->estado === 'finalizado' || $a->is_realizado;
                    }
                    if (request('status') === 'recuperada') {
                        return $a->recuperada ?? false;
                    }
                    return $a->estado === request('status');
                })->values();
            }
            if (request('pago_estado')) {
                $allFutureAndProjected = $allFutureAndProjected->filter(function($a) {
                    $estadoPago = $a->payment ? $a->payment->estado : 'pendiente';
                    return $estadoPago === request('pago_estado');
                })->values();
            }
        }

        // Paginate or Take 2 depending on ver_todo OR if filtering
        if (request()->has('ver_todo') || request()->has('filter')) {
            $perPage = 4;
            $page = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
            
            $visibleFutureSessions = new \Illuminate\Pagination\LengthAwarePaginator(
                $allFutureAndProjected->forPage($page, $perPage)->values(),
                $allFutureAndProjected->count(),
                $perPage,
                $page,
                ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(), 'query' => request()->query()]
            );
        } else {
            $visibleFutureSessions = $allFutureAndProjected->take(2);
        }

        // 3. Sesiones COMPLETADAS (Historial para vista)
        $completedAppointments = $completedAppointmentsCollection;

        // Get pending recovery requests keyed by appointment ID
        $pendingRecoveryIds = \App\Models\Waitlist::where('usuario_id', Auth::id())
            ->whereNotNull('original_appointment_id')
            ->pluck('original_appointment_id')
            ->toArray();

        // Extraer datos de UI para la tabla principal (paginada) --> We don't really use this map anymore in view, we use objects directly
        $appointmentUiData = [];
        // ...
        
        if (request()->ajax()) {
            return view('dashboard.patient', [
                'appointments' => $visibleFutureSessions,
                'projectedAppointments' => $projectedAppointments,
                'visibleFutureSessions' => $visibleFutureSessions,
                'isMobile' => $isMobile,
                'isAjax' => true,
                'pendingRecoveryIds' => $pendingRecoveryIds,
            ])->render();
        }
        foreach($visibleFutureSessions as $appt) {
             $appointmentUiData[$appt->id] = [
                 'ui_status' => $appt->ui_status ?? null,
                 'payment_block_reason' => $appt->payment_block_reason ?? null
             ];
        }

        // Asignar appointments para la tabla (usa visibleFutureSessions)
        $appointments = $visibleFutureSessions;

        // Notifications
        $notifications = Auth::user()->unreadNotifications;

        // Credits
        $totalCredits = Auth::user()->paciente ? Auth::user()->paciente->credits()->where('status', 'active')->sum('amount') : 0;

        return view('dashboard.patient', compact(
            'appointments', 
            'allAppointments', 
            'payableAppointment', 
            'visibleFutureSessions', 
            'completedAppointments',
            'appointmentUiData', 
            'resources',
            'documents',
            'nextAppointment',
            'pendingRecoveryIds',
            'patientAppointmentsDates',
            'fixedReservation',
            'blockedDays',
            'blockWeekends',
            'occupiedSlots', 
            'adminUser',
            'availabilities',
            'googleAvailableSlots',
            'notifications',
            'totalCredits'
        ));
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
        $pendingAppointments = Appointment::where('estado', 'pendiente')->count();
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

        // Sincronización automática de Google Calendar (Throttled inside service)
        if (auth()->user()->google_calendar_url) {
            $this->syncService->sync(auth()->user());
        }

        // Turnos de hoy (Upcoming only)
        $todayAppointments = Appointment::with(['user', 'payment'])
            ->whereDate('fecha_hora', \Carbon\Carbon::today())
            ->where('fecha_hora', '>=', now()) // Filter past appointments
            ->orderBy('fecha_hora', 'asc')
            ->get();
            
        // Proyectar citas fijas para hoy (Turnos de Hoy widget)
        $fixedReservationsToday = Appointment::where('es_recurrente', true)
            ->where('estado', '!=', 'cancelado')
            ->whereNotNull('fecha_hora')
            ->orderBy('fecha_hora', 'asc')
            ->with(['user', 'payment'])
            ->get()
            ->unique('usuario_id');
            
        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();
        $projectedToday = collect();
        
        foreach($fixedReservationsToday as $fixed) {
            $frecuencia = $fixed->frecuencia; 
            $baseDate = $fixed->fecha_hora->copy(); 
            $intervalDays = ($frecuencia === 'quincenal') ? 14 : 7;
            
            $iterDate = $baseDate->copy();
            while ($iterDate->copy()->addDays($intervalDays)->lt($todayStart)) {
                $iterDate->addDays($intervalDays);
            }
            if ($iterDate->lt($todayStart)) {
                 $iterDate->addDays($intervalDays);
            }
             
            if ($iterDate->gte($todayStart) && $iterDate->lte($todayEnd) && $iterDate->gte(now())) {
                $apptDateTime = $iterDate->copy();
                
                $exists = $todayAppointments->contains(function($appt) use ($apptDateTime, $fixed) {
                    return $appt->usuario_id == $fixed->usuario_id 
                        && $appt->fecha_hora->format('Y-m-d H:i') == $apptDateTime->format('Y-m-d H:i');
                });
                
                if(!$exists) {
                    $virtual = new Appointment();
                    $virtual->id = - abs(crc32('proj_home_' . $fixed->usuario_id . '_' . $apptDateTime->timestamp));
                    $virtual->usuario_id = $fixed->usuario_id;
                    $virtual->fecha_hora = $apptDateTime;
                    $virtual->estado = 'confirmado';
                    $virtual->es_recurrente = true;
                    $virtual->frecuencia = $frecuencia;
                    $virtual->is_projected = true;
                    $virtual->user = $fixed->user;
                    $virtual->payment = null; // Projected so not paid yet possibly
                    $projectedToday->push($virtual);
                }
            }
        }
        
        if($projectedToday->count() > 0) {
            $todayAppointments = $todayAppointments->concat($projectedToday)->sortBy('fecha_hora')->values();
        }

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


        // --- Nuevas Métricas Financieras (CACHED for 30 seconds) ---
        $stats = \Illuminate\Support\Facades\Cache::remember('admin_dashboard_stats', 30, function() {
            // 1. Ingresos del Mes (Solo pagos APROBADOS este mes)
            $monthlyIncome = Appointment::whereYear('fecha_hora', now()->year)
                ->whereMonth('fecha_hora', now()->month)
                ->whereHas('payment', function($q) {
                    $q->where('estado', 'verificado');
                })
                ->sum('monto_final');

            // 2. Por Cobrar (En realidad: DINERO EN REVISIÓN / Comprobantes Pendientes)
            $pendingIncome = Appointment::where('estado', '!=', 'cancelado')
                ->whereHas('payment', function($q) {
                    $q->where('estado', 'pendiente');
                })
                ->count();

            // 3. Pacientes Nuevos (Total registrados, o ajustar rango si se desea)
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

            return compact('monthlyIncome', 'pendingIncome', 'newPatientsCount', 'sessionsCount', 'averageSessionPrice');
        });

        extract($stats);

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

    public function patientHome(Request $request)
    {
        $isMobile = is_mobile();
        $perPage = 2;

        $query = Appointment::where('usuario_id', Auth::id())
            ->with(['payment'])
            ->when(request('month_filter'), function($q) {
                $q->whereMonth('fecha_hora', request('month_filter'));
            })
            ->when(request('status'), function($q) {
                $q->where('estado', request('status'));
            })
            ->when(request('pago_estado'), function($q) {
                $q->whereHas('payment', function($pq) {
                    $pq->where('estado', request('pago_estado'));
                });
            })
            ->orderByRaw('CASE WHEN fecha_hora >= ? THEN 0 ELSE 1 END', [now()])
            ->orderBy('fecha_hora', 'asc');

        // Global Sequential Payment Helper: find the absolute first one that needs payment
        $firstUnpaidId = Appointment::where('usuario_id', Auth::id())
            ->where('estado', '!=', 'cancelado')
            ->where(function($q) {
                $q->whereDoesntHave('payment')
                  ->orWhereHas('payment', function($pq) {
                      $pq->where('estado', '!=', 'verificado');
                  });
            })
            ->orderBy('fecha_hora', 'asc')
            ->value('id');

        $appointments = $query->paginate($perPage)->appends(request()->query());

        // Fetch Credit Balance
        $creditBalance = 0;
        $paciente = Auth::user()->paciente;
        if ($paciente) {
            $creditBalance = \App\Models\PatientCredit::where('paciente_id', $paciente->id)
                ->where('is_used', false)
                ->sum('amount');
        }

        if (request()->ajax()) {
            return view('dashboard.patient', [
                'appointments' => $appointments,
                'isMobile' => $isMobile,
                'isAjax' => true,
                'firstUnpaidId' => $firstUnpaidId,
                'creditBalance' => $creditBalance
            ])->render();
        }

        return view('dashboard.patient', compact('appointments', 'isMobile', 'creditBalance'))->with([
            'firstUnpaidId' => $firstUnpaidId
        ]);
    }

    public function adminAgenda(Request $request)
    {
        // Sincronización automática de Google Calendar removida por rendimiento
        
        // Default: Start from January 2026 (the minimum allowed)
        $minAllowed = \Carbon\Carbon::create(2026, 1, 1)->startOfDay();
        
        // If month/year provided in request, use them; otherwise default to January 2026
        $month = $request->input('month');
        $year = $request->input('year');
        
        if (!$month || !$year) {
            // Default to current month/year
            $month = now()->month;
            $year = now()->year;
        }

        // Validate: Don't allow going before January 2026
        $currentDate = \Carbon\Carbon::createFromDate($year, $month, 1);
        if ($currentDate->lt($minAllowed)) {
            $currentDate = $minAllowed->copy();
            $month = 1;
            $year = 2026;
        }
        
        // Appointments del mes seleccionado
        $appointments = Appointment::with(['user', 'payment'])
            ->whereYear('fecha_hora', $year)
            ->whereMonth('fecha_hora', $month)
            ->orderBy('fecha_hora', 'asc')
            ->get();

        // Proyectar citas futuras para reservas fijas para el MES SELECCIONADO
        $projectedAppointments = collect();
        
        // Obtenemos las reservas fijas originales (priorizando las más antiguas como base)
        $fixedReservations = Appointment::where('es_recurrente', true)
            ->where('estado', '!=', 'cancelado')
            ->whereNotNull('fecha_hora')
            ->orderBy('fecha_hora', 'asc') // Tomar la primera base
            ->with('user')
            ->get()
            ->unique(function ($item) {
                return $item->usuario_id; // Una sola cadencia base por paciente
            });

        $startOfMonth = $currentDate->copy()->startOfMonth();
        $endOfMonth = $currentDate->copy()->endOfMonth();
        $endOfYear = now()->endOfYear();
        
        foreach($fixedReservations as $fixedReservation) {
            $userId = $fixedReservation->usuario_id;
            $frecuencia = $fixedReservation->frecuencia; // semanal o quincenal
            $baseDate = $fixedReservation->fecha_hora->copy(); 
            $intervalDays = ($frecuencia === 'quincenal') ? 14 : 7;
            
            // Fast forward from baseDate to the start of the current viewing month
            $iterDate = $baseDate->copy();
            while ($iterDate->copy()->addDays($intervalDays)->lt($startOfMonth)) {
                $iterDate->addDays($intervalDays);
            }
            // Ensure first internal generation lands inside this month if originally before
            if ($iterDate->lt($startOfMonth)) {
                 $iterDate->addDays($intervalDays);
             }
            
            // Generate dates hasta fin de año 2026
            while($iterDate->lte($endOfYear)) {
                if ($iterDate->gte($startOfMonth) && $iterDate->lte($endOfMonth)) {
                    $apptDateTime = $iterDate->copy();
                    
                    // Check if exists in DB (to avoid projecting over a real record)
                    $exists = $appointments->contains(function($appt) use ($apptDateTime, $userId) {
                        return $appt->usuario_id == $userId 
                            && $appt->fecha_hora->format('Y-m-d H:i') == $apptDateTime->format('Y-m-d H:i');
                    });
                    
                        if(!$exists) {
                            $virtual = new Appointment();
                            $virtual->id = - abs(crc32('proj_' . $userId . '_' . $apptDateTime->timestamp));
                            $virtual->usuario_id = $userId;
                            $virtual->fecha_hora = $apptDateTime;
                            $virtual->estado = 'confirmado';
                            $virtual->es_recurrente = true;
                            $virtual->frecuencia = $frecuencia;
                            $virtual->modalidad = $fixedReservation->modalidad;
                            $virtual->monto_final = $fixedReservation->monto_final;
                            $virtual->is_projected = true;
                            $virtual->user = $fixedReservation->user;
                            $projectedAppointments->push($virtual);
                        }
                    }
                $iterDate->addDays($intervalDays);
            }
        }

        // Merge projected appointments into the main appointments collection
        if($projectedAppointments->count() > 0) {
            $appointments = $appointments->concat($projectedAppointments)->sortBy('fecha_hora');
        }

        // Eventos de Google del mes seleccionado
        // Filtramos en memoria o DB. Como ExternalEvent tiene start_time, usamos DB.

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

        $prev = $currentDate->copy()->subMonth();

        return view('dashboard.admin.agenda', compact(
            'todayAppointments', 
            'nextAdminAppointment', 
            'appointments', 
            'externalEvents', 
            'recentRegistrations',
            'currentDate', // Para la navegación en la vista
            'prev',
            'minAllowed',
            'blockedDays' // Pass blocked days/holidays
        ));
    }

    public function adminPacientes(Request $request)
    {
        $search = $request->input('search');
        $type = $request->input('type');
        $sort = $request->input('sort', 'name'); // default sort by name
        $order = $request->input('order', 'asc');

        $query = \App\Models\User::where('rol', 'paciente')
            ->select('id', 'nombre', 'email', 'created_at') // Limit columns for User
            ->with(['paciente:id,user_id,tipo_paciente,telefono,meet_link,precio_personalizado', 'turnos' => function($q) {
                $q->where('es_recurrente', true)->orderBy('fecha_hora', 'desc')->limit(1); // Only need the most recent recurring one
            }]);

        // Filter by Name
        if ($search) {
            $query->where('nombre', 'like', '%' . $search . '%');
        }

        // Filter by Type
        if ($type) {
            $query->whereHas('paciente', function($q) use ($type) {
                $q->where('tipo_paciente', $type);
            });
        }

        // Sort
        if ($sort === 'turnos') {
            $query->orderBy('turnos_count', $order);
        } else {
            $query->orderBy('nombre', $order);
        }

        $patients = $query->paginate(6)->appends($request->all());

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
