<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function index()
    {
        return redirect()->route('admin.dashboard');
    }

    public function store(Request $request)
    {
        $request->validate([
            'appointment_date' => 'required|date|after:now',
            'notes' => 'nullable|string',
            'modalidad' => 'required|in:virtual,presencial',
            'proof' => Auth::user()->tipo_paciente === 'nuevo' ? 'required|file|mimes:jpg,jpeg,png,pdf|max:10240' : 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240', // 10MB limit
        ]);

        \Illuminate\Support\Facades\Log::info('Appointment Request:', $request->all());

        $appointmentDate = \Carbon\Carbon::parse($request->appointment_date);
        
        // [RESTRICTION] One appointment per day per patient
        $existingAppt = Appointment::where('usuario_id', Auth::id())
            ->whereDate('fecha_hora', $appointmentDate->toDateString())
            ->where('estado', '!=', 'cancelado')
            ->exists();

        if ($existingAppt) {
            return redirect()->back()->withErrors(['appointment_date' => 'Ya tenés un turno reservado para este día. Si necesitás cambiarlo, cancelá el anterior primero.']);
        }
        
        // Fetch patient specific data for links
        $patient = \App\Models\Paciente::where('user_id', Auth::id())->first();
        $meetLink = $patient ? $patient->meet_link : null;

        // Determine how many appointments to create
        // If frequency is weekly (default) and it's a fixed reservation (new logic implied by user request)
        // We will create appointments for the next 12 months (approx 52 weeks)
        
        $iterations = 1;
        if ($request->frecuencia !== 'eventual') {
            $iterations = 52; // 1 year of weekly appointments
        }

        $createdAppointments = [];
        $currentDate = $appointmentDate->copy();

        for ($i = 0; $i < $iterations; $i++) {
            // Check for conflict on this specific date
            $exists = Appointment::where('usuario_id', Auth::id())
                ->whereDate('fecha_hora', $currentDate->toDateString())
                ->where('estado', '!=', 'cancelado')
                ->exists();

            if (!$exists) {
                $appt = Appointment::create([
                    'usuario_id' => Auth::id(),
                    'fecha_hora' => $currentDate->copy(),
                    'modalidad' => $request->modalidad,
                    'frecuencia' => $request->frecuencia,
                    'estado' => 'confirmed', // Fixed reservations are confirmed by default? Or pending? User said "reserva", usually implies confirmed if it blocks the agenda. Let's stick to 'pendiente' or 'confirmado' based on payment? 
                                             // Actually, for fixed reservations, they usually start confirmed or pending approval. 
                                             // The original code was 'pendiente'. I'll keep 'pendiente' for the first one, but maybe 'confirmado' for the rest? 
                                             // User said "una vez que reserva... es para todos los días". This implies they are BLOCKED. 
                                             // So I will set them as 'pendiente' (waiting for admin approval/payment) BUT they exist in DB so they block the slot.
                                             // Wait, previous code had 'pendiente'.
                    'estado' => 'pendiente', 
                    'es_recurrente' => true, 
                    'notas' => $request->notes,
                    'vence_en' => $currentDate->copy()->subHours(20),
                    'meet_link' => $meetLink, // Assign patient's meet link
                    'link_reunion' => $meetLink, // Assign to both widely used columns just in case
                ]);
                $createdAppointments[] = $appt;
            }
            
            // Move to next week
            $currentDate->addWeek();
        }

        // Use the FIRST created appointment for the payment proof logic attachment
        $appt = $createdAppointments[0] ?? null;

        if (!$appt) {
             return redirect()->back()->withErrors(['appointment_date' => 'No se pudieron crear los turnos. Posiblemente ya existan reservas en esas fechas.']);
        }

        if ($request->hasFile('proof')) {
            $path = $request->file('proof')->store('pagos', 'public');
            \App\Models\Payment::create([
                'turno_id' => $appt->id,
                'comprobante_ruta' => $path,
                'estado' => 'pendiente',
            ]);
        }

        // Notificar al Admin (Web + Mail)
        $admin = \App\Models\User::where('rol', 'admin')->first();
        if ($admin) {
            $hasProof = $request->hasFile('proof') ? ' [Comprobante Adjunto]' : '';
            $admin->notify(new \App\Notifications\AdminNotification([
                'title' => 'Reserva de Turno' . ($request->hasFile('proof') ? ' + Pago' : ''),
                'mensaje' => 'Nuevo turno reservado por ' . Auth::user()->nombre . ' para el ' . $appointmentDate->format('d/m H:i') . 
                            ' (' . ucfirst($request->frecuencia ?? 'semanal') . ') ' . 
                            ($appt->es_recurrente ? '[Reserva Fija]' : '[Turno Eventual]') . $hasProof,
                'link' => $request->hasFile('proof') ? route('admin.finanzas') . '#honorarios' : route('admin.agenda', ['selected_date' => $appointmentDate->toDateString()]),
                'type' => 'reserva'
            ]));
        }

        return redirect()->route('patient.dashboard')->with('success', 'Turno solicitado. Tu comprobante fue enviado y está en revisión.');
    }

    /**
     * Handle payment proof upload for an existing appointment.
     */
    public function uploadProof(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required|exists:turnos,id',
            'proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ]);

        $appt = Appointment::findOrFail($request->appointment_id);

        // Security check
        if ($appt->usuario_id !== Auth::id()) {
            abort(403);
        }

        $path = $request->file('proof')->store('pagos', 'public');

        // Create or Update Payment
        $appt->payment()->updateOrCreate(
            ['turno_id' => $appt->id],
            [
                'comprobante_ruta' => $path,
                'estado' => 'pendiente',
            ]
        );

        // Notify Admin
        $admin = \App\Models\User::where('rol', 'admin')->first();
        if ($admin) {
            $admin->notify(new \App\Notifications\AdminNotification([
                'title' => '🔥 Nuevo Comprobante Recibido',
                'mensaje' => 'El paciente ' . Auth::user()->nombre . ' subió un comprobante de pago para la sesión del ' . $appt->fecha_hora->format('d/m H:i') . '. Por favor, verificalo en la sección de Honorarios.',
                'link' => route('admin.finanzas') . '#honorarios',
                'type' => 'pago'
            ]));
        }

        return back()->with('success', 'Comprobante subido con éxito. El pago está siendo verificado.');
    }

    public function confirm($id)
    {
        $appointment = Appointment::findOrFail($id);
        
        // Authorization check - only admin can confirm
        $this->authorize('confirm', $appointment);
        
        // [FINANCE] Lock price at confirmation to preserve history
        $honorario = 0;
        if ($appointment->user && $appointment->user->paciente) {
            // Use the accessor to handle custom price or fallback to base price
            $honorario = $appointment->user->paciente->precio_sesion;
        } else {
            // Final fallback if no model exists for some reason
            $honorario = \App\Models\Setting::get('precio_base_sesion', 25000);
        }

        $appointment->update([
            'estado' => 'confirmado',
            'monto_final' => $honorario
        ]);

        // [FIX] Update payment status to 'verificado' so it counts in monthly income
        if ($appointment->payment) {
            $appointment->payment->update(['estado' => 'verificado']);
        }

        // Notificar al Paciente (Web + Mail)
        $appointment->user->notify(new \App\Notifications\PatientNotification([
            'title' => 'Turno Confirmado',
            'mensaje' => 'Tu turno para el ' . $appointment->fecha_hora->format('d/m H:i') . ' ha sido CONFIRMADO.',
            'link' => route('patient.dashboard'),
            'type' => 'success'
        ]));

        $this->logActivity('turno_confirmado', 'Confirmó manualmente el turno del ' . $appointment->fecha_hora->format('d/m H:i') . ' para ' . $appointment->user->nombre, [
            'turno_id' => $appointment->id,
            'paciente' => $appointment->user->nombre
        ]);

        if (str_contains(url()->previous(), 'finanzas')) {
            return redirect()->route('admin.finanzas')->withFragment('honorarios')->with('success', 'Turno confirmado.');
        }
        return back()->with('success', 'Turno confirmado.');
    }

    public function cancel($id)
    {
        $appointment = Appointment::with('user.paciente')->findOrFail($id);
        
        // Authorization check - admin or owner can cancel
        $this->authorize('cancel', $appointment);

        $ahora = now();
        $isCriticalZone = $appointment->isInCriticalZone();
        $isPaid = $appointment->payment && $appointment->payment->estado === 'verificado';

        if ($isCriticalZone) {
            // Lógica < 24hs: Sesión Perdida, no se reintegra
            $appointment->update([
                'estado' => Appointment::ESTADO_SESION_PERDIDA,
                'debe_pagarse' => true,
                'motivo_cancelacion' => 'Cancelación en zona crítica (< 24hs).'
            ]);
            $msg = 'Sesión marcada como perdida por política de 24hs.';
        } else {
            // Lógica > 24hs: Cancelación normal, generar crédito si estaba paga
            $appointment->update([
                'estado' => 'cancelado',
                'debe_pagarse' => false
            ]);

            if ($isPaid) {
                \App\Models\PatientCredit::create([
                    'paciente_id' => $appointment->user->paciente->id,
                    'appointment_id' => $appointment->id,
                    'amount' => $appointment->monto_final,
                    'reason' => 'Crédito por cancelación > 24hs del turno ' . $appointment->fecha_hora->format('d/m H:i'),
                    'status' => 'active'
                ]);
                $msg = 'Turno cancelado y crédito generado por sesión abonada.';
            } else {
                $msg = 'Turno cancelado correctamente.';
            }
        }

        // Notificar al Paciente (Mail + DB)
        if ($appointment->user) {
            $appointment->user->notify(new \App\Notifications\PatientNotification([
                'title' => $isCriticalZone ? 'Sesión Perdida' : 'Turno Cancelado',
                'mensaje' => $isCriticalZone 
                    ? 'Tu turno para el ' . $appointment->fecha_hora->format('d/m H:i') . ' se marcó como Sesión Perdida por cancelación tardía.'
                    : 'Tu turno para el ' . $appointment->fecha_hora->format('d/m H:i') . ' ha sido cancelado.',
                'link' => route('patient.dashboard'),
                'type' => $isCriticalZone ? 'error' : 'cancelado'
            ]));

            // Notificar a la Admin (Nazarena) si fue cancelado por el paciente
            if (auth()->id() === $appointment->usuario_id) {
                $admin = \App\Models\User::where('rol', 'admin')->first();
                if ($admin) {
                    $admin->notify(new \App\Notifications\AdminNotification([
                        'title' => $isCriticalZone ? 'Sesión Perdida por Paciente' : 'Turno Cancelado por Paciente',
                        'mensaje' => 'El paciente ' . $appointment->user->nombre . ($isCriticalZone ? ' perdió su sesión del ' : ' canceló su turno del ') . $appointment->fecha_hora->format('d/m H:i') . '.',
                        'link' => route('admin.agenda'), 
                        'type' => 'cancelacion_paciente'
                    ]));
                }
            }
        }

        // Lógica de Lista de Espera: Solo si no fue sesión perdida (porque si es perdida, el admin podría querer cobrarla igual o no liberarla tan rápido, pero la política dice "liberar cupo" en el ultimatum, aquí el usuario la canceló él mismo)
        // El usuario dijo: "Si faltan < 24hs: Cambiar el estado visual a 'Sesión Perdida'. El botón de cancelar debe avisar que 'No se reintegra el valor'".
        // No dijo explícitamente si liberar el cupo. Normalmente una sesión perdida LIBERA el cupo para que otro la tome.
        
        $nextInLine = \App\Models\Waitlist::where(function($q) use ($appointment) {
            $q->where('fecha_especifica', $appointment->fecha_hora->toDateString())
              ->orWhere(function($sub) use ($appointment) {
                  $sub->where('dia_semana', $appointment->fecha_hora->dayOfWeek)
                      ->where('hora_inicio', $appointment->fecha_hora->format('H:i:s'));
              });
        })->orWhere(function($q) {
            $q->whereNull('fecha_especifica')->whereNull('dia_semana');
        })->orderBy('created_at', 'asc')->first();

        if ($nextInLine) {
            if ($nextInLine->usuario_id && $nextInLine->user) {
                $nextInLine->user->notify(new \App\Notifications\PatientNotification([
                    'title' => '¡Turno Disponible!',
                    'mensaje' => '¡Buenas noticias! Se liberó el turno del ' . $appointment->fecha_hora->format('d/m H:i') . '. Podés reservarlo ahora.',
                    'link' => route('patient.dashboard'),
                    'type' => 'turno_disponible'
                ]));
            }
            $nextInLine->delete(); 
        }

        $this->logActivity('turno_cancelado', 'Canceló ' . ($isCriticalZone ? 'tardíamente ' : '') . 'el turno del ' . $appointment->fecha_hora->format('d/m H:i') . ' para ' . $appointment->user->nombre, [
            'turno_id' => $appointment->id,
            'paciente' => $appointment->user->nombre
        ]);

        if (str_contains(url()->previous(), 'finanzas')) {
            return redirect()->route('admin.finanzas')->withFragment('honorarios')->with('success', $msg);
        }
        return back()->with('success', $msg);
    }
}
