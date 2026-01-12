<?php

namespace App\Http\Controllers;

use App\Models\Waitlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WaitlistController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'fecha_especifica' => 'required|date|after_or_equal:today',
            'hora_inicio' => 'required',
        ]);

        // Evitar duplicados
        $exists = Waitlist::where('usuario_id', Auth::id())
            ->where('fecha_especifica', $request->fecha_especifica)
            ->where('hora_inicio', $request->hora_inicio)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Ya estás en la lista para este turno.'], 422);
        }

        Waitlist::create([
            'usuario_id' => Auth::id(),
            'fecha_especifica' => $request->fecha_especifica,
            'hora_inicio' => $request->hora_inicio,
            'dia_semana' => \Carbon\Carbon::parse($request->fecha_especifica)->dayOfWeek,
        ]);

        return response()->json(['message' => '¡Listo! Te avisaremos si este turno se libera.']);
    }
}
