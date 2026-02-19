<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\GoogleCalendar\Event;
use Carbon\Carbon;

class PaymentController extends Controller
{
    public function create($appointment_id)
    {
        $appointment = Appointment::findOrFail($appointment_id);
        
        if ($appointment->usuario_id !== auth()->id()) {
            abort(403);
        }

        return view('payments.create', compact('appointment'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required|exists:turnos,id',
            'proof' => 'required|image|max:2048',
        ]);

        $appointment = Appointment::findOrFail($request->appointment_id);
        if ($appointment->usuario_id !== auth()->id()) {
            abort(403);
        }

        if ($request->hasFile('proof')) {
            // Generar nombre único con UUID
            $extension = $request->file('proof')->getClientOriginalExtension();
            $filename = \Illuminate\Support\Str::uuid() . '.' . $extension;
            
            // Guardar en disco 'local' (privado, no accesible vía URL pública)
            $path = $request->file('proof')->storeAs('pagos_privados', $filename, 'local');

            $payment = Payment::create([
                'turno_id' => $appointment->id,
                'comprobante_ruta' => $path,
                'estado' => 'pendiente',
            ]);

            // Notificar al Admin (Web + Mail)
            $admin = \App\Models\User::where('rol', 'admin')->first();
            if ($admin) {
                $admin->notify(new \App\Notifications\AdminNotification([
                    'title' => 'Nuevo Comprobante',
                    'mensaje' => 'Nuevo comprobante subido por ' . auth()->user()->nombre . ' para el ' . $appointment->fecha_hora->format('d/m H:i'),
                    'link' => route('admin.dashboard'),
                    'type' => 'pago'
                ]));
            }

            return redirect()->route('patient.dashboard')->with('success', 'Comprobante subido de forma segura. Esperando validación.');
        }

        return back()->with('error', 'Error al subir el archivo.');
    }

    public function showProof($id)
    {
        $payment = Payment::findOrFail($id);
        
        // Solo el Admin o el propio Paciente pueden verlo
        if (auth()->user()->rol !== 'admin' && auth()->id() !== $payment->appointment->usuario_id) {
            abort(403);
        }

        // Check local disk (private uploads via PaymentController)
        if (Storage::disk('local')->exists($payment->comprobante_ruta)) {
             return Storage::disk('local')->response($payment->comprobante_ruta);
        }

        // Check public disk (uploads via AppointmentController)
        if (Storage::disk('public')->exists($payment->comprobante_ruta)) {
             return Storage::disk('public')->response($payment->comprobante_ruta);
        }

        abort(404, 'Archivo no encontrado en el servidor.');
    }

    public function verify($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->update(['estado' => 'verificado', 'verificado_en' => now()]);
        
        $payment->appointment->update(['estado' => 'confirmado']); // Note: In a real scenario, we should also update monto_final here
        
        // [FINANCE] Lock price
        $honorario = 0;
        if ($payment->appointment->user && $payment->appointment->user->paciente) {
             // Use accessor to ensure fallbacks (custom price > base price)
             $honorario = $payment->appointment->user->paciente->precio_sesion;
        } else {
             // Fallback if no patient record (unlikely)
             $honorario = \App\Models\Setting::get('precio_base_sesion', 25000);
        }
        $payment->appointment->update([
            'estado' => 'confirmado',
            'monto_final' => $honorario
        ]);

        // Notificar al Paciente (Web + Mail)
        $payment->appointment->user->notify(new \App\Notifications\PatientNotification([
            'title' => 'Pago Verificado',
            'mensaje' => 'Tu pago ha sido verificado y tu turno para el ' . $payment->appointment->fecha_hora->format('d/m H:i') . ' está confirmado.',
            'link' => route('patient.dashboard'),
            'type' => 'success'
        ]));

        // Lógica de Google Calendar
        try {
            $appointment = $payment->appointment;
            $start = $appointment->fecha_hora;
            $end = $start->copy()->addHour(); // Duración de una hora por defecto

            $event = new Event;
            $event->name = 'Sesión: ' . $appointment->user->nombre . ' (' . ucfirst($appointment->modalidad) . ')';
            $event->startDateTime = $start;
            $event->endDateTime = $end;
            $event->description = "Sesión de psicología con " . $appointment->user->nombre . "\nModalidad: " . $appointment->modalidad;
            
            if ($appointment->modalidad === 'virtual') {
                // Link fijo de Nazarena (placeholder until provided)
                $meetLink = 'https://meet.google.com/xxx-yyyy-zzz'; 
                $event->location = $meetLink;
                // Actualizar el link en el turno para que se vea en el mail
                $appointment->update(['link_reunion' => $meetLink]);
            }

            $event->save();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error al crear evento en Google Calendar: " . $e->getMessage());
        }

        // Enviar mail de confirmación
        try {
            \Illuminate\Support\Facades\Mail::to($payment->appointment->user->email)->send(new \App\Mail\PaymentVerified(
                $payment->appointment->user->nombre,
                $payment->appointment->fecha_hora->format('d/m H:i'),
                $payment->appointment->link_reunion
            ));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error al enviar mail de pago verificado: " . $e->getMessage());
        }

        $this->logActivity('pago_verificado', 'Aprobó el pago del turno #' . $payment->turno_id . ' para ' . $payment->appointment->user->nombre, [
            'pago_id' => $payment->id,
            'turno_id' => $payment->turno_id,
            'paciente' => $payment->appointment->user->nombre
        ]);

        return back()->with('success', 'Pago verificado y turno confirmado.');
    }

    public function reject($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->update(['estado' => 'rechazado']);
        
        $payment->appointment->update(['estado' => 'pendiente']); // Vuelve a pendiente o cancelado? User said "rechazo y acepto".

        $this->logActivity('pago_rechazado', 'Rechazó el pago del turno #' . $payment->turno_id . ' para ' . $payment->appointment->user->nombre, [
            'pago_id' => $payment->id,
            'turno_id' => $payment->turno_id,
            'paciente' => $payment->appointment->user->nombre
        ]);

        // Notificar al Paciente
        \App\Models\Notification::create([
            'usuario_id' => $payment->appointment->usuario_id,
            'mensaje' => 'Tu comprobante de pago para el ' . $payment->appointment->fecha_hora->format('d/m H:i') . ' ha sido rechazado. Por favor subí uno válido.',
            'link' => route('patient.dashboard')
        ]);

        return back()->with('success', 'Pago rechazado.');
    }
}
