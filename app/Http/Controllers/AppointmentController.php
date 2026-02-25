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
        
        // [RESTRICTION] One ACTIVE appointment per day per patient
        $existingAppt = Appointment::where('usuario_id', Auth::id())
            ->whereDate('fecha_hora', $appointmentDate->toDateString())
            // Solo cuentan turnos realmente activos (pendiente/confirmado), no cancelados ni recuperaciones pasadas
            ->whereIn('estado', ['pendiente', 'confirmado'])
            ->exists();

        if ($existingAppt) {
            return redirect()->back()->withErrors(['appointment_date' => 'Ya tenés un turno reservado para este día. Si necesitás cambiarlo, cancelá el anterior primero.']);
        }
        
        // Fetch patient specific data for links
        $patient = \App\Models\Paciente::where('user_id', Auth::id())->first();
        $meetLink = $patient ? $patient->meet_link : null;

        // Determine iterations and step based on frequency
        // Always cover ~1 month:
        //   semanal  → 4 iterations × 1 week  = 4 weeks
        //   quincenal → 2 iterations × 2 weeks = 4 weeks
        //   eventual  → 1 iteration
        $iterations = 1;
        if ($request->frecuencia === 'quincenal') {
            $iterations = 2;
        } elseif ($request->frecuencia !== 'eventual') {
            $iterations = 4; // semanal
        }

        $createdAppointments = [];
        $currentDate = $appointmentDate->copy();

        for ($i = 0; $i < $iterations; $i++) {
            // Check for conflict on this specific date (solo turnos activos)
            $exists = Appointment::where('usuario_id', Auth::id())
                ->whereDate('fecha_hora', $currentDate->toDateString())
                ->whereIn('estado', ['pendiente', 'confirmado'])
                ->exists();

            if (!$exists) {
                $appt = Appointment::create([
                    'usuario_id' => Auth::id(),
                    'fecha_hora' => $currentDate->copy(),
                    'modalidad' => $request->modalidad,
                    'frecuencia' => $request->frecuencia,
                    'estado' => 'pendiente',
                    'es_recurrente' => ($request->frecuencia !== 'eventual'),
                    'notas' => $request->notes,
                    'vence_en' => $currentDate->copy()->subHours(20),
                    'meet_link' => $meetLink,
                    'link_reunion' => $meetLink,
                ]);
                $createdAppointments[] = $appt;
            }

            // Advance to next slot based on frequency
            if ($request->frecuencia === 'quincenal') {
                $currentDate->addWeeks(2);
            } elseif ($request->frecuencia !== 'eventual') {
                $currentDate->addWeek();
            }
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
                'title' => 'Nuevo Comprobante Recibido',
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

        $authUserId = auth()->id();
        $isAdmin = auth()->user() && auth()->user()->rol === 'admin';
        $ahora = now();
        $isCriticalZone = $appointment->isInCriticalZone();
        $isPaid = $appointment->payment && $appointment->payment->estado === 'verificado';

        // ═══════════════════════════════════════════════════════════
        // TABLA DE VERDAD DE CANCELACIÓN
        // ═══════════════════════════════════════════════════════════
        // Cancelación	Pagó	¿Puede recuperar?	¿Debe pagar?
        // > 24 hs	    No	    ✅ Sí	            ❌ No
        // > 24 hs	    Yes	✅ Sí	            ❌ No
        // ≤ 24 hs	    No	    ❌ No	            ✅ Sí (bloqueado)
        // ≤ 24 hs	    Yes	✅ Sí	            ❌ No

        // Si es Admin, la cancelación es total y gratuita SIEMPRE.
        if ($isAdmin) {
            $appointment->update([
                'estado' => 'cancelado',
                'debe_pagarse' => false,
                'cancelado_con_mas_de_24hs' => true
            ]);
            $msg = 'Turno cancelado por la profesional.';
            // Si ya estaba pago, generamos crédito (porque la psicólogos canceló, el paciente no pierde su dinero)
            if ($isPaid) {
                if (!$appointment->user->paciente->credits()->where('status', 'active')->exists()) {
                    \App\Models\PatientCredit::create([
                        'paciente_id' => $appointment->user->paciente->id,
                        'appointment_id' => $appointment->id,
                        'amount' => $appointment->monto_final,
                        'reason' => 'Crédito por cancelación de la profesional (' . $appointment->fecha_hora->format('d/m H:i') . ')',
                        'status' => 'active'
                    ]);
                    $msg .= ' Se generó crédito para la próxima sesión del paciente.';
                } else {
                    $msg .= ' El paciente ya cuenta con un crédito activo.';
                }
            }
        } elseif ($isCriticalZone) {
            // ZONA CRÍTICA: ≤ 24hs
            // Si no pagó -> debe pagarse (bloqueado), NO puede recuperar
            // Si pagó -> puede recuperar, NO debe pagarse
            $appointment->update([
                'estado' => 'cancelado',
                'debe_pagarse' => !$isPaid, // Debe pagar si NO está pagado
                'cancelado_con_mas_de_24hs' => false,
                'motivo_cancelacion' => 'Cancelación en zona crítica (< 24hs).'
            ]);
            
            if ($isPaid) {
                // Generar crédito por cancelación dentro de las 24hs si ya pagó, y no tiene otro activo
                if (!$appointment->user->paciente->credits()->where('status', 'active')->exists()) {
                    \App\Models\PatientCredit::create([
                        'paciente_id' => $appointment->user->paciente->id,
                        'appointment_id' => $appointment->id,
                        'amount' => $appointment->monto_final,
                        'reason' => 'Crédito por cancelación < 24hs del turno ' . $appointment->fecha_hora->format('d/m H:i'),
                        'status' => 'active'
                    ]);
                    $msg = 'Turno cancelado. Puedes recuperar este turno y se generó un crédito para tu próxima sesión.';
                } else {
                    $msg = 'Turno cancelado. Puedes recuperar este turno. Ya cuentas con un crédito activo en tu cuenta.';
                }
            } else {
                // Sesión perdida - debe pagar
                $msg = 'Sesión perdida. No se puede recuperar este turno hasta realizar el pago.';
            }
        } else {
            // ZONA NORMAL: > 24hs
            // Siempre puede recuperar, nunca debe pagar
            $appointment->update([
                'estado' => 'cancelado',
                'debe_pagarse' => false,
                'cancelado_con_mas_de_24hs' => true,
                'motivo_cancelacion' => 'Cancelación con más de 24hs de anticipación.'
            ]);

            if ($isPaid) {
                if (!$appointment->user->paciente->credits()->where('status', 'active')->exists()) {
                    \App\Models\PatientCredit::create([
                        'paciente_id' => $appointment->user->paciente->id,
                        'appointment_id' => $appointment->id,
                        'amount' => $appointment->monto_final,
                        'reason' => 'Crédito por cancelación > 24hs del turno ' . $appointment->fecha_hora->format('d/m H:i'),
                        'status' => 'active'
                    ]);
                    $msg = 'Turno cancelado y crédito generado para tu próxima sesión.';
                } else {
                    $msg = 'Turno cancelado. Ya contás con un crédito activo en tu cuenta.';
                }
            } else {
                $msg = 'Turno cancelado correctamente. Puedes recuperar este turno.';
            }

            // ═══════════════════════════════════════════════════════════
            // CREAR ENTRADA EN WAITLIST PARA RECUPERACIÓN
            // ═══════════════════════════════════════════════════════════
            // Solo si: > 24hs y (no pagó O pagó)
            // No crear waitlist si: ≤ 24hs y no pagó
            \App\Models\Waitlist::create([
                'usuario_id' => $appointment->usuario_id,
                'name' => $appointment->user->nombre,
                'phone' => $appointment->user->paciente->telefono ?? '',
                'email' => $appointment->user->email,
                'availability' => json_encode([
                    'modalidad' => $appointment->modalidad,
                    'hora_preferida' => $appointment->fecha_hora->format('H:i'),
                    'dias_preferidos' => [$appointment->fecha_hora->dayOfWeek]
                ]),
                'modality' => $appointment->modalidad,
                'original_appointment_id' => $appointment->id,
                'dia_semana' => $appointment->fecha_hora->dayOfWeek,
                'hora_inicio' => $appointment->fecha_hora->format('H:i:s'),
            ]);
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

    public function cancelProjected(Request $request)
    {
        $request->validate([
            'fecha_hora' => 'required',
            'usuario_id' => 'sometimes|exists:users,id'
        ]);

        $isAdmin = auth()->user() && auth()->user()->rol === 'admin';
        $userId = $isAdmin && $request->has('usuario_id') ? $request->usuario_id : auth()->id();
        $date = \Carbon\Carbon::parse($request->fecha_hora);

        // Validar zona crítica SOLO para pacientes
        if (!$isAdmin) {
            $isCriticalZone = $date->diffInHours(now()) < 24 && $date->isFuture();
            if ($isCriticalZone) {
                return back()->with('error', 'No puedes cancelar una proyección con menos de 24 horas de anticipación. Debes abonar y cancelar o esperar a que se genere la sesión.');
            }
        }

        // Buscar reserva fija base
        $baseReservation = Appointment::where('usuario_id', $userId)
                                    ->where('es_recurrente', true)
                                    ->first();

        if (!$baseReservation) {
            return back()->with('error', 'No se encontró la reserva fija.');
        }

        // Crear el turno directamente como cancelado
        Appointment::create([
            'usuario_id' => $userId,
            'fecha_hora' => $date,
            'modalidad' => $baseReservation->modalidad,
            'monto_final' => $baseReservation->monto_final,
            'estado' => 'cancelado',
            'motivo_cancelacion' => $isAdmin ? 'Turno proyectado cancelado por la profesional (Reserva fija).' : 'Turno proyectado cancelado por el paciente (Reserva fija).',
            'es_recurrente' => true,
            'frecuencia' => $baseReservation->frecuencia,
            'debe_pagarse' => false, // Siempre false si es proyección o admin cancela
            'cancelado_con_mas_de_24hs' => true,
        ]);

        return back()->with('success', 'Turno cancelado correctamente.');
    }

    public function cancelFixedReservation()
    {
        $user = auth()->user();
        if (!$user) return back()->with('error', 'No autorizado.');

        // 1. Buscar todos los turnos futuros marcados como recurrentes
        $futureRecurring = Appointment::where('usuario_id', $user->id)
                                    ->where('es_recurrente', true)
                                    ->where('fecha_hora', '>=', now())
                                    ->get();

        if ($futureRecurring->isEmpty()) {
            return back()->with('error', 'No se encontraron turnos de reserva fija activa en el futuro.');
        }

        $count = $futureRecurring->count();
        $creditsGenerated = 0;

        foreach ($futureRecurring as $appt) {
            $isPaid = $appt->paymentWasVerified();

            // Marcar como cancelado y NO recurrente
            $appt->update([
                'estado' => 'cancelado',
                'es_recurrente' => false,
                'debe_pagarse' => false,
                'motivo_cancelacion' => 'Reserva fija cancelada por el paciente.'
            ]);

            // Si estaba pago, generar crédito
            if ($isPaid) {
                \App\Models\PatientCredit::create([
                    'paciente_id' => $user->paciente->id,
                    'appointment_id' => $appt->id,
                    'amount' => $appt->monto_final,
                    'reason' => 'Crédito por cancelación de reserva fija (' . $appt->fecha_hora->format('d/m H:i') . ')',
                    'status' => 'active'
                ]);
                $creditsGenerated++;
            }
        }

        $this->logActivity('reserva_fija_cancelada', "Canceló su reserva fija. Se cancelaron {$count} turnos futuros.", [
            'paciente' => $user->nombre,
            'turnos_cancelados' => $count,
            'creditos_generados' => $creditsGenerated
        ]);

        // Notificar a la Admin (Nazarena)
        $admin = \App\Models\User::where('rol', 'admin')->first();
        if ($admin) {
            $admin->notify(new \App\Notifications\AdminNotification([
                'title' => 'Reserva Fija Finalizada',
                'mensaje' => "El paciente {$user->nombre} ha cancelado su reserva fija. Se liberaron {$count} turnos de su agenda.",
                'link' => route('admin.agenda'),
                'type' => 'cancelacion_paciente'
            ]));
        }

        $successMsg = "Tu reserva fija ha sido cancelada. Se han cancelado {$count} turnos futuros" . ($creditsGenerated > 0 ? " y se generaron {$creditsGenerated} créditos para tus próximas sesiones." : ".");

        return back()->with('success', $successMsg);
    }

    /**
     * Store a recovered appointment created by the admin from the waitlist.
     */
    public function storeRecovery(Request $request)
    {
        $request->validate([
            'waitlist_id' => 'required|exists:waitlists,id',
            'fecha' => 'required|date',
            'hora' => 'required',
            'modalidad' => 'required|in:virtual,presencial',
        ]);

        $waitlist = \App\Models\Waitlist::with('user')->findOrFail($request->waitlist_id);
        $pacienteId = $waitlist->user->paciente->id ?? null;
        
        // Find if there's an active credit
        $tieneCredito = false;
        if ($pacienteId) {
            $creditQuery = \App\Models\PatientCredit::where('paciente_id', $pacienteId)
                ->where('status', 'active');
                
            $credit = (clone $creditQuery)->where('appointment_id', $waitlist->original_appointment_id)->first() ?? $creditQuery->orderBy('created_at', 'asc')->first();
            $tieneCredito = $credit ? true : false;
        }

        $precio = 0;
        if (!$tieneCredito) {
            $precio = $waitlist->user->paciente->honorario_pactado ?? \App\Models\Setting::get('precio_base_sesion', 20000);
            if (!$precio) $precio = \App\Models\Setting::get('precio_base_sesion', 20000);
        }

        $appointment = Appointment::create([
            'usuario_id' => $waitlist->usuario_id,
            'paciente_id' => $pacienteId,
            'fecha_hora' => $fecha_hora,
            'modalidad' => $request->modalidad,
            'estado' => Appointment::ESTADO_CONFIRMADO,
            'ui_status' => 'recuperado',
            'es_recurrente' => false,
            'es_recuperacion' => false, // To allow normal payment flow if no credit
            'waitlist_id' => $waitlist->id,
            'notas' => 'Asignado por la Lic.',
            'frecuencia' => 'eventual',
            'debe_pagarse' => !$tieneCredito,
            'monto_final' => $precio,
        ]);


        // Eliminar de la lista de espera
        $waitlist->delete();

        // Notificar al paciente
        if ($appointment->user) {
            $isRecovery = !empty($waitlist->original_appointment_id);
            
            $appMsg = $isRecovery 
                ? 'La licenciada te asignó un turno de recuperación desde la lista de espera.'
                : 'La licenciada te asignó un turno disponible desde la lista de espera.';
                
            $mailMsg = $isRecovery
                ? 'Te asignaron un turno de recuperación disponible. Ingresá para verlo, confirmarlo o gestionarlo.'
                : 'Te asignaron un turno disponible. Ingresá para verlo, confirmarlo o gestionarlo.';

            $appointment->user->notify(new \App\Notifications\PatientNotification([
                'title' => $isRecovery ? 'Turno de Recuperación Agendado' : 'Turno Asignado',
                'mensaje' => $appMsg,
                'email_mensaje' => $mailMsg,
                'link' => route('patient.dashboard'),
                'type' => 'success'
            ]));
        }

        // Registrar actividad
        $this->logActivity('turno_recuperado', 'Agendó turno de recuperación para ' . $waitlist->name . ' el ' . $fecha_hora->format('d/m H:i'), [
            'turno_id' => $appointment->id,
            'paciente' => $waitlist->name
        ]);

        return redirect()->route('admin.waitlist')->with('success', 'Turno de recuperación agendado correctamente.');
    }

}
