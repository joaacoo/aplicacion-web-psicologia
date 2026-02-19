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

        $waitlist = Waitlist::create([
            'usuario_id' => $user ? $user->id : null,
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
                ? "{$waitlist->name} solicita RECUPERAR una sesión para el " . \Carbon\Carbon::parse($waitlist->fecha_especifica)->format('d/m') . " a las {$waitlist->hora_inicio}."
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
