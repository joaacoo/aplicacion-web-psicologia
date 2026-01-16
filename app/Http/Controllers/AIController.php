<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AIController extends Controller
{
    public function chat(Request $request)
    {
        // 1. Validation for Admin
        if (!auth()->check() || auth()->user()->rol !== 'admin') {
            return response()->json(['message' => 'Acceso denegado.'], 403);
        }

        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $mensajeUsuario = $request->input('message');
        
        // 2. Generate Context dynamically
        $contexto = $this->getContext($mensajeUsuario);

        // 3. Prepare Master Prompt
        // 3. Prepare Master Prompt
        $promptBase = "Sos un Asistente Virtual Interno de Soporte UX / Sistema integrado dentro de un Dashboard Profesional de Gestión para Psicóloga.

Este sistema es administrativo y operativo, no clínico.

👉 NO sos terapeuta.
👉 NO das diagnósticos clínicos.
👉 NO reemplazás a la profesional.

Tu función es explicar cómo funciona el sistema, ayudar a entender comportamientos de la interfaz, guiar a la psicóloga en el uso correcto del dashboard, aclarar estados, botones y flujos, y responder preguntas operativas y administrativas.

🧭 ESTRUCTURA DE LA INTERFAZ:
- Header Global (Navbar Superior): Se oculta al bajar y reaparece al subir (Smart Sticky Header). Maximiza el espacio.
- Page Header (Encabezado de Sección): Visible dentro del contenido. Indica en qué sección estás (ej. Disponibilidad y Horarios).

🔴 AGENDA Y TURNOS:
Estados: Libre, Reservado, Confirmado, Cancelado.
Si sábados y domingos figuran como LIBRES, los pacientes pueden reservar.

🔵 PAGOS:
Estados: Aprobado, Rechazado, Pendiente.
Un pago rechazado no confirma el turno. No inventar datos bancarios.

🟣 SOPORTE INTERNO:
Si hay errores visuales (botones que no cambian), sugerí recargar o revisar el historial.
No prometas soluciones técnicas imposibles.

⚡ COMPORTAMIENTO CONVERSACIONAL:
Respondés en tiempo real, estilo ChatGPT.
Continuidad de contexto.
Español neutro / Argentina.
Empático, profesional, ordenado y claro.

Información Actual del Sistema:
{{CONTEXTO_DEL_SISTEMA}}

Consulta del usuario:
{{MENSAJE_DEL_USUARIO}}";

        $prompt = str_replace(
            ['{{CONTEXTO_DEL_SISTEMA}}', '{{MENSAJE_DEL_USUARIO}}'],
            [$contexto, $mensajeUsuario],
            $promptBase
        );

        // 4. Stream Response (Ollama)
        return response()->stream(function () use ($prompt) {
            
            // Ollama API Endpoint (Local)
            $ch = curl_init('http://localhost:11434/api/generate');

            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                CURLOPT_POSTFIELDS => json_encode([
                    'model' => 'llama3.2:1b', // Ensure this model exists in your Ollama
                    'prompt' => $prompt,
                    'stream' => true // Enable streaming
                ]),
                CURLOPT_WRITEFUNCTION => function ($curl, $data) {
                    // Ollama sends chunks. We can echo them directly or process them.
                    // For simplicity, we echo directly. Frontend handles JSON parsing if needed.
                    echo $data;
                    ob_flush();
                    flush();
                    return strlen($data);
                }
            ]);

            curl_exec($ch);
            curl_close($ch);

        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no', // Disable buffering for Nginx
        ]);
    }

    private function getContext($message)
    {
        $messageLower = mb_strtolower($message);
        $todayStr = now()->translatedFormat('l j \d\e F \d\e Y');
        $context = "Fecha actual: $todayStr.\n\n";
        
        // Detectar intenciones
        $needsAgenda = preg_match('/agenda|turno|sesi[oó]n|hoy|d[ií]a|mañana|pr[oó]xim|calendario/', $messageLower);
        $needsPagos = preg_match('/pago|cobro|dinero|pendiente|verificado|rechazado|comprobante/', $messageLower);
        $needsPacientes = preg_match('/paciente|cliente|usuario|total|cuántos|cuántas/', $messageLower);
        
        // Default to Agenda if unknown
        if (!$needsAgenda && !$needsPagos && !$needsPacientes) {
            $needsAgenda = true;
        }
        
        // 1. Agenda
        if ($needsAgenda) {
            $appointmentsToday = Appointment::with('user')
                ->whereDate('fecha_hora', now())
                ->orderBy('fecha_hora', 'asc')
                ->get();
                
            $appointmentsTomorrow = Appointment::with('user')
                ->whereDate('fecha_hora', now()->addDay())
                ->where('estado', '!=', 'cancelado')
                ->orderBy('fecha_hora', 'asc')
                ->get();
                
            $context .= "AGENDA:\n";
            if ($appointmentsToday->isEmpty()) {
                $context .= "- Hoy no hay turnos agendados.\n";
            } else {
                $context .= "- Turnos de HOY:\n";
                foreach ($appointmentsToday as $appt) {
                    $time = $appt->fecha_hora->format('H:i');
                    $client = $appt->user ? $appt->user->nombre . ' ' . $appt->user->apellido : 'Usuario eliminado';
                    $estado = $appt->estado;
                    $context .= "  * $time hs: $client ($estado)\n";
                }
            }
            
            if (!$appointmentsTomorrow->isEmpty()) {
                $context .= "- Turnos de MAÑANA:\n";
                foreach ($appointmentsTomorrow as $appt) {
                    $time = $appt->fecha_hora->format('H:i');
                    $client = $appt->user ? $appt->user->nombre . ' ' . $appt->user->apellido : 'Usuario eliminado';
                    $context .= "  * $time hs: $client\n";
                }
            }
        }
        
        // 2. Pagos
        if ($needsPagos) {
            $pagosPendientes = Payment::where('estado', 'pendiente')->count();
            $pagosVerificados = Payment::where('estado', 'verificado')
                ->whereMonth('created_at', now()->month)
                ->count();
                
            $context .= "\nPAGOS:\n";
            $context .= "- Pendientes de verificar: $pagosPendientes\n";
            $context .= "- Verificados este mes: $pagosVerificados\n";
        }
        
        // 3. Pacientes
        if ($needsPacientes) {
            $totalPacientes = User::where('rol', 'paciente')->count();
            $nuevosPacientes = User::where('rol', 'paciente')->where('tipo_paciente', 'nuevo')->count();
            
            $context .= "\nPACIENTES:\n";
            $context .= "- Total activos: $totalPacientes\n";
            $context .= "- Nuevos (ingreso reciente): $nuevosPacientes\n";
        }

        return $context;
    }
}
