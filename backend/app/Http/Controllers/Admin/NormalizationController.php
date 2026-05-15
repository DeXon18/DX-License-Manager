<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ImportLog;
use App\Models\Client;
use App\Models\ClientAlias;
use App\Models\Contract;
use App\Models\AiAuditResult;
use App\Models\NormalizationDecision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NormalizationController extends Controller
{
    /**
     * List all pending normalization warnings from import logs and audits.
     */
    public function index()
    {
        // 1. Get CSV Import warnings
        $csvLogs = ImportLog::whereNotNull('warnings')
            ->orderBy('created_at', 'desc')
            ->get();

        // 2. Get AI Audit warnings (Licensing)
        $auditLogs = AiAuditResult::whereNotNull('warnings')
            ->orderBy('created_at', 'desc')
            ->get();

        // 3. Get all ignored names
        $ignoredNames = NormalizationDecision::where('decision', 'ignore')
            ->pluck('detected_name')
            ->toArray();

        $findings = [];

        // Process CSV Warnings
        foreach ($csvLogs as $log) {
            $findings = array_merge($findings, $this->parseWarnings($log->warnings, $log->filename, $log->created_at, 'CSV', $ignoredNames));
        }

        // Process Audit Warnings
        foreach ($auditLogs as $audit) {
            $filename = ($audit->results['filename'] ?? 'Licencia') . " ({$audit->sold_to})";
            $findings = array_merge($findings, $this->parseWarnings($audit->warnings, $filename, $audit->created_at, 'Auditoría', $ignoredNames));
        }

        // Sort by date desc
        usort($findings, function($a, $b) {
            return $b['date'] <=> $a['date'];
        });

        return view('admin.normalization.index', compact('findings'));
    }

    /**
     * Helper to parse warnings and filter out resolved/ignored.
     */
    private function parseWarnings($warnings, $sourceName, $date, $sourceType, $ignoredNames)
    {
        $parsed = [];
        foreach ($warnings as $warning) {
            $isSuspicion = str_contains(strtolower($warning), 'sospecha') || str_contains(strtolower($warning), 'parece');
            $isNew = str_contains(strtolower($warning), 'nuevo cliente');

            if ($isSuspicion || $isNew) {
                if ($isSuspicion) {
                    preg_match('/El cliente \'(.*)\' se parece un .* a \'(.*)\'/i', $warning, $matches);
                    $detectedName = $matches[1] ?? 'Error al extraer';
                    $suggestedName = $matches[2] ?? null;
                } elseif ($isNew) {
                    preg_match('/registrado: (.*)/i', $warning, $matches);
                    $detectedName = $matches[1] ?? 'Nuevo Cliente';
                    $suggestedName = null;
                }

                $detectedName = trim($detectedName);

                if (ClientAlias::where('name', $detectedName)->exists() || in_array($detectedName, $ignoredNames)) {
                    continue;
                }

                $parsed[] = [
                    'filename' => $sourceName,
                    'source_type' => $sourceType,
                    'date' => $date,
                    'type' => $isSuspicion ? 'suspicion' : 'new',
                    'detected_name' => $detectedName,
                    'suggested_name' => $suggestedName ? trim($suggestedName) : null,
                    'full_message' => $warning
                ];
            }
        }
        return $parsed;
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

            // 4. Migrate everything if duplicate exists
            if ($duplicateClient && $duplicateClient->id !== $realClient->id) {
                // Migrate Contracts
                Contract::where('client_id', $duplicateClient->id)
                    ->update(['client_id' => $realClient->id]);

                // Migrate Licenses & Inventory
                AiAuditResult::where('client_id', $duplicateClient->id)
                    ->update(['client_id' => $realClient->id]);
                
                \App\Models\LicenseInventoryDaemon::where('client_id', $duplicateClient->id)
                    ->update(['client_id' => $realClient->id]);

                // Migrate Contacts & Certificates
                \App\Models\Contact::where('client_id', $duplicateClient->id)
                    ->update(['client_id' => $realClient->id]);
                
                \App\Models\Certificate::where('client_id', $duplicateClient->id)
                    ->update(['client_id' => $realClient->id]);
                
                // 5. Delete duplicate client
                $duplicateClient->delete();
            }

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
        $request->validate([
            'detected_name' => 'required|string',
        ]);

        NormalizationDecision::updateOrCreate(
            ['detected_name' => $request->detected_name],
            ['decision' => 'ignore']
        );

        return redirect()->back()->with('info', "El cliente '{$request->detected_name}' ha sido descartado y no volverá a aparecer en la bandeja.");
    }
}
