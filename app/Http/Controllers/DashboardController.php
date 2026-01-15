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

        // Expandir rangos en slots de 1 hora (45 min sesión + 15 min gap)
        $googleAvailableSlots = collect();
        foreach ($googleRawEvents as $event) {
            $current = $event->start_time->copy();
            // Mientras haya al menos 45 minutos disponibles
            while ($current->copy()->addMinutes(45)->lte($event->end_time)) {
                $googleAvailableSlots->push([
                    'start_time' => $current->copy(),
                    'end_time' => $current->copy()->addMinutes(45),
                ]);
                // Saltamos 1 hora para el siguiente slot
                $current->addHour();
            }
        }

        // Eventos que representan BLOQUEOS (Cualquier cosa que NO sea Disponibilidad)
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
}
