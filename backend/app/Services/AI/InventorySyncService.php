<?php

namespace App\Services\AI;

use App\Models\AiAuditResult;
use App\Models\LicenseInventoryDaemon;
use App\Models\LicenseInventoryProduct;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class InventorySyncService
{
    /**
     * Sincroniza el inventario activo a partir de un resultado de auditoría.
     */
    public function syncFromResult(AiAuditResult $audit)
    {
        $data = $audit->results;
        if (!$data || !$audit->client_id) {
            Log::info("InventorySync: Saltando sincronización, falta data o client_id.");
            return;
        }

        $soldTo = $data['sold_to'] ?? $audit->sold_to;
        $daemonName = $data['vendor_daemon'] ?? 'unknown';
        
        if (!$soldTo || $daemonName === 'unknown') {
            Log::warning("InventorySync: Missing sold_to or daemon for audit {$audit->uuid}");
            return;
        }

        // 1. Sincronizar el Daemon (Contenedor)
        $daemon = LicenseInventoryDaemon::updateOrCreate(
            [
                'client_id' => $audit->client_id,
                'sold_to' => $soldTo,
                'daemon' => $daemonName,
            ],
            [
                'hostname' => $data['server_hostname'] ?? null,
                'composite' => $data['server_host_id'] ?? null,
                'hardware_id' => $this->extractHardwareId($data),
                'version' => $data['version'] ?? null,
                'type' => $this->determineType($data),
            ]
        );

        // 2. Sincronizar Productos
        $products = $data['products'] ?? [];
        foreach ($products as $prodData) {
            if (empty($prodData['product_code'])) continue;

            $productCode = $prodData['product_code'];
            $hostId = $prodData['node_locked_host_id'] ?? null;

            LicenseInventoryProduct::updateOrCreate(
                [
                    'daemon_id' => $daemon->id,
                    'product_code' => $productCode,
                    'node_locked_host_id' => $hostId,
                ],
                [
                    'description' => $prodData['description'] ?? null,
                    'quantity' => $prodData['quantity'] ?? 1,
                    'expiration_date' => $this->parseDate($prodData['expiration_date'] ?? null),
                    'status' => 'active', // Siempre reactivamos si aparece en una nueva subida
                ]
            );
        }

        Log::info("InventorySync: Sincronización completada para Sold-To {$soldTo} / {$daemonName}");
    }

    /**
     * Extrae el ID de hardware si está presente en el host_id del servidor.
     */
    private function extractHardwareId(array $data)
    {
        $hostId = $data['server_host_id'] ?? '';
        if (str_contains($hostId, 'UG_HWKEY_ID=')) {
            return str_replace('UG_HWKEY_ID=', '', $hostId);
        }
        return null;
    }

    /**
     * Determina el tipo de licencia (floating, node-locked, dongle).
     */
    private function determineType(array $data)
    {
        $hostId = $data['server_host_id'] ?? '';
        if (str_contains($hostId, 'UG_HWKEY_ID=')) {
            return 'dongle';
        }
        
        $products = $data['products'] ?? [];
        foreach ($products as $p) {
            if (!empty($p['node_locked_host_id'])) {
                return 'node-locked';
            }
        }

        return 'floating';
    }

    /**
     * Parsea la fecha de expiración manejando casos especiales.
     */
    private function parseDate($dateStr)
    {
        if (!$dateStr) return null;
        
        $specialCases = ['permanent', '9999-12-31', 'any', 'uncounted'];
        if (in_array(strtolower($dateStr), $specialCases)) {
            return null;
        }

        try {
            return Carbon::parse($dateStr)->format('Y-m-d');
        } catch (\Exception $e) {
            Log::error("InventorySync: Error parseando fecha '{$dateStr}': " . $e->getMessage());
            return null;
        }
    }
}
