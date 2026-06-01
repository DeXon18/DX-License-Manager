<?php

namespace App\Services\Data;

use App\Models\Client;
use App\Models\ClientAlias;
use Illuminate\Support\Str;

class ClientNormalizationService
{
    /**
     * Normalize a client name and find its ID.
     *
     * @param string $name
     * @param float $threshold Similarity threshold (0 to 1)
     * @return array [id, status, name, suggested_name, similarity, warning]
     */
    public function resolve(string $name, float $threshold = 0.85, bool $useAi = true): array
    {
        $name = trim($name);
        $titleName = Str::title($name);

        // 1. Exact Match in Clients
        $client = Client::where('name', $titleName)->first();
        if ($client) {
            return ['id' => $client->id, 'status' => 'exact', 'name' => $client->name];
        }

        // 2. Exact Match in Aliases
        $alias = ClientAlias::where('name', $titleName)->first();
        if ($alias) {
            return ['id' => $alias->client_id, 'status' => 'alias', 'name' => $alias->client->name];
        }

        // 3. Fuzzy Match
        $allClients = Client::all(['id', 'name']);
        $bestMatch = null;
        $highestSimilarity = 0;

        foreach ($allClients as $c) {
            $similarity = $this->calculateSimilarity($titleName, $c->name);
            if ($similarity > $highestSimilarity) {
                $highestSimilarity = $similarity;
                $bestMatch = $c;
            }
        }

        // If similarity is high, we consider it a suspicion
        if ($highestSimilarity >= $threshold) {
            $warning = "El cliente '{$titleName}' se parece un " . round($highestSimilarity * 100, 2) . "% a '{$bestMatch->name}'. Se ha creado un nuevo cliente por precaución, revisar posibles duplicados.";
            
            // Create the new client anyway for immediate use, but flag it
            $newClient = Client::create(['name' => $titleName]);

            return [
                'id' => $newClient->id, 
                'status' => 'suspicion', 
                'name' => $newClient->name,
                'suggested_id' => $bestMatch->id,
                'suggested_name' => $bestMatch->name,
                'similarity' => round($highestSimilarity * 100, 2),
                'warning' => $warning
            ];
        }

        // 3.5. Fallback a Inteligencia Artificial (Gemini/DeepSeek/OpenRouter)
        if ($useAi) {
        try {
            $aiService = app(\App\Services\AI\ClientAiNormalizationService::class);
            $aiResult = $aiService->evaluate($titleName);
            
            if ($aiResult['matched'] && $aiResult['confidence'] >= 0.8 && $aiResult['matched_id']) {
                $suggestedClient = Client::find($aiResult['matched_id']);
                if ($suggestedClient) {
                    $providerName = strtoupper($aiResult['provider'] ?? 'IA');
                    $confidencePct = round($aiResult['confidence'] * 100, 2);
                    $reason = $aiResult['reason'] ?? 'Coincidencia por inteligencia artificial.';
                    
                    // Formato compatible con el parseador regex: /El cliente \'(.*)\' se parece un .* a \'(.*)\'/i
                    $warning = "El cliente '{$titleName}' se parece un {$confidencePct}% ({$providerName}) a '{$suggestedClient->name}'. Razón: {$reason} Se ha creado un nuevo cliente por precaución, revisar posibles duplicados.";
                    
                    $newClient = Client::create(['name' => $titleName]);
                    
                    return [
                        'id' => $newClient->id,
                        'status' => 'suspicion',
                        'name' => $newClient->name,
                        'suggested_id' => $suggestedClient->id,
                        'suggested_name' => $suggestedClient->name,
                        'similarity' => $confidencePct,
                        'warning' => $warning
                    ];
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("ClientNormalizationService AI fallback error: " . $e->getMessage());
        }
        } // End if ($useAi)

        // 4. Totally New (No similarity or very low)
        $newClient = Client::create(['name' => $titleName]);
        $warning = "Nuevo cliente registrado: {$titleName}";
        
        return [
            'id' => $newClient->id, 
            'status' => 'new', 
            'name' => $newClient->name,
            'warning' => $warning
        ];
    }

    /**
     * Calculate similarity between two strings (0 to 1).
     */
    private function calculateSimilarity(string $str1, string $str2): float
    {
        $str1 = mb_strtolower($str1);
        $str2 = mb_strtolower($str2);
        
        if ($str1 === $str2) return 1.0;

        $len1 = mb_strlen($str1);
        $len2 = mb_strlen($str2);
        $maxLen = max($len1, $len2);

        if ($maxLen === 0) return 1.0;

        $distance = levenshtein($str1, $str2);
        return 1 - ($distance / $maxLen);
    }
}
