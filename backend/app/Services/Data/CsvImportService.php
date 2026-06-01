<?php

namespace App\Services\Data;

use App\Models\Client;
use App\Models\Contract;
use App\Models\Vendor;
use App\Models\ImportLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CsvImportService
{
    protected $normalizationService;

    public function __construct(ClientNormalizationService $normalizationService)
    {
        $this->normalizationService = $normalizationService;
    }

    /**
     * Import contracts from a CSV file.
     * 
     * @param string $filePath
     * @param string $filename
     * @return array
     */
    public function import(string $filePath, string $filename): array
    {
        $handle = fopen($filePath, 'r');
        
        $log = ImportLog::create([
            'filename' => $filename,
            'status' => 'processing',
        ]);

        $processedIds = [];
        $rowCount = 0;
        $errors = [];
        $warnings = [];

        try {
            DB::beginTransaction();

            // 1. Detectar separador automáticamente
            $firstLine = fgets($handle);
            $separator = (str_contains($firstLine, ';')) ? ';' : ',';
            rewind($handle);

            // 2. Omitir BOM si existe
            $bom = fread($handle, 3);
            if ($bom !== "\xEF\xBB\xBF") {
                rewind($handle);
            }

            while (($row = fgetcsv($handle, 0, $separator)) !== false) {
                // Si es la primera fila, comprobar si es cabecera o dato
                if ($rowCount === 0) {
                    $firstCol = strtoupper(trim($row[0] ?? ''));
                    // Si NO contiene CONH, es una cabecera, la saltamos
                    if (!str_contains($firstCol, 'CONH')) {
                        $rowCount++;
                        continue;
                    }
                }

                $rowCount++;

                // Validar si la fila tiene datos mínimos (identificador CONH en col 1)
                $contractNumber = trim($row[0] ?? '');
                if (empty($contractNumber) || !str_contains(strtoupper($contractNumber), 'CONH')) {
                    continue;
                }
                
                try {
                    // 1. Normalize Client Name using the Intelligence Engine
                    // Bypass AI on bulk CSV import to prevent timeouts and rate limits (passes false as 3rd param)
                    $rawName = trim($row[2] ?? 'Desconocido');
                    $normalization = $this->normalizationService->resolve($rawName, 0.85, false);
                    
                    $clientId = $normalization['id'];

                    // Capturar advertencias (sospechas o nuevos clientes) para el log
                    if (isset($normalization['warning'])) {
                        $warnings[] = "Fila $rowCount: " . $normalization['warning'];
                    }

                    // 2. Find/Create Vendor
                    $vendorName = trim($row[3] ?? '');
                    $vendor = Vendor::firstOrCreate(['name' => $vendorName]);

                    // 3. Parse Date (Format d/m/Y)
                    $endDate = null;
                    if (!empty($row[6])) {
                        try {
                            $endDate = Carbon::createFromFormat('d/m/Y', trim($row[6]))->startOfDay();
                        } catch (\Exception $e) {
                            $errors[] = "Fila $rowCount: Formato de fecha inválido [{$row[6]}]";
                        }
                    }

                    // 4. Upsert Contract by contract_number
                    $contract = Contract::updateOrCreate(
                        ['contract_number' => trim($row[0])],
                        [
                            'client_id' => $clientId,
                            'vendor_id' => $vendor->id,
                            'cost_center' => $row[1] ?? null,
                            'type_product' => $row[4] ?? null,
                            'sub_product' => $row[5] ?? null,
                            'end_date' => $endDate,
                            'status' => isset($row[7]) ? trim($row[7]) : null,
                            'comment' => isset($row[8]) ? trim($row[8]) : null,
                        ]
                    );

                    $processedIds[] = $contract->id;

                } catch (\Exception $e) {
                    $errors[] = "Fila $rowCount: " . $e->getMessage();
                }
            }

            // 5. "Logic for Baja": contracts not present in this CSV are marked as Baja
            if (!empty($processedIds)) {
                Contract::whereNotIn('id', $processedIds)
                    ->where('status', '!=', 'Baja')
                    ->update(['status' => 'Baja']);
            }

            DB::commit();

            $log->update([
                'status' => count($errors) > 0 ? 'partial' : 'success',
                'total_rows' => $rowCount,
                'processed_rows' => count($processedIds),
                'errors' => $errors,
                'warnings' => $warnings,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            $log->update([
                'status' => 'failed',
                'errors' => [$e->getMessage()],
            ]);
            throw $e;
        } finally {
            fclose($handle);
        }

        return [
            'log_id' => $log->id,
            'total' => $rowCount,
            'processed' => count($processedIds),
            'errors' => $errors,
            'warnings' => $warnings
        ];
    }
}

