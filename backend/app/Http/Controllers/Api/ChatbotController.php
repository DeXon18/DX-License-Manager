<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AI\ChatbotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    protected ChatbotService $chatbotService;

    public function __construct(ChatbotService $chatbotService)
    {
        $this->chatbotService = $chatbotService;
    }

    /**
     * Procesa una consulta conversacional desde el widget web del portal.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function query(Request $request): JsonResponse
    {
        $request->validate([
            'messages' => 'required|array',
            'messages.*.role' => 'required|string|in:user,assistant,model',
            'messages.*.content' => 'required|string',
        ]);

        try {
            $history = $request->input('messages');
            $result = $this->chatbotService->query($history);

            // Registrar telemetría de tokens si el proveedor los reporta
            if (!empty($result['usage_metadata'])) {
                $usage = $result['usage_metadata'];
                
                // Formato Gemini
                $promptTokens = $usage['promptTokenCount'] ?? ($usage['prompt_tokens'] ?? 0);
                $completionTokens = $usage['candidatesTokenCount'] ?? ($usage['completion_tokens'] ?? 0);
                $totalTokens = $usage['totalTokenCount'] ?? ($usage['total_tokens'] ?? 0);
                
                if ($totalTokens > 0) {
                    \App\Models\AiTokenLog::create([
                        'user_id' => auth()->id(), // null si el chatbot no requiere auth, pero usualmente en admin sí
                        'action' => 'chatbot_query',
                        'provider' => $result['provider'] ?? 'gemini',
                        'model' => $result['model'] ?? 'gemini-1.5-flash',
                        'prompt_tokens' => $promptTokens,
                        'completion_tokens' => $completionTokens,
                        'total_tokens' => $totalTokens,
                    ]);
                }
            }

            $providerLabel = $result['provider'] ?? 'openrouter';
            if (!empty($result['model'])) {
                $modelRecord = \App\Models\AiModel::where('openrouter_id', $result['model'])->first();
                if ($modelRecord) {
                    $providerLabel = $modelRecord->name;
                    if ($result['provider'] === 'redis-cache') {
                        $providerLabel .= ' [Caché]';
                    }
                }
            }

            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'],
                'provider' => $providerLabel,
                'usage_metadata' => $result['usage_metadata'] ?? null,
                'data' => $result['data'] ?? []
            ]);
        } catch (\Exception $e) {
            Log::error("ChatbotController: Error procesando chat: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => "Ha ocurrido un error inesperado al procesar tu consulta conversacional. Por favor, reintenta en unos instantes.",
            ], 500);
        }
    }
}
