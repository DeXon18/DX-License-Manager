<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CompositeParserService
{
    /**
     * Procesa el texto de adaptadores usando Gemini.
     */
    public function parse(string $text): array
    {
        $prompt = <<<EOT
Dado el siguiente listado de CIDs/MACs de adaptadores de red de un host 
con licencias Siemens PLM Software, selecciona el más adecuado para 
asignar la licencia.

Criterio de selección (en orden de prioridad):
1. Adaptador Ethernet físico activo (ej: Intel, Realtek, Broadcom)
2. Adaptador Wi-Fi físico, si no hay Ethernet disponible
3. Nunca seleccionar: VPN, Bluetooth, Wi-Fi Direct, Mobile Broadband, 
   adaptadores virtuales (VMware, Hyper-V, Barracuda, etc.)

Devuelve SOLO esto en formato JSON:
{
  "hostname": "<extraído del texto si existe>",
  "composite": "<valor>",
  "mac": "<valor>",
  "adapter": "<nombre del adaptador>",
  "reason": "<una línea explicando por qué>"
}

Lista de adaptadores:
$text
EOT;

        try {
            $apiKey = config('ai.gemini_key');
            
            if (!$apiKey) {
                throw new \Exception("GEMINI_API_KEY no configurada.");
            }

            $response = Http::timeout(30)->post(
                "https://generativelanguage.googleapis.com/v1/models/gemini-2.0-flash:generateContent?key={$apiKey}",
                [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'response_mime_type' => 'application/json',
                    ]
                ]
            );

            if (!$response->successful()) {
                throw new \Exception("Error en API Gemini: " . $response->body());
            }

            $data = $response->json();
            $content = $data['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
            
            // Limpiar posibles bloques markdown si Gemini los incluye
            $content = preg_replace('/```json\s*|```/i', '', $content);
            
            return json_decode($content, true) ?: [];

        } catch (\Exception $e) {
            Log::error("CompositeParserService Error: " . $e->getMessage());
            return [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }
    }
}
