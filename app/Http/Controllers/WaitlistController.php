<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Waitlist;

class WaitlistController extends Controller
{
    public function create()
    {
        return view('waitlist');
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => $user ? 'nullable' : 'required|string|max:255',
            'phone' => $user ? 'nullable' : 'required|string|max:255',
            'availability' => 'nullable|string',
            'modality' => 'nullable|string',
        ]);

        // Validación para solicitudes de recuperación
        if ($request->boolean('is_recovery')) {
            $originalAppointmentId = $request->original_appointment_id ?? $request->appointment_id;
            if (!$originalAppointmentId) {
                abort(403, 'ID de turno original requerido para recuperación.');
            }

            $appointment = \App\Models\Appointment::findOrFail($originalAppointmentId);

            // Verificar que el turno pertenezca al usuario
            if ($appointment->usuario_id !== $user->id) {
                abort(403, 'No tienes permiso para recuperar este turno.');
            }

            // Verificar que esté cancelado
            if ($appointment->estado !== 'cancelado') {
                abort(403, 'Solo se pueden recuperar turnos cancelados.');
            }

            // Verificar que no se haya solicitado ya la recuperación
            if ($appointment->recovery_requested_at) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['error' => 'Ya has solicitado la recuperación de este turno.'], 403);
                }
                return back()->with('error', 'Ya has solicitado la recuperación de este turno.');
            }

            // Aplicar reglas de recuperación
            $canRecover = !$appointment->isInCriticalZone() || ($appointment->payment && $appointment->payment->estado == 'verificado');
            if (!$canRecover) {
                abort(403, 'No cumples con las condiciones para recuperar este turno.');
            }

            // Marcar como solicitado
            $appointment->update(['recovery_requested_at' => now()]);
        }

        $waitlist = Waitlist::create([
            'usuario_id' => $user ? $user->id : null,
            'original_appointment_id' => $request->original_appointment_id ?? $request->appointment_id,
            'name' => $request->name ?? ($user ? $user->nombre . ' ' . $user->apellido : 'Guest'),
            'phone' => $request->phone ?? ($user && $user->telefono ? $user->telefono : 'N/A'),
            'availability' => $request->availability ?? 'Horario Específico',
            'modality' => $request->modality ?? 'Cualquiera',
            'fecha_especifica' => $request->fecha_especifica,
            'hora_inicio' => $request->hora_inicio,
            'dia_semana' => $request->fecha_especifica ? \Carbon\Carbon::parse($request->fecha_especifica)->dayOfWeek : null,
        ]);

        // Notificar al Admin
        $admin = \App\Models\User::where('rol', 'admin')->first();
        if ($admin) {
            $isRecovery = $request->boolean('is_recovery');
            $title = $isRecovery ? 'Solicitud de RECUPERACIÓN' : 'Nueva Lista de Espera';
            $mensaje = $isRecovery 
                ? "{$waitlist->name} solicita RECUPERAR su sesión."
                : "{$waitlist->name} se unió a la lista de espera.";

            $admin->notify(new \App\Notifications\AdminNotification([
                'title' => $title,
                'mensaje' => $mensaje,
                'link' => route('admin.waitlist'),
                'type' => $isRecovery ? 'recuperación' : 'waitlist'
            ]));
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => '¡Te uniste a la lista de espera! Te avisaremos cuando haya un lugar.']);
        }

        // check if user is auth to redirect to dashboard, else redirect to home/login with message
        if (auth()->check()) {
            return redirect()->route('dashboard')->with('success', '¡Te uniste a la lista de espera! Te avisaremos cuando haya un lugar.');
        }

        return redirect()->route('login')->with('success', '¡Te uniste a la lista de espera! Te contactaremos pronto.');
    }
    public function destroy($id)
    {
        $item = Waitlist::findOrFail($id);
        $item->delete();
        return back()->with('success', 'Paciente eliminado de la lista de espera.');
    }
}
