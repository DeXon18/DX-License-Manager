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
     * Sincroniza los modelos locales con la API pública de OpenRouter.
     *
     * @return array [
     *   'success' => bool,
     *   'total_remote' => int,
     *   'updated' => int,
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
                $routes = AiRoute::whereIn('primary_model_id', $deprecatedModelIds)
                    ->orWhereIn('fallback_model_id', $deprecatedModelIds)
                    ->get();

                foreach ($routes as $route) {
                    $isPrimary = in_array($route->primary_model_id, $deprecatedModelIds);
                    $isFallback = in_array($route->fallback_model_id, $deprecatedModelIds);

                    $brokenRoutes[] = [
                        'task' => $route->task_name,
                        'type' => $isPrimary ? 'primary' : 'fallback',
                        'model_name' => $isPrimary ? $route->primaryModel->name : $route->fallbackModel->name,
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
                'deprecated' => $deprecatedCount,
                'broken_routes' => $brokenRoutes,
                'message' => "Catálogo sincronizado con éxito. {$updatedCount} modelos actualizados, {$deprecatedCount} descatalogados."
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
