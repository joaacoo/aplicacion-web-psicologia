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
        
        $appt = Appointment::create([
            'usuario_id' => Auth::id(),
            'fecha_hora' => $appointmentDate,
            'modalidad' => $request->modalidad,
            'estado' => 'pendiente',
            'notas' => $request->notes,
            'vence_en' => $appointmentDate->copy()->subHours(20),
        ]);

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
            $admin->notify(new \App\Notifications\AdminNotification([
                'title' => 'Reserva de Turno',
                'mensaje' => 'Nuevo turno reservado por ' . Auth::user()->nombre . ' para el ' . $appointmentDate->format('d/m H:i'),
                'link' => route('admin.dashboard'),
                'type' => 'reserva'
            ]));
        }

        return redirect()->route('patient.dashboard')->with('success', 'Turno solicitado. Tu comprobante fue enviado y está en revisión.');
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

        return back()->with('success', 'Turno confirmado.');
    }

    public function cancel($id)
    {
        $appointment = Appointment::findOrFail($id);
        
        // Authorization check - admin or owner can cancel
        $this->authorize('cancel', $appointment);
        $appointment->update(['estado' => 'cancelado']);

        // Notificar al Paciente por DB
        \App\Models\Notification::create([
            'usuario_id' => $appointment->usuario_id,
            'mensaje' => 'Tu turno para el ' . $appointment->fecha_hora->format('d/m H:i') . ' ha sido cancelado.',
            'link' => route('patient.dashboard')
        ]);

        // Lógica de Lista de Espera: Buscar el primero de la lista
        $nextInLine = \App\Models\Waitlist::where(function($q) use ($appointment) {
            $q->where('fecha_especifica', $appointment->fecha_hora->toDateString())
              ->orWhere(function($sub) use ($appointment) {
                  $sub->where('dia_semana', $appointment->fecha_hora->dayOfWeek)
                      ->where('hora_inicio', $appointment->fecha_hora->format('H:i:s'));
              });
        })->orWhere(function($q) {
            // Also include people with no specific time preference
            $q->whereNull('fecha_especifica')->whereNull('dia_semana');
        })->orderBy('created_at', 'asc')->first();

        if ($nextInLine) {
            if ($nextInLine->usuario_id && $nextInLine->user) {
                // Notificar por DB
                \App\Models\Notification::create([
                    'usuario_id' => $nextInLine->usuario_id,
                    'mensaje' => '¡Buenas noticias! Se liberó el turno del ' . $appointment->fecha_hora->format('d/m H:i') . '. Podés reservarlo ahora.',
                    'link' => route('patient.dashboard')
                ]);

                try {
                    \Illuminate\Support\Facades\Mail::raw('Se liberó un turno el ' . $appointment->fecha_hora->format('d/m H:i') . ' que te interesaba. Entrá al portal rápido para reservarlo antes de que alguien más lo tome.', function($msg) use ($nextInLine) {
                        $msg->to($nextInLine->user->email)->subject('¡Turno disponible!');
                    });
                } catch (\Exception $e) {}
            }

            // Opcionalmente: Podríamos borrarlo de la lista para que no siga recibiendo avisos si no lo toma?
            // User dice "notificar automáticamente al primero". Si lo borramos y no lo toma, se pierde.
            // Mejor lo dejamos o lo borramos? "Notificar" implies a one-time thing.
            $nextInLine->delete(); 
        }

        $this->logActivity('turno_cancelado', 'Canceló el turno del ' . $appointment->fecha_hora->format('d/m H:i') . ' para ' . $appointment->user->nombre, [
            'turno_id' => $appointment->id,
            'paciente' => $appointment->user->nombre
        ]);

        return back()->with('success', 'Turno cancelado y lista de espera notificada.');
    }
}
