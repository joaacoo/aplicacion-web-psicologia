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
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'availability' => 'required|string',
            'modality' => 'required|string',
        ]);

        Waitlist::create([
            'usuario_id' => auth()->id(),
            'name' => $request->name,
            'phone' => $request->phone,
            'availability' => $request->availability,
            'modality' => $request->modality,
            'fecha_especifica' => $request->fecha_especifica,
            'hora_inicio' => $request->hora_inicio,
            'dia_semana' => $request->fecha_especifica ? \Carbon\Carbon::parse($request->fecha_especifica)->dayOfWeek : null,
        ]);

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
