<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ImportLog;
use App\Models\Client;
use App\Models\ClientAlias;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NormalizationController extends Controller
{
    /**
     * List all pending normalization warnings from import logs.
     */
    public function index()
    {
        // Get logs with warnings
        $logs = ImportLog::whereNotNull('warnings')
            ->orderBy('created_at', 'desc')
            ->get();

        $findings = [];

        foreach ($logs as $log) {
            foreach ($log->warnings as $warning) {
                // Detect suspicions or new entries
                $isSuspicion = str_contains(strtolower($warning), 'sospecha') || str_contains(strtolower($warning), 'parece');
                $isNew = str_contains(strtolower($warning), 'nuevo cliente');

                if ($isSuspicion || $isNew) {
                    
                    // Try to extract names if it's a suspicion
                    $detectedName = 'Desconocido';
                    $suggestedName = null;

                    if ($isSuspicion) {
                        preg_match('/El cliente \'(.*)\' se parece un .* a \'(.*)\'/i', $warning, $matches);
                        $detectedName = $matches[1] ?? 'Error al extraer';
                        $suggestedName = $matches[2] ?? null;
                    } elseif ($isNew) {
                        preg_match('/registrado: (.*)/i', $warning, $matches);
                        $detectedName = $matches[1] ?? 'Nuevo Cliente';
                    }
                    
                    $findings[] = [
                        'log_id' => $log->id,
                        'filename' => $log->filename,
                        'date' => $log->created_at,
                        'type' => $isSuspicion ? 'suspicion' : 'new',
                        'detected_name' => trim($detectedName),
                        'suggested_name' => $suggestedName ? trim($suggestedName) : null,
                        'full_message' => $warning
                    ];
                }
            }
        }

        return view('admin.normalization.index', compact('findings'));
    }

    /**
     * Unify a detected name into an existing client.
     */
    public function unify(Request $request)
    {
        $request->validate([
            'detected_name' => 'required|string',
            'suggested_name' => 'required|string',
        ]);

        $detectedName = $request->detected_name;
        $suggestedName = $request->suggested_name;

        DB::beginTransaction();
        try {
            // 1. Find the "real" client
            $realClient = Client::where('name', $suggestedName)->first();
            if (!$realClient) {
                throw new \Exception("El cliente sugerido '$suggestedName' no existe.");
            }

            // 2. Find the "duplicate" client (if created)
            $duplicateClient = Client::where('name', $detectedName)->first();

            // 3. Create Alias
            ClientAlias::updateOrCreate(
                ['name' => $detectedName],
                ['client_id' => $realClient->id]
            );

            // 4. Migrate contracts if duplicate exists
            if ($duplicateClient && $duplicateClient->id !== $realClient->id) {
                Contract::where('client_id', $duplicateClient->id)
                    ->update(['client_id' => $realClient->id]);
                
                // 5. Delete duplicate client
                $duplicateClient->delete();
            }

            // 6. Clean up the warning from logs (Optional, for now we just show it's done)
            // In a more robust version, we would remove the warning from the JSON

            DB::commit();
            return redirect()->back()->with('success', "Cliente '$detectedName' unificado con éxito bajo '$suggestedName'.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', "Error al unificar: " . $e->getMessage());
        }
    }

    /**
     * Dismiss a normalization warning.
     */
    public function dismiss(Request $request)
    {
        // For now, dismissal is just symbolic as we are reading raw logs.
        // In the future, we could have a "normalization_ignored" table.
        return redirect()->back()->with('info', "Aviso descartado temporalmente (Lógica de persistencia en desarrollo).");
    }
}
