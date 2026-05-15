<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AI\AuditService;
use Illuminate\Http\Request;

class AuditCallbackController extends Controller
{
    protected $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    public function __invoke(Request $request)
    {
        // 1. Verificar firma HMAC (si el secreto está configurado)
        $secret = config('ai.n8n_webhook_secret');
        if ($secret) {
            $signature = $request->header('X-N8N-Signature');
            $payload = $request->getContent();
            $computed = hash_hmac('sha256', $payload, $secret);

            if (!$signature || !hash_equals($computed, $signature)) {
                \Illuminate\Support\Facades\Log::warning("Intento de callback n8n con firma inválida desde: " . $request->ip());
                return response()->json(['error' => 'Invalid signature'], 401);
            }
        }

        // 2. n8n envía el JSON resultado
        $data = $request->all();

        $success = $this->auditService->handleCallback($data);

        if (!$success) {
            return response()->json(['error' => 'Audit record not found or invalid data'], 404);
        }

        return response()->json(['message' => 'Audit updated successfully']);
    }
}
