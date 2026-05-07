<?php

namespace App\Services\AI;

use App\Models\AiAuditResult;
use App\Models\ClientMapping;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AuditService
{
    /**
     * Envía una solicitud de auditoría al motor n8n.
     */
    public function requestAudit(int $userId, string $licenseText, array $detectedHostIds, string $vendor = 'siemens')
    {
        $uuid = (string) Str::uuid();

        // Crear registro en estado procesamiento
        $audit = AiAuditResult::create([
            'uuid' => $uuid,
            'user_id' => $userId,
            'vendor' => $vendor,
            'status' => 'processing',
        ]);

        try {
            $response = Http::timeout(30)->post(config('ai.n8n_webhook_url'), [
                'uuid' => $uuid,
                'license_text' => $licenseText,
                'php_detected_host_ids' => $detectedHostIds,
                'callback_url' => config('ai.callback_url'),
            ]);

            if (!$response->successful()) {
                throw new \Exception("n8n returned error: " . $response->status());
            }

            return $audit;

        } catch (\Exception $e) {
            Log::error("Audit request failed: " . $e->getMessage());
            $audit->update(['status' => 'failed']);
            return $audit;
        }
    }

    /**
     * Procesa el callback recibido de n8n.
     */
    public function handleCallback(array $data)
    {
        $uuid = $data['uuid'] ?? null;
        if (!$uuid) return false;

        $audit = AiAuditResult::where('uuid', $uuid)->first();
        if (!$audit) return false;

        // Intentar vincular cliente si no está vinculado
        $clientId = $audit->client_id;
        $soldTo = $data['sold_to'] ?? null;
        $customerName = $data['customer_name'] ?? null;

        if (!$clientId && $soldTo) {
            // 1. Buscar por mapeo existente
            $mapping = ClientMapping::where('sold_to', $soldTo)->first();
            if ($mapping) {
                $clientId = $mapping->client_id;
            } 
            // 2. Si no hay mapeo, buscar por nombre (Fuzzy match simple o exacto)
            elseif ($customerName) {
                $client = \App\Models\Client::where('name', 'LIKE', '%' . $customerName . '%')->first();
                if ($client) {
                    $clientId = $client->id;
                    // Auto-crear mapeo para el futuro
                    ClientMapping::create([
                        'client_id' => $clientId,
                        'sold_to' => $soldTo,
                        'vendor' => $audit->vendor,
                    ]);
                }
            }
        }

        $audit->update([
            'client_id' => $clientId,
            'sold_to' => $soldTo,
            'customer_name' => $customerName,
            'results' => $data, // Guardamos todo el JSON para la UI
            'status' => 'completed',
        ]);

        // Sincronizar Inventario Activo
        try {
            app(InventorySyncService::class)->syncFromResult($audit);
        } catch (\Exception $e) {
            Log::error("Error sincronizando inventario: " . $e->getMessage());
        }

        return true;
    }
}
