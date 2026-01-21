<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function store(Request $request)
    {
        // Validación
        $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'email_contacto' => 'nullable|email|max:255',
        ]);

        // Sanitización básica para descripción
        $cleanDescription = strip_tags($request->description);

        $ticket = Ticket::create([
            'user_id' => Auth::id(), // Puede ser null
            'email_contacto' => Auth::check() ? Auth::user()->email : $request->email_contacto,
            'subject' => $request->subject,
            'description' => $cleanDescription,
            'status' => 'abierto',
            'priority' => 'media',
            'metadata' => [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->input('url_origen'),
            ]
        ]);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Reporte enviado correctamente. Nos pondremos en contacto.'], 201);
        }

        return back()->with('success', 'Reporte enviado correctamente.');
    }
}
