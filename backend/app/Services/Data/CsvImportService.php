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
        
        // Skip BOM if present
        if (fgets($handle, 4) !== "\xef\xbb\xbf") {
            rewind($handle);
        }

        $headers = fgetcsv($handle, 0, ';');
        
        $log = ImportLog::create([
            'filename' => $filename,
            'status' => 'processing',
        ]);

        $processedIds = [];
        $rowCount = 0;
        $errors = [];

        try {
            DB::beginTransaction();

            while (($row = fgetcsv($handle, 0, ';')) !== false) {
                // Basic validation: row must have at least the contract number and client
                if (empty($row[0]) || empty($row[2])) continue;

                $rowCount++;
                
                try {
                    // 1. Normalize Client Name (Title Case)
                    $clientName = Str::title(trim($row[2]));
                    $client = Client::firstOrCreate(['name' => $clientName]);

                    // 2. Find/Create Vendor
                    $vendorName = trim($row[3]);
                    $vendor = Vendor::firstOrCreate(['name' => $vendorName]);

                    // 3. Parse Date (Format d/m/Y)
                    $endDate = null;
                    if (!empty($row[5])) {
                        try {
                            $endDate = Carbon::createFromFormat('d/m/Y', trim($row[5]))->startOfDay();
                        } catch (\Exception $e) {
                            $errors[] = "Fila $rowCount: Formato de fecha inválido [{$row[5]}]";
                        }
                    }

                    // 4. Upsert Contract by contract_number
                    $contract = Contract::updateOrCreate(
                        ['contract_number' => trim($row[0])],
                        [
                            'client_id' => $client->id,
                            'vendor_id' => $vendor->id,
                            'cost_center' => $row[1] ?? null,
                            'type_product' => $row[4] ?? null,
                            'end_date' => $endDate,
                            'status' => $row[6] ?? null,
                            'comment' => $row[7] ?? null,
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
            'total' => $rowCount,
            'processed' => count($processedIds),
            'errors' => $errors
        ];
    }
}
