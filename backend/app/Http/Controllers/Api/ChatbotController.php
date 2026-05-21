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

            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'],
                'provider' => $result['provider']
            ]);
        } catch (\Exception $e) {
            Log::error("ChatbotController: Error procesando chat: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => "Ha ocurrido un error inesperado al procesar tu consulta conversacional. Por favor, reintenta en unos instantes.",
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
