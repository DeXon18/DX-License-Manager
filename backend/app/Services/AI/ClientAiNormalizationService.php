<?php

namespace App\Services\AI;

use App\Models\Client;
use App\Models\AiTokenLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ClientAiNormalizationService
{
    /**
     * Evalúa semánticamente un nombre contra candidatos locales utilizando IA con fallback.
     *
     * @param string $rawName Nombre entrante a normalizar.
     * @return array [matched, matched_id, confidence, provider, reason]
     */
    public function evaluate(string $rawName): array
    {
        $rawName = trim($rawName);
        if (empty($rawName)) {
            return $this->emptyResponse('Nombre entrante vacío.');
        }

        // 1. Pre-filtrado local por tokens
        $candidates = $this->findLocalCandidates($rawName);
        if (empty($candidates)) {
            return $this->emptyResponse('No se encontraron candidatos locales con coincidencia léxica.');
        }

        // 2. Preparar Prompt estructurado
        $candidatesJson = json_encode($candidates, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $prompt = <<<EOT
Dado un nombre de cliente entrante y una lista de clientes candidatos existentes en la base de datos, determina si el nombre entrante se refiere inequívocamente a alguno de los candidatos (debido a siglas, acrónimos, abreviaciones corporativas, variantes regionales o erratas obvias).

Nombre de cliente entrante: "{$rawName}"

Candidatos existentes en la Base de Datos:
{$candidatesJson}

Instrucciones críticas:
1. Responde ÚNICAMENTE con un objeto JSON válido.
2. Si el nombre entrante es claramente el mismo cliente que un candidato (ej. "Uro Vehiculos Especiales Sa (Urovesa)" coincide plenamente con "Urovesa", o "Geminis Lathes S.a." coincide con "Geminis Lathes"), establece "matched" a true y asigna el correspondiente ID del candidato.
3. Si el nombre entrante es una empresa completamente distinta y no se relaciona con ningún candidato, establece "matched" a false y matched_id a null.
4. Si no estás seguro o la coincidencia es ambigua, establece "matched" a false.

Responde estrictamente en formato JSON con la siguiente estructura:
{
  "matched": true | false,
  "matched_id": <ID del candidato que coincide, o null>,
  "confidence": <número flotante entre 0 y 1>,
  "reason": "<explicación en una frase corta en español>"
}
EOT;

        // 3. Centralización en OpenRouter Hub
        $openrouterKey = config('ai.openrouter_key');
        if (empty($openrouterKey)) {
            Log::error("ClientAiNormalizationService: OPENROUTER_API_KEY no está configurada.");
            return $this->emptyResponse('API Key faltante.');
        }

        $route = \App\Models\AiRoute::with(['primaryModel', 'fallbackModel'])->find('normalizacion');
        $modelId = $route && $route->primaryModel ? $route->primaryModel->openrouter_id : 'google/gemma-4-31b-it:free';

        try {
            $parsed = $this->callOpenRouterApi($openrouterKey, $modelId, $prompt);
            if ($parsed) return $parsed;
        } catch (\Exception $e) {
            $isTimeout = str_contains($e->getMessage(), 'timed out') || str_contains($e->getMessage(), 'cURL error 28') || str_contains($e->getMessage(), 'Connection');
            if ($e->getCode() == 429 || $isTimeout) {
                if ($route && $route->fallbackModel) {
                    $reason = $isTimeout ? 'Timeout' : '429 Rate Limit';
                    Log::warning("ClientAiNormalizationService: {$reason} con {$modelId}, saltando a fallback {$route->fallbackModel->openrouter_id}");
                    try {
                        $parsed = $this->callOpenRouterApi($openrouterKey, $route->fallbackModel->openrouter_id, $prompt);
                        if ($parsed) {
                            $parsed['used_fallback'] = true;
                            return $parsed;
                        }
                    } catch (\Exception $fallbackE) {
                        Log::error("ClientAiNormalizationService: Fallback falló: " . $fallbackE->getMessage());
                    }
                } else {
                    Log::error("ClientAiNormalizationService: Error/Timeout y no hay fallback configurado.");
                }
            } else {
                Log::error("ClientAiNormalizationService: Error llamando a OpenRouter: " . $e->getMessage());
            }
        }

        return $this->emptyResponse('El proveedor de IA falló o retornó un formato inválido.');
    }

    private function callOpenRouterApi(string $key, string $modelId, string $prompt): ?array
    {
        $response = Http::timeout(30)
            ->withHeaders([
                'Authorization' => "Bearer {$key}",
                'Content-Type' => 'application/json',
                'HTTP-Referer' => 'https://beta.dxpro.es',
                'X-Title' => 'DX License Manager'
            ])
            ->post('https://openrouter.ai/api/v1/chat/completions', [
                'model' => $modelId,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt]
                ],
                'temperature' => 0.1
            ]);

        if (!$response->successful()) {
            throw new \Exception("Status " . $response->status() . ": " . $response->body(), $response->status());
        }

        $resData = $response->json();
        $text = $resData['choices'][0]['message']['content'] ?? '{}';
        
        if (isset($resData['usage'])) {
            $this->logTokens('openrouter', $modelId, 'normalization_search', $resData['usage']);
        }

        $parsed = $this->parseJsonAndValidate($text);
        if ($parsed) {
            $parsed['provider'] = 'openrouter';
            $parsed['model'] = $modelId;
            return $parsed;
        }
        
        return null;
    }

    /**
     * Busca candidatos existentes en la DB partiendo de palabras clave.
     */
    private function findLocalCandidates(string $name): array
    {
        // Limpieza básica y conversión a minúsculas
        $clean = mb_strtolower($name);
        
        // Excluir stop-words corporativas comunes para aislar los lexemas raíces
        $stopwords = [
            'sa', 'sl', 'co', 'ltd', 'inc', 'gmbh', 'corp', 'corporation', 'company', 
            'compania', 'de', 'y', 'la', 'en', 'el', 'los', 'las', 'un', 'una', 'del', 
            'al', 'e', 'o', 'u', 'asociacion', 'grupo', 'vehiculos', 'especiales',
            's.a.', 's.l.', 's.l', 's.a', 'ltd.', 'inc.', 'ltda', 'limitada', 'sociedad', 'anonima'
        ];

        // Quitar caracteres no alfanuméricos (mantener espacios)
        $clean = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $clean);
        
        // Dividir en palabras
        $words = preg_split('/\s+/', $clean, -1, PREG_SPLIT_NO_EMPTY);
        
        // Filtrar stop-words y palabras cortas de menos de 3 letras
        $tokens = array_filter($words, function ($word) use ($stopwords) {
            return strlen($word) >= 3 && !in_array($word, $stopwords);
        });

        if (empty($tokens)) {
            return [];
        }

        // Buscar en BD clientes que coincidan con alguno de los tokens (LIKE)
        $query = Client::query();
        foreach (array_values($tokens) as $index => $token) {
            if ($index === 0) {
                $query->where('name', 'LIKE', "%{$token}%");
            } else {
                $query->orWhere('name', 'LIKE', "%{$token}%");
            }
        }

        // Limitar a máximo 8 candidatos para mantener el prompt compacto y rápido
        return $query->limit(8)->get(['id', 'name'])->toArray();
    }

    /**
     * Limpia y valida el JSON de respuesta del LLM.
     */
    private function parseJsonAndValidate(string $text): ?array
    {
        $text = trim($text);
        // Quitar posibles bloques markdown
        $text = preg_replace('/```json\s*|```/i', '', $text);
        $text = trim($text);

        $decoded = json_decode($text, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
            return null;
        }

        // Asegurar campos obligatorios
        return [
            'matched' => (bool) ($decoded['matched'] ?? false),
            'matched_id' => isset($decoded['matched_id']) ? (int) $decoded['matched_id'] : null,
            'confidence' => (float) ($decoded['confidence'] ?? 0.0),
            'reason' => (string) ($decoded['reason'] ?? 'Sin razón especificada.'),
        ];
    }

    /**
     * Estructura vacía por defecto ante fallos.
     */
    private function emptyResponse(string $reason): array
    {
        return [
            'matched' => false,
            'matched_id' => null,
            'confidence' => 0.0,
            'provider' => null,
            'reason' => $reason
        ];
    }

    /**
     * Evalúa si dos nombres de cliente existentes se refieren a la misma entidad.
     *
     * @param string $name1 Nombre del primer cliente.
     * @param string $name2 Nombre del segundo cliente.
     * @return array [is_duplicate, confidence, provider, reason]
     */
    public function evaluateDuplicatePair(string $name1, string $name2): array
    {
        $name1 = trim($name1);
        $name2 = trim($name2);
        
        $prompt = <<<EOT
Determina de forma experta si los dos siguientes nombres de cliente corresponden a la misma empresa o entidad de negocio (ej. variantes ortográficas, erratas, fusiones, siglas de la misma marca, o filiales idénticas).

Cliente A: "{$name1}"
Cliente B: "{$name2}"

Instrucciones críticas:
1. Responde ÚNICAMENTE con un objeto JSON válido.
2. Si consideras que son la misma empresa, establece "is_duplicate" a true.
3. Si son empresas claramente distintas, establece "is_duplicate" a false.
4. Explica detalladamente en español tu razonamiento en el campo "reason" (máximo una frase).

Responde estrictamente en formato JSON con la siguiente estructura:
{
  "is_duplicate": true | false,
  "confidence": <número flotante entre 0 y 1>,
  "reason": "<explicación del razonamiento en español>"
}
EOT;

        // Exec fallback chain (Gemini -> Deepseek)
        $geminiKey = config('ai.gemini_key');
        if ($geminiKey) {
            try {
                $response = Http::timeout(10)->post(
                    "https://generativelanguage.googleapis.com/v1beta/models/gemini-3.1-flash-lite:generateContent?key={$geminiKey}",
                    [
                        'contents' => [['parts' => [['text' => $prompt]]]],
                        'generationConfig' => ['response_mime_type' => 'application/json']
                    ]
                );

                if ($response->successful()) {
                    $resData = $response->json();
                    $text = $resData['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
                    
                    if (isset($resData['usageMetadata'])) {
                        $this->logTokens('gemini', 'gemini-3.1-flash-lite', 'normalization_pair', $resData['usageMetadata']);
                    }

                    $decoded = $this->parseJsonAndValidateDuplicate($text);
                    if ($decoded) {
                        $decoded['provider'] = 'gemini';
                        return $decoded;
                    }
                }
            } catch (\Exception $e) {
                Log::warning("ClientAiNormalizationService: Error evaluando par en Gemini: " . $e->getMessage());
            }
        }
        
        // Deepseek fallback
        $deepseekKey = config('ai.deepseek_key');
        if ($deepseekKey) {
            try {
                $response = Http::timeout(10)
                    ->withHeaders(['Authorization' => "Bearer {$deepseekKey}", 'Content-Type' => 'application/json'])
                    ->post('https://api.deepseek.com/chat/completions', [
                        'model' => 'deepseek-chat',
                        'messages' => [['role' => 'user', 'content' => $prompt]],
                        'response_format' => ['type' => 'json_object'],
                        'temperature' => 0.1
                    ]);

                if ($response->successful()) {
                    $resData = $response->json();
                    $text = $resData['choices'][0]['message']['content'] ?? '{}';
                    
                    if (isset($resData['usage'])) {
                        $this->logTokens('deepseek', 'deepseek-chat', 'normalization_pair', $resData['usage']);
                    }

                    $decoded = $this->parseJsonAndValidateDuplicate($text);
                    if ($decoded) {
                        $decoded['provider'] = 'deepseek';
                        return $decoded;
                    }
                }
            } catch (\Exception $e) {
                Log::warning("ClientAiNormalizationService: Error evaluando par en Deepseek: " . $e->getMessage());
            }
        }

        // Return local warning response
        return [
            'is_duplicate' => true,
            'confidence' => 0.75,
            'provider' => 'local',
            'reason' => 'Coincidencia local por alto porcentaje de similitud.'
        ];
    }

    private function parseJsonAndValidateDuplicate(string $text): ?array
    {
        $text = trim($text);
        $text = preg_replace('/```json\s*|```/i', '', $text);
        $text = trim($text);

        $decoded = json_decode($text, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
            return null;
        }

        return [
            'is_duplicate' => (bool) ($decoded['is_duplicate'] ?? false),
            'confidence' => (float) ($decoded['confidence'] ?? 0.0),
            'reason' => (string) ($decoded['reason'] ?? 'Analizado semánticamente.'),
        ];
    }

    /**
     * Registra los tokens consumidos en la base de datos.
     */
    private function logTokens(string $provider, string $model, string $action, array $usageData): void
    {
        try {
            $promptTokens = 0;
            $completionTokens = 0;
            $totalTokens = 0;

            if ($provider === 'gemini') {
                $promptTokens = $usageData['promptTokenCount'] ?? 0;
                $completionTokens = $usageData['candidatesTokenCount'] ?? 0;
                $totalTokens = $usageData['totalTokenCount'] ?? 0;
            } else {
                // Formato OpenAI/DeepSeek/OpenRouter
                $promptTokens = $usageData['prompt_tokens'] ?? 0;
                $completionTokens = $usageData['completion_tokens'] ?? 0;
                $totalTokens = $usageData['total_tokens'] ?? 0;
            }

            AiTokenLog::create([
                'provider' => $provider,
                'model' => $model,
                'action' => $action,
                'prompt_tokens' => $promptTokens,
                'completion_tokens' => $completionTokens,
                'total_tokens' => $totalTokens,
                'user_id' => auth()->check() ? auth()->id() : null,
            ]);
        } catch (\Exception $e) {
            Log::warning("ClientAiNormalizationService: No se pudo registrar tokens: " . $e->getMessage());
        }
    }
}
