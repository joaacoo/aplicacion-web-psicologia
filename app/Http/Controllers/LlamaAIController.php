<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class LlamaAIController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $message = $request->input('message');
        $user = Auth::user();

        // Usar Together AI API con Llama 2
        // Registrate en https://www.together.ai/ para obtener tu API key
        $apiKey = env('TOGETHER_AI_API_KEY');
        
        if (!$apiKey) {
            return response()->json([
                'message' => 'Lo siento, el servicio de IA no está configurado. Por favor, contacta al administrador.'
            ], 503);
        }

        try {
            $response = Http::withToken($apiKey)
                ->timeout(30)
                ->post('https://api.together.xyz/inference', [
                    'model' => 'meta-llama/Llama-2-7b-chat-hf',
                    'prompt' => $this->buildPrompt($message, $user),
                    'max_tokens' => 512,
                    'temperature' => 0.7,
                    'top_p' => 0.9,
                ]);

            if ($response->successful()) {
                $result = $response->json();
                $text = $result['output']['choices'][0]['text'] ?? 'No se pudo procesar la respuesta.';
                
                // Limpiar la respuesta
                $text = trim($text);
                
                return response()->json([
                    'message' => $text
                ]);
            } else {
                return response()->json([
                    'message' => 'Error al procesar tu pregunta. Intenta nuevamente.'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error de conexión. Por favor, intenta más tarde.'
            ], 500);
        }
    }

    private function buildPrompt($message, $user)
    {
        $context = "Eres un asistente IA amigable para una clínica psicológica llamada 'Espacio Terapéutico' dirigida por la Lic. Nazarena De Luca. ";
        $context .= "Tu nombre es Llama IA y debes ayudar con: ";
        $context .= "- Preguntas sobre la agenda de turnos ";
        $context .= "- Información sobre pagos de sesiones ";
        $context .= "- Análisis de pacientes (solo información general) ";
        $context .= "- Recordatorios de citas ";
        $context .= "- Consejos generales sobre bienestar ";
        
        if ($user) {
            $context .= "El usuario es: {$user->name}. ";
        }
        
        $context .= "Sé conciso, amable y profesional. Responde en español.\n\n";
        $context .= "Pregunta del usuario: {$message}\n\n";
        $context .= "Respuesta:";

        return $context;
    }
}
