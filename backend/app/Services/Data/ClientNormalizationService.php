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
     * @return array [id, status, suggestion]
     */
    public function findOrCreate(string $name, float $threshold = 0.85): array
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

        // If similarity is very high, we consider it a suspicion
        if ($highestSimilarity >= $threshold) {
            return [
                'id' => null, 
                'status' => 'suspicion', 
                'suggested_id' => $bestMatch->id,
                'suggested_name' => $bestMatch->name,
                'similarity' => round($highestSimilarity * 100, 2),
                'original_name' => $titleName
            ];
        }

        // 4. Totally New (No similarity or very low)
        // For now, we create it automatically but mark it as new
        $newClient = Client::create(['name' => $titleName]);
        return ['id' => $newClient->id, 'status' => 'new', 'name' => $newClient->name];
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
