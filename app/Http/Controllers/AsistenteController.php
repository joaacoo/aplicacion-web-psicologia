<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use Carbon\Carbon;
use Gemini\Factory;
use Gemini\Data\Tool;
use Gemini\Data\FunctionDeclaration;
use Gemini\Data\Schema;
use Gemini\Enums\DataType;
use Gemini\Data\Content;
use Gemini\Data\Part;
use Gemini\Data\FunctionResponse;
use Gemini\Enums\Role;

class AsistenteController extends Controller
{
    public function chat(Request $request)
    {
        if (!auth()->check() || auth()->user()->rol !== 'admin') {
            return response()->json(['message' => 'Acceso denegado.'], 403);
        }

        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $userMessage = $request->input('message');
        \Log::info("Gemini Chat Request: " . substr($userMessage, 0, 50) . "...");

        try {
            $apiKey = env('GEMINI_API_KEY');
            \Log::info("DEBUG: Checking GEMINI_API_KEY from env");
            if (empty($apiKey)) {
                \Log::error("Gemini Error: API Key is empty or null. Check .env file.");
                // Fallback attempt (sometimes getenv works where env doesn't in some configs)
                $apiKey = getenv('GEMINI_API_KEY');
                if ($apiKey) {
                    \Log::info("DEBUG: Found API Key using getenv()");
                }
            }

            if (!$apiKey) {
                \Log::error("Gemini Error: Falta API Key (Final check)");
                throw new \Exception("Falta API Key - Verifique configuración de entorno.");
            }

            $client = (new Factory)->withApiKey($apiKey)->make();
            $model = $client->generativeModel(model: 'gemini-1.5-flash-001');

            // Definir herramientas
            $tools = [
                new Tool(
                    functionDeclarations: [
                        new FunctionDeclaration(
                            name: 'obtenerTurnosHoy',
                            description: 'Obtiene los turnos del día.',
                            parameters: new Schema(
                                type: DataType::OBJECT,
                                properties: []
                            )
                        )
                    ]
                )
            ];

            foreach ($tools as $tool) {
                $model = $model->withTool($tool);
            }



            // Simular history con system instruction "fake"
            $chat = $model->startChat(history: [
                new Content(
                    parts: [new Part(text: "Eres un asistente administrativo privado para el consultorio de Nazarena.
- SOLO respondes sobre: turnos de hoy/mañana/semana, comprobantes pendientes por validar, deudores (nuevos que deben pagar antes), resumen facturado del mes.
- Usa datos reales del sistema.
- Responde corto y en español.
- Si no es sobre eso: 'Solo puedo ayudarte con turnos, pagos pendientes y agenda. ¿Qué necesitás?'
- Para nuevos pacientes: recuerda que deben pagar antes de la sesión.
- Fecha actual: " . now()->format('d/m/Y') )],
                    role: Role::USER
                ),
                new Content(
                    parts: [new Part(text: "Entendido. Soy el Asistente Virtual administrativo.")],
                    role: Role::MODEL
                )
            ]);

            \Log::info("Enviando mensaje a Gemini...");
            $response = $chat->sendMessage($userMessage);

            // Manejo manual de llamadas a función (Robustez)
            $candidates = $response->candidates;
            $functionCall = null;
            
            if (!empty($candidates) && isset($candidates[0]->content->parts[0]->functionCall)) {
                $functionCall = $candidates[0]->content->parts[0]->functionCall;
            }

            if ($functionCall) {
                \Log::info("Gemini solicitó función: " . $functionCall->name);
                if ($functionCall->name === 'obtenerTurnosHoy') {
                    $toolResult = $this->obtenerTurnosHoy();
                    \Log::info("Resultado función: " . substr($toolResult, 0, 100));
                    
                    // Enviar resultado de la herramienta de vuelta al modelo
                    // Envolvemos en Content para cumplir con la firma estricta de sendMessage (Text|Content)
                    $part = new Part(
                        functionResponse: new FunctionResponse(
                            name: 'obtenerTurnosHoy',
                            response: ['content' => $toolResult]
                        )
                    );
                    
                    $response = $chat->sendMessage(new Content(parts: [$part], role: 'function'));
                }
            }

            $responseText = $response->text();

            if (!$responseText) {
                \Log::warning("Gemini devolvió respuesta vacía.");
                return response()->json(['response' => 'La IA no pudo generar una respuesta. (Empty)']);
            }

            return response()->json(['response' => $responseText]);

        } catch (\Throwable $e) {
            \Log::error("Gemini Critical Error: " . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return response()->json(['response' => 'Error: ' . $e->getMessage()]);
        }
    }

    private function obtenerTurnosHoy()
    {
        $todayStr = now()->translatedFormat('l j \d\e F \d\e Y');
        
        $appointmentsToday = Appointment::with('user')
            ->whereDate('fecha_hora', now())
            ->where('estado', '!=', 'cancelado')
            ->orderBy('fecha_hora', 'asc')
            ->get();

        if ($appointmentsToday->isEmpty()) {
            return "No hay turnos confirmados para hoy ($todayStr).";
        }

        $result = "Turnos para hoy ($todayStr):\n";
        foreach ($appointmentsToday as $appt) {
            $time = $appt->fecha_hora->format('H:i');
            $client = $appt->user ? $appt->user->nombre : 'Usuario desconocido';
            $estado = $appt->estado;
            $result .= "- $time hs: $client ($estado)\n";
        }
        
        return $result;
    }
}
