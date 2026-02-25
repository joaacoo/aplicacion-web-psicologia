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

        $paciente = Auth::user()->paciente;

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

        // FIX (BLOQUEO REFINADO): Identificar horarios ocupados por reservas fijas con metadata
        $fixedBlockedSlots = Appointment::where('es_recurrente', true)
            ->get()
            ->map(function($a) {
                return [
                    'dia' => $a->fecha_hora->dayOfWeek === 0 ? 7 : $a->fecha_hora->dayOfWeek,
                    'hora' => $a->fecha_hora->format('H:i'),
                    'frecuencia' => $a->frecuencia, // 'semanal' / 'quincenal'
                    'modalidad' => $a->modalidad,   // 'presencial' / 'virtual'
                    'week_parity' => $a->fecha_hora->weekOfYear % 2 // 0 or 1
                ];
            })->unique(function($item) {
                // Unicidad por todo para no enviar duplicados exactos, pero permitimos misma hora en distinta paridad si es quincenal
                return $item['dia'].$item['hora'].$item['frecuencia'].$item['week_parity'];
            })->values()->toArray();
        // We add them as "occupied" but the frontend will handle them specially if needed.
        // Actually, for full day blocking, we need to handle it in the view or here.
        // Let's pass $blockedDays to the view separately to disable the whole day in the calendar.

        $occupiedSlots = array_values(array_unique(array_merge($occupiedAppts, $occupiedExternal)));

        // Patient specific data for the view
        $resources = \App\Models\Resource::where('paciente_id', Auth::id())->latest()->get();
        $documents = \App\Models\Document::where('user_id', Auth::id())->latest()->get();
        $blockWeekends = $adminUser->block_weekends ?? false;

        // For Sequential Payment logic, we need ALL appointments to find the "next" one
        // REGLA: Los turnos cancelados NUNCA se borran y deben verse siempre en la tabla
        $allAppointments = Appointment::where('usuario_id', Auth::id())
            ->where('fecha_hora', '>=', now()->subYear()) // Mostrar turnos desde hace 1 año
            ->where(function($q) {
                $q->where('estado', '!=', 'cancelado')
                  ->orWhere('motivo_cancelacion', 'not like', '%Reserva fija%');
            })
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
        
        // 1. Obtener sesiones activas (que aún no terminaron)
        $allFuture = $allAppointments->filter(function($a) {
            return !$a->isPastSessionTime();
        });

        // 1b. Obtener sesiones pasadas (finalizadas)
        $completedAppointmentsCollection = $allAppointments->filter(function($a) {
            return $a->isPastSessionTime();
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

        // Regla especial de UX para reserva fija en este dashboard principal:
        // - Si el paciente tenía reserva fija y la canceló, el filtro robusto ya se encargó 
        //   de ocultar esas sesiones muertas sin borrar los turnos individuales.
        
        // Credits: Ver si tiene créditos activos
        $activeCreditsCount = $paciente ? $paciente->credits()->where('status', 'active')->count() : 0;
        $hasActiveCredit = $activeCreditsCount > 0;

        // Re-apply Sequential Logic on the combined list
        // Create a separate queue for sequential logic that ignores cancelled turns
        $paymentQueue = $allFutureAndProjected->filter(function($a) {
            // No contar cancelados ni turnos de recuperación para bloquear pagos futuros
            return $a->estado !== 'cancelado' && !($a->es_recuperacion ?? false);
        })->values();

        $payableAppointment = null;
        $creditAppliedToThisSession = false;

        $allFutureAndProjected->transform(function($appt) use ($paymentQueue, &$payableAppointment, &$hasActiveCredit, &$creditAppliedToThisSession) {
            // If it's cancelled, it shouldn't be part of the sequential logic blocking/payable flow
            if ($appt->estado === 'cancelado') {
                $appt->ui_status = 'cancelled';
                $appt->is_payable = false;
                $appt->payment_block_reason = 'Turno cancelado.';
                return $appt;
            }

            // Find its index in the payment queue
            $queueIndex = $paymentQueue->search(function($item) use ($appt) {
                // For virtual we need date check, for real we can use ID
                if ($appt->id && $item->id) return $appt->id === $item->id;
                return $appt->fecha_hora->equalTo($item->fecha_hora);
            });

            // Is it the FIRST ACTIVE session in the queue?
            $isFirstActive = ($queueIndex === 0); 
            
            // Si es un turno real (no virtual)
            if (!$appt->is_virtual) {
                // Caso 1: Tiene crédito activo y es el primero que puede recibirlo
                if ($hasActiveCredit && !$creditAppliedToThisSession) {
                    $appt->ui_status = 'credit_applied';
                    $appt->is_payable = false;
                    $creditAppliedToThisSession = true;
                } 
                // Caso 2: Es pagable (es el primero en la cola activa y cumple condiciones)
                elseif ($isFirstActive && $appt->isPayable(false)) {
                    $appt->ui_status = 'payable';
                    $appt->is_payable = true;
                    $payableAppointment = $appt;
                } 
                // Caso 3: Bloqueado por secuencia
                else {
                    $appt->ui_status = 'locked_sequential';
                    $appt->is_payable = false;
                    $appt->payment_block_reason = ($queueIndex > 0) ? 'Se habilitará al finalizar la sesión anterior.' : $appt->getPaymentBlockReason();
                }
            } else {
                // Turnos virtuales/proyectados
                if ($hasActiveCredit && !$creditAppliedToThisSession) {
                    $appt->ui_status = 'credit_applied';
                    $appt->is_payable = false;
                    $creditAppliedToThisSession = true;
                } elseif ($isFirstActive) {
                    $appt->ui_status = 'payable';
                    $appt->is_payable = true;
                } else {
                    $appt->ui_status = 'locked_sequential';
                    $appt->is_payable = false;
                    $appt->payment_block_reason = 'Se habilitará al finalizar la sesión anterior.';
                }
            }
            return $appt;
        });


        // Append completed sessions ONLY if ver_todo or filter is requested
        if (request()->has('ver_todo') || request()->has('filter')) {
            $allFutureAndProjected = $allFutureAndProjected->concat($completedAppointmentsCollection->sortByDesc('fecha_hora'))->values();
        }

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

        // Paginate or show all appointments depending on parameters
        if (request()->has('ver_todo')) {
            // Show all future and projected appointments without limiting
            $visibleFutureSessions = $allFutureAndProjected;
        } else {
            // Default: paginate with 4 appointments per page (also when filter is applied)
            $perPage = 4;
            $page = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;

            $visibleFutureSessions = new \Illuminate\Pagination\LengthAwarePaginator(
                $allFutureAndProjected->forPage($page, $perPage)->values(),
                $allFutureAndProjected->count(),
                $perPage,
                $page,
                ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(), 'query' => request()->query()]
            );
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
            return view('dashboard.partials.appointments_table', [
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
            'fixedBlockedSlots',
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

        // Turnos de hoy (Upcoming only, excluyendo cancelados)
        $todayAppointments = Appointment::with(['user', 'payment'])
            ->whereDate('fecha_hora', \Carbon\Carbon::today())
            ->where('fecha_hora', '>=', now()) // Filter past appointments
            ->where(function($q) {
                $q->where('estado', '!=', 'cancelado')
                  ->orWhere('motivo_cancelacion', 'not like', '%Reserva fija%');
            })
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
            ->where('estado', '!=', 'cancelado')
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

        // Detectar si el paciente tiene / tuvo reserva fija
        $userId = Auth::id();
        $hasActiveFixed = Appointment::where('usuario_id', $userId)
            ->where('es_recurrente', true)
            ->where('fecha_hora', '>=', now())
            ->exists();

        $hadFixedCancelled = Appointment::where('usuario_id', $userId)
            ->where('motivo_cancelacion', 'like', '%Reserva fija cancelada por el paciente%')
            ->exists();

        $query = Appointment::where('usuario_id', $userId)
            ->with(['payment'])
            ->where(function($q) {
                $q->where('estado', '!=', 'cancelado')
                  ->orWhere('motivo_cancelacion', 'not like', '%Reserva fija%');
            });

        // REGLA EXTRA (para esta vista simple):
        // Si tiene reserva fija activa: mostrar solo desde que empieza la reserva hasta fin de año de esa reserva.
        if ($hasActiveFixed) {
            $baseReservation = Appointment::where('usuario_id', $userId)
                ->where('es_recurrente', true)
                ->orderBy('fecha_hora', 'asc')
                ->first();

            if ($baseReservation && $baseReservation->fecha_hora) {
                $start = $baseReservation->fecha_hora->copy()->startOfDay();
                $end = \Carbon\Carbon::create($start->year, 12, 31)->endOfDay();
                $query->whereBetween('fecha_hora', [$start, $end]);
            }
        }

        // Aplicar filtros y orden a la query resultante
        $query = $query
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
            // No usar turnos de recuperación para bloquear el pago del siguiente turno
            ->where(function($q) {
                $q->whereNull('es_recuperacion')
                  ->orWhere('es_recuperacion', false);
            })
            ->where(function($q) {
                $q->whereDoesntHave('payment')
                  ->orWhereHas('payment', function($pq) {
                      $pq->where('estado', '!=', 'verificado');
                  });
            })
            ->orderBy('fecha_hora', 'asc')
            ->value('id');

        $appointments = $query->paginate($perPage)->appends(request()->query());

        // Fetch Pending Recovery IDs
        $pendingRecoveryIds = \App\Models\Waitlist::where('usuario_id', Auth::id())
            ->whereNotNull('original_appointment_id')
            ->pluck('original_appointment_id')
            ->toArray();

        // Fetch Credit Balance
        $creditBalance = 0;
        $paciente = Auth::user()->paciente;
        if ($paciente) {
            $creditBalance = \App\Models\PatientCredit::where('paciente_id', $paciente->id)
                ->where('status', 'active')
                ->sum('amount');
        }

        if (request()->ajax()) {
            return view('dashboard.patient', [
                'appointments' => $appointments,
                'isMobile' => $isMobile,
                'isAjax' => true,
                'firstUnpaidId' => $firstUnpaidId,
                'creditBalance' => $creditBalance,
                'pendingRecoveryIds' => $pendingRecoveryIds
            ])->render();
        }

        return view('dashboard.patient', compact('appointments', 'isMobile', 'creditBalance', 'pendingRecoveryIds'))->with([
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
        
        // Appointments del mes seleccionado - MOSTRAR TODOS LOS TURNOS (incluyendo cancelados)
        // REGLA: Los turnos NUNCA se eliminan, todo queda visible y trazable
        $appointments = Appointment::with(['user', 'payment'])
            ->whereYear('fecha_hora', $year)
            ->whereMonth('fecha_hora', $month)
            ->where(function($q) {
                $q->where('estado', '!=', 'cancelado')
                  ->orWhere('motivo_cancelacion', 'not like', '%Reserva fija%');
            })
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

    /**
     * AJAX endpoint to fetch fresh day details for the admin agenda.
     */
    public function getAgendaDayDetails(Request $request)
    {
        $date = $request->input('date'); // YYYY-MM-DD
        
        if (!$date) return response()->json(['error' => 'No date provided'], 400);

        $appointments = Appointment::with(['user', 'payment'])
            ->whereDate('fecha_hora', $date)
            ->where(function($q) {
                $q->where('estado', '!=', 'cancelado')
                  ->orWhere('motivo_cancelacion', 'not like', '%Reserva fija%');
            })
            ->orderBy('fecha_hora', 'asc')
            ->get();

        // Project fixed sessions for this specific day if not in DB
        $dayStart = \Carbon\Carbon::parse($date)->startOfDay();
        $dayEnd = $dayStart->copy()->endOfDay();
        
        $fixedReservations = Appointment::where('es_recurrente', true)
            ->where('estado', '!=', 'cancelado')
            ->whereNotNull('fecha_hora')
            ->with('user')
            ->get()
            ->unique('usuario_id');

        $projected = collect();
        foreach($fixedReservations as $fixed) {
            $frecuencia = $fixed->frecuencia;
            $baseDate = $fixed->fecha_hora->copy();
            $intervalDays = ($frecuencia === 'quincenal') ? 14 : 7;
            
            $iterDate = $baseDate->copy();
            while ($iterDate->copy()->addDays($intervalDays)->lt($dayStart)) {
                $iterDate->addDays($intervalDays);
            }
            if ($iterDate->lt($dayStart)) {
                $iterDate->addDays($intervalDays);
            }

            if ($iterDate->gte($dayStart) && $iterDate->lte($dayEnd)) {
                $exists = $appointments->contains(function($a) use ($iterDate, $fixed) {
                    return $a->usuario_id == $fixed->usuario_id 
                        && $a->fecha_hora->format('Y-m-d H:i') == $iterDate->format('Y-m-d H:i');
                });

                if (!$exists) {
                    $virtual = new Appointment();
                    $virtual->id = - abs(crc32('proj_day_' . $fixed->usuario_id . '_' . $iterDate->timestamp));
                    $virtual->usuario_id = $fixed->usuario_id;
                    $virtual->fecha_hora = $iterDate->copy();
                    $virtual->estado = Appointment::ESTADO_CONFIRMADO;
                    $virtual->es_recurrente = true;
                    $virtual->is_projected = true;
                    $virtual->user = $fixed->user;
                    $projected->push($virtual);
                }
            }
        }

        $allApps = $appointments->concat($projected)->sortBy('fecha_hora')->values();

        // Fetch external events for the day
        $externalEvents = ExternalEvent::where(function($q) use ($dayStart, $dayEnd) {
                $q->whereBetween('start_time', [$dayStart, $dayEnd])
                  ->orWhereBetween('end_time', [$dayStart, $dayEnd]);
            })->orderBy('start_time', 'asc')->get();

        return response()->json([
            'date' => $date,
            'appointments' => $allApps->map(function($app) {
                // Determine display status based on rules
                $statusType = 'confirmado';
                if ($app->estado === 'cancelado') {
                    $statusType = $app->es_recurrente ? 'cancelado_fijo' : 'cancelado';
                }
                elseif ($app->es_recuperacion || $app->ui_status === 'recuperado') $statusType = 'recuperado';
                elseif ($app->es_recurrente) $statusType = 'fijo';
                elseif ($app->estado === 'ausente') $statusType = 'ausente';
                elseif ($app->estado === 'completado') $statusType = 'finalizado';

                return [
                    'id' => $app->id,
                    'time' => $app->fecha_hora->format('H:i'),
                    'user_name' => $app->user ? $app->user->nombre : 'Paciente Desconocido',
                    'estado' => $app->estado,
                    'is_projected' => $app->is_projected ?? false,
                    'status_type' => $statusType, 
                    'es_recurrente' => $app->es_recurrente,
                    'es_recuperacion' => $app->es_recuperacion || $app->ui_status === 'recuperado',
                ];
            }),
            'external_events' => $externalEvents->map(function($evt) {
                return [
                    'title' => $evt->title,
                    'start' => $evt->start_time->format('H:i'),
                    'end' => $evt->end_time->format('H:i'),
                ];
            })
        ]);
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
                $q->where('es_recurrente', true)
                  ->where('estado', '!=', 'cancelado')
                  ->orderBy('fecha_hora', 'desc')
                  ->limit(1);
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
