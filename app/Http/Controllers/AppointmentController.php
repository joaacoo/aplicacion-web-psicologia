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

        // Notificar al Admin
        $admin = \App\Models\User::where('rol', 'admin')->first();
        if ($admin) {
            \App\Models\Notification::create([
                'usuario_id' => $admin->id,
                'mensaje' => 'Nuevo turno reservado por ' . Auth::user()->nombre,
                'link' => route('admin.dashboard')
            ]);
        }

        return redirect()->route('patient.dashboard')->with('success', 'Turno solicitado. Por favor subí tu comprobante de pago para confirmarlo.');
    }

    public function confirm($id)
    {
        $appointment = Appointment::findOrFail($id);
        
        // [FINANCE] Lock price at confirmation to preserve history
        $honorario = 0;
        if ($appointment->user && $appointment->user->paciente) {
            $honorario = $appointment->user->paciente->honorario_pactado;
        }

        $appointment->update([
            'estado' => 'confirmado',
            'monto_final' => $honorario
        ]);

        // Notificar al Paciente
        \App\Models\Notification::create([
            'usuario_id' => $appointment->usuario_id,
            'mensaje' => 'Tu turno para el ' . $appointment->fecha_hora->format('d/m H:i') . ' ha sido CONFIRMADO.',
            'link' => route('patient.dashboard')
        ]);

        $this->logActivity('turno_confirmado', 'Confirmó manualmente el turno del ' . $appointment->fecha_hora->format('d/m H:i') . ' para ' . $appointment->user->nombre, [
            'turno_id' => $appointment->id,
            'paciente' => $appointment->user->nombre
        ]);

        return back()->with('success', 'Turno confirmado.');
    }

    public function cancel($id)
    {
        $appointment = Appointment::findOrFail($id);
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
