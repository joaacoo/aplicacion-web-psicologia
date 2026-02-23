<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

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
            'user_id' => Auth::id(),
            'email_contacto' => Auth::check() ? Auth::user()->email : $request->email_contacto,
            'subject' => $request->subject,
            'description' => $cleanDescription,
            'status' => 'nuevo',
            'priority' => 'media',
            'metadata' => [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->input('url_origen'),
            ]
        ]);

        // Notificar al Admin (Web + Mail)
        try {
            $admin = \App\Models\User::where('rol', 'admin')->first();
            if ($admin) {
                $sender = $ticket->email_contacto ?? 'Anónimo';
                if (Auth::check()) {
                    $sender = Auth::user()->nombre . " ({$sender})";
                }

                $admin->notify(new \App\Notifications\AdminNotification([
                    'title' => 'Reporte de Problema',
                    'mensaje' => "Nuevo reporte de Problema\n\n**Remitente:**\n{$sender}\n\n**Asunto:**\n{$ticket->subject}\n\n**Descripción:**\n{$ticket->description}",
                    'link' => route('admin.dashboard'),
                    'type' => 'error'
                ]));
            } else {
                // Fallback manual email if no admin user found
                Mail::raw("Se ha reportado un nuevo problema:\n\nAsunto: {$ticket->subject}\nDescripción: {$ticket->description}", function ($message) use ($ticket) {
                    $message->to(env('SUPPORT_EMAIL', 'joacodeluca2009@gmail.com'))
                        ->subject("Nuevo Reporte de Problema: {$ticket->subject}");
                });
            }
        } catch (\Exception $e) {
            // Log the error but don't fail the request
            \Illuminate\Support\Facades\Log::error("Error enviando notificación de ticket: " . $e->getMessage());
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Reporte enviado correctamente. El administrador ha sido notificado (vía web), aunque el envío de mail falló si no hay credenciales configuradas.',
                'status' => 'success'
            ], 201);
        }

        return back()->with('success', 'Reporte enviado correctamente.');
    }
}
