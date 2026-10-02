<?php

namespace App\Services\AI;

use App\Models\AiModel;
use App\Models\AiRoute;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenRouterSyncService
{
    protected string $apiUrl = 'https://openrouter.ai/api/v1/models';

    /**
     * Top 10 modelos gratuitos líderes ordenados por preferencia de uso, calidad y contexto.
     */
    public const TOP_FREE_MODELS = [
        'openrouter/free' => [
            'name' => 'OpenRouter Free Router (Gratis)',
            'weekly_limit' => 5000000,
        ],
        'google/gemma-4-31b-it:free' => [
            'name' => 'Google Gemma 4 31B Instruct (Gratis)',
            'weekly_limit' => 5000000,
        ],
        'google/gemma-4-26b-a4b-it:free' => [
            'name' => 'Google Gemma 4 26B MoE (Gratis)',
            'weekly_limit' => 5000000,
        ],
        'qwen/qwen3.8-27b:free' => [
            'name' => 'Qwen 3.8 27B Vision (Gratis)',
            'weekly_limit' => 5000000,
        ],
        'nvidia/nemotron-3.5-lightning:free' => [
            'name' => 'NVIDIA Nemotron 3.5 Lightning 1M (Gratis)',
            'weekly_limit' => 5000000,
        ],
        'nvidia/nemotron-3-ultra-550b-a55b:free' => [
            'name' => 'NVIDIA Nemotron 3 Ultra 550B (Gratis)',
            'weekly_limit' => 5000000,
        ],
        'nvidia/nemotron-3-nano-omni-30b-a3b-reasoning:free' => [
            'name' => 'NVIDIA Nemotron 3 Nano Omni (Gratis)',
            'weekly_limit' => 5000000,
        ],
        'thinkingmachines/inkling:free' => [
            'name' => 'Thinking Machines Inkling 1M (Gratis)',
            'weekly_limit' => 5000000,
        ],
        'thinkingmachines/inkling-small:free' => [
            'name' => 'Thinking Machines Inkling Small (Gratis)',
            'weekly_limit' => 5000000,
        ],
        'cohere/north-mini-code:free' => [
            'name' => 'Cohere North Mini Code (Gratis)',
            'weekly_limit' => 5000000,
        ],
    ];

    /**
     * Sincroniza los modelos locales con la API pública de OpenRouter.
     *
     * @return array [
     *   'success' => bool,
     *   'total_remote' => int,
     *   'updated' => int,
     *   'added_free' => int,
     *   'deprecated' => int,
     *   'broken_routes' => array,
     *   'message' => string
     * ]
     */
    public function sync(): array
    {
        try {
            $response = Http::timeout(25)->get($this->apiUrl);

            if (!$response->successful()) {
                throw new \Exception("OpenRouter API respondió con estado " . $response->status());
            }

            $remoteData = $response->json('data') ?? [];
            if (empty($remoteData)) {
                throw new \Exception("La API de OpenRouter devolvió un catálogo vacío.");
            }

            // Indexar por ID de OpenRouter
            $remoteModels = [];
            foreach ($remoteData as $m) {
                if (isset($m['id'])) {
                    $remoteModels[$m['id']] = $m;
                }
            }

            // 1. Asegurar la presencia y activación de los Top 10 modelos Free si existen remotamente
            $addedFreeCount = 0;
            foreach (self::TOP_FREE_MODELS as $freeId => $meta) {
                if (isset($remoteModels[$freeId])) {
                    $remote = $remoteModels[$freeId];
                    $promptPrice = floatval($remote['pricing']['prompt'] ?? 0);
                    $completionPrice = floatval($remote['pricing']['completion'] ?? 0);

                    $model = AiModel::firstOrNew(['openrouter_id' => $freeId]);
                    if (!$model->exists) {
                        $model->name = $meta['name'];
                        $model->weekly_tokens_limit = $meta['weekly_limit'];
                        $addedFreeCount++;
                    }
                    $model->price_prompt = $promptPrice;
                    $model->price_completion = $completionPrice;
                    $model->is_free = true;
                    $model->is_active = true;
                    $model->save();
                }
            }

            $localModels = AiModel::all();
            $updatedCount = 0;
            $deprecatedCount = 0;
            $deprecatedModelIds = [];

            foreach ($localModels as $local) {
                if (isset($remoteModels[$local->openrouter_id])) {
                    $remote = $remoteModels[$local->openrouter_id];
                    
                    // Precios por token
                    $promptPrice = floatval($remote['pricing']['prompt'] ?? 0);
                    $completionPrice = floatval($remote['pricing']['completion'] ?? 0);
                    $isFree = ($promptPrice == 0 && $completionPrice == 0);

                    $local->update([
                        'price_prompt' => $promptPrice,
                        'price_completion' => $completionPrice,
                        'is_free' => $isFree,
                    ]);
                    $updatedCount++;
                } else {
                    // Modelo ya no existe en OpenRouter
                    if ($local->is_active) {
                        $local->update(['is_active' => false]);
                        $deprecatedCount++;
                        $deprecatedModelIds[] = $local->id;
                    }
                }
            }

            // Comprobar si hay rutas afectadas por modelos deprecados
            $brokenRoutes = [];
            if (!empty($deprecatedModelIds)) {
                $routes = AiRoute::with(['primaryModel', 'fallbackModel'])
                    ->whereIn('primary_model_id', $deprecatedModelIds)
                    ->orWhereIn('fallback_model_id', $deprecatedModelIds)
                    ->get();

                foreach ($routes as $route) {
                    $isPrimary = in_array($route->primary_model_id, $deprecatedModelIds);
                    $isFallback = in_array($route->fallback_model_id, $deprecatedModelIds);

                    $brokenRoutes[] = [
                        'task' => $route->task_name,
                        'type' => $isPrimary ? 'primary' : 'fallback',
                        'model_name' => $isPrimary ? ($route->primaryModel?->name ?? 'N/A') : ($route->fallbackModel?->name ?? 'N/A'),
                    ];

                    Log::warning("OpenRouterSync: La ruta '{$route->task_name}' utiliza un modelo descatalogado.", [
                        'type' => $isPrimary ? 'primary' : 'fallback',
                        'model_id' => $isPrimary ? $route->primary_model_id : $route->fallback_model_id,
                    ]);
                }
            }

            return [
                'success' => true,
                'total_remote' => count($remoteModels),
                'updated' => $updatedCount,
                'added_free' => $addedFreeCount,
                'deprecated' => $deprecatedCount,
                'broken_routes' => $brokenRoutes,
                'message' => "Catálogo sincronizado con éxito. {$updatedCount} modelos actualizados, {$addedFreeCount} nuevos gratuitos añadidos, {$deprecatedCount} descatalogados."
            ];

        } catch (\Exception $e) {
            Log::error("OpenRouterSyncService: Error en sincronización: " . $e->getMessage());

            return [
                'success' => false,
                'total_remote' => 0,
                'updated' => 0,
                'deprecated' => 0,
                'broken_routes' => [],
                'message' => "Error al sincronizar con OpenRouter: " . $e->getMessage()
            ];
        }
    }
}
