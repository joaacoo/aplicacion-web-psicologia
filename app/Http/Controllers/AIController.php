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
        $promptBase = "Sos Gemma, la Asistente Virtual Administrativa de la Lic. Nazarena De Luca.
Tu función es gestionar tareas administrativas del consultorio.
NO tenés acceso ni debés hablar sobre historiales clínicos o diagnósticos médicos.

MODELO MENTAL:
- Sos eficiente, cálida y profesional.
- Tu prioridad es facilitar la gestión del consultorio.
- Usás formato Markdown para resaltar datos importantes (negrita).
- Proporcionás links directos para acciones concretas.

FORMATO DE RESPUESTA:
- Usá enlaces explícitos en este formato: `[Texto del botón](#ancla)`.
  - Links disponibles:
    - Turnos: `[Ver Turnos](#turnos)`
    - Agendar: `[Agendar Turno](#agenda)`
    - Pacientes: `[Ver Pacientes](#pacientes)`
    - Pagos: `[Ver Pagos](#pagos)`
    - Documentos: `[Biblioteca](#documentos)`
- Si listás tareas, usá una lista numerada.

CONTEXTO ACTUAL DEL SISTEMA:
{{CONTEXTO_DEL_SISTEMA}}

CONSULTA DEL USUARIO:
{{MENSAJE_DEL_USUARIO}}

Instrucción final: Respondé de forma concisa (máx 3 parrafor). Si te piden agendar, da el link. Si te piden revisar pagos, da el resumen y el link.";

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
                    'model' => 'gemma3:1b', // Updated to Gemma 3 model
                    'prompt' => $prompt,
                    'stream' => true
                ]),
                CURLOPT_WRITEFUNCTION => function ($curl, $data) {
                    $lines = explode("\n", trim($data));

                    foreach ($lines as $line) {
                        $json = json_decode($line, true);

                        if (isset($json['response'])) {
                            echo $json['response'];
                            ob_flush();
                            flush();
                        }
                    }

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
        $needsAgenda = preg_match('/turno|agenda|sesi[oó]n|hoy|mañana|pr[oó]xim|calendario|hora|horario|agendar|cita/', $messageLower);
        $needsPagos = preg_match('/pago|cobro|dinero|comprobante|pendiente|verificado|rechazado/', $messageLower);
        $needsPacientes = preg_match('/paciente|clientes|usuarios|cuántos|total|nuevos|activos|link|whatsapp/', $messageLower);
        $needsTareas = preg_match('/tarea|pendiente|hacer|recordator|debe/', $messageLower);

        if (!$needsAgenda && !$needsPagos && !$needsPacientes && !$needsTareas) {
            $needsAgenda = true; // Default
            $needsTareas = true;
        }
        
        // 1. Tareas Pendientes (Nuevo)
        if ($needsTareas || $needsPagos) {
            $pagosPendientesCount = Payment::where('estado', 'pendiente')->count();
            $turnosSinConfirmar = Appointment::where('estado', 'pendiente')
                ->where('fecha_hora', '>=', now())
                ->count();
            
            $context .= "TAREAS PENDIENTES:\n";
            if ($pagosPendientesCount > 0) {
                $context .= "1. Revisar $pagosPendientesCount comprobantes de pago pendientes.\n";
            }
            if ($turnosSinConfirmar > 0) {
                $context .= "2. Confirmar $turnosSinConfirmar solicitudes de turnos.\n";
            }
            if ($pagosPendientesCount == 0 && $turnosSinConfirmar == 0) {
                $context .= "- No tenés tareas administrativas urgentes.\n";
            }
            $context .= "\n";
        }

        // 2. Agenda
        if ($needsAgenda) {
            $appointmentsToday = Appointment::with('user')
                ->whereDate('fecha_hora', now())
                ->where('estado', '!=', 'cancelado')
                ->orderBy('fecha_hora', 'asc')
                ->get();
                
            $appointmentsTomorrow = Appointment::with('user')
                ->whereDate('fecha_hora', now()->addDay())
                ->where('estado', '!=', 'cancelado')
                ->orderBy('fecha_hora', 'asc')
                ->get();
                
            $context .= "AGENDA:\n";
            if ($appointmentsToday->isEmpty()) {
                $context .= "- Hoy no tienes turnos confirmados.\n";
            } else {
                $context .= "- Turnos de HOY:\n";
                foreach ($appointmentsToday as $appt) {
                    $time = $appt->fecha_hora->format('H:i');
                    $client = $appt->user ? $appt->user->nombre : 'Usuario eliminado';
                    $estado = $appt->estado;
                    $context .= "  * $time hs: $client ($estado)\n";
                }
            }
            
            if (!$appointmentsTomorrow->isEmpty()) {
                $context .= "\n- Turnos de MAÑANA:\n";
                foreach ($appointmentsTomorrow as $appt) {
                    $time = $appt->fecha_hora->format('H:i');
                    $client = $appt->user ? $appt->user->nombre : 'Usuario eliminado';
                    $context .= "  * $time hs: $client\n";
                }
            }
        }   
        
        // 3. Pagos
        if ($needsPagos) {
            $pagosVerificados = Payment::where('estado', 'verificado')
                ->whereMonth('created_at', now()->month)
                ->count();
                
            $context .= "\nESTADÍSTICAS PAGOS (Mes Actual):\n";
            $context .= "- Verificados: $pagosVerificados\n";
        }

        return $context;
    }
}
