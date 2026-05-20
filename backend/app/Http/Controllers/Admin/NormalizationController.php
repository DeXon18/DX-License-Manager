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
use Illuminate\Support\Facades\Cache;

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

        $allClients = Client::orderBy('name', 'asc')->get(['id', 'name']);
        $scannedDuplicates = Cache::remember('dx_scanned_duplicates', 86400, function() {
            return $this->detectDuplicates();
        });
 
        return view('admin.normalization.index', compact('findings', 'allClients', 'scannedDuplicates'));
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

        $detectedInput = trim($request->detected_name);
        $suggestedInput = trim($request->suggested_name);

        // 1. Find the "real" client
        $realClient = null;
        if (preg_match('/\(ID:\s*(\d+)\)/i', $suggestedInput, $matches)) {
            $realClient = Client::find($matches[1]);
        }
        if (!$realClient) {
            $suggestedNameClean = preg_replace('/\s*\(ID:\s*\d+\)/i', '', $suggestedInput);
            $realClient = Client::where('name', trim($suggestedNameClean))->first();
        }

        if (!$realClient) {
            return redirect()->back()->with('error', "El cliente sugerido '$suggestedInput' no existe.");
        }

        // 2. Find the "duplicate" client (if created)
        $duplicateClient = null;
        if (preg_match('/\(ID:\s*(\d+)\)/i', $detectedInput, $matches)) {
            $duplicateClient = Client::find($matches[1]);
        }
        if (!$duplicateClient) {
            $detectedNameClean = preg_replace('/\s*\(ID:\s*\d+\)/i', '', $detectedInput);
            $duplicateClient = Client::where('name', trim($detectedNameClean))->first();
        }

        // Clean names for alias creation
        $detectedName = $duplicateClient ? $duplicateClient->name : trim(preg_replace('/\s*\(ID:\s*\d+\)/i', '', $detectedInput));
        $suggestedName = $realClient->name;

        if (($duplicateClient && $realClient->id === $duplicateClient->id) || $detectedName === $suggestedName) {
            return redirect()->back()->with('error', "No se puede unificar un cliente consigo mismo.");
        }

        DB::beginTransaction();
        try {
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
            Cache::forget('dx_scanned_duplicates');
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

        Cache::forget('dx_scanned_duplicates');

        return redirect()->back()->with('info', "El cliente '{$request->detected_name}' ha sido descartado y no volverá a aparecer en la bandeja.");
    }

    /**
     * AJAX endpoint to run semantic AI check on a candidate pair.
     */
    public function analyzeAi(Request $request)
    {
        $request->validate([
            'client1' => 'required|string',
            'client2' => 'required|string',
        ]);

        $service = new \App\Services\AI\ClientAiNormalizationService();
        $result = $service->evaluateDuplicatePair($request->client1, $request->client2);

        return response()->json($result);
    }

    /**
     * Clear client duplicates cache and force a complete database scan.
     */
    public function forceScan()
    {
        Cache::forget('dx_scanned_duplicates');

        // Warm cache by immediately running the detection
        $this->detectDuplicates();

        return redirect()->route('admin.normalization.index')
            ->with('success', 'El escáner de duplicados de la base de datos se ha completado y guardado en caché con éxito.');
    }

    /**
     * Algoritmo de detección de duplicados en base de datos.
     */
    private function detectDuplicates()
    {
        $clients = Client::orderBy('name', 'asc')->get();
        $suspicions = [];

        $ignoredNames = NormalizationDecision::where('decision', 'ignore')
            ->pluck('detected_name')
            ->toArray();

        // FIX BUG #1 — Patrón expandido con todos los prefijos sectoriales relevantes
        $genericPattern = '/\b('
            // Metalmecánica
            . 'talleres|taller|mecanizados|mecanizaciones|mecanicos|mecanica'
            . '|metalicas|metalurgica|metalurgicas|metalmecanica'
            . '|fundicion|fundiciones|forjas|estampaciones|laminados|matriceria|troqueleria'
            // Industria general
            . '|industrias|industria|industrial|industriales'
            . '|fabricacion|produccion|instalaciones|manufacturas'
            // Tecnología / ingeniería
            . '|tecnologias|tecnologia|sistemas|ingenieria|engineering'
            . '|electronica|electronico|automatizacion'
            . '|soluciones|solucions|consulting|consultores|desarrollos'
            // Química / plásticos
            . '|quimicas|quimica|plasticos|plastico|polimeros'
            // Logística / transporte
            . '|logistica|transporte|transportes|distribuciones'
            // Educación / investigación
            . '|universidad|escuela|tecnica|superior|politecnica|politecnico|laboral'
            . '|instituto|centro|facultad|fundacion|asociacion|cooperativa'
            // Genéricos de empresa
            . '|comercial|promociones|construcciones|construccion'
            . '|servicios|services|grupo|group|internacional|nacional'
            . ')\b/i';

        // Sufijos societarios a eliminar
        $suffixPattern = '/\b(sl|sa|slu|sll|s\.l|s\.a|s\.l\.u|scoop|sau'
            . '|limitada|limited|ltd|gmbh|co|corp|inc'
            . '|solutions|espanola|espana|spain'
            . ')\b/i';

        $count = count($clients);
        for ($i = 0; $i < $count; $i++) {
            $c1 = $clients[$i];
            if (in_array($c1->name, $ignoredNames)) continue;

            $n1     = strtolower(trim(preg_replace('/\s+/', ' ', preg_replace('/[^a-z0-9 ]/i', ' ', $c1->name))));
            $clean1 = trim(preg_replace('/\s+/', ' ', preg_replace($suffixPattern, '', $n1)));
            $ultra1 = trim(preg_replace('/\s+/', ' ', preg_replace($genericPattern, '', $clean1)));

            // Guard: si ultraClean queda vacío tras depurar, usar clean como fallback
            if (strlen($ultra1) < 3) $ultra1 = $clean1;
            if (strlen($ultra1) < 3) continue;

            for ($j = $i + 1; $j < $count; $j++) {
                $c2 = $clients[$j];
                if (in_array($c2->name, $ignoredNames)) continue;

                $n2     = strtolower(trim(preg_replace('/\s+/', ' ', preg_replace('/[^a-z0-9 ]/i', ' ', $c2->name))));
                $clean2 = trim(preg_replace('/\s+/', ' ', preg_replace($suffixPattern, '', $n2)));
                $ultra2 = trim(preg_replace('/\s+/', ' ', preg_replace($genericPattern, '', $clean2)));

                // Guard para $ultra2
                if (strlen($ultra2) < 3) $ultra2 = $clean2;
                if (strlen($ultra2) < 3) continue;

                // Filtro rápido: primeros 4 chars del nombre distintivo deben coincidir
                $compareLen = min(4, min(strlen($ultra1), strlen($ultra2)));
                if (substr($ultra1, 0, $compareLen) !== substr($ultra2, 0, $compareLen)) {
                    continue;
                }

                // FIX BUG #2 — similar_text sobre las cadenas depuradas (ultraClean)
                similar_text($ultra1, $ultra2, $percent);

                if ($percent >= 70) {
                    $older = $c1->id < $c2->id ? $c1 : $c2;
                    $newer = $c1->id < $c2->id ? $c2 : $c1;

                    $suspicions[] = [
                        'duplicate'  => $newer,
                        'target'     => $older,
                        'similarity' => round($percent),
                        'reason'     => "Similitud del " . round($percent) . "% ('$ultra1' vs '$ultra2')",
                    ];
                }
            }
        }

        usort($suspicions, fn($a, $b) => $b['similarity'] <=> $a['similarity']);
        return $suspicions;
    }
}
