<?php

namespace App\Services\Audit;

/**
 * LicenseParserService
 * 
 * Se encarga de limpiar y pre-procesar archivos de licencia de Siemens (NX, StarCCM, etc.)
 * para reducir el tamaño del payload y optimizar el contexto enviado a una IA (n8n).
 */
class LicenseParserService
{
    /**
     * Limpia el contenido de la licencia eliminando firmas y bloques redundantes.
     * 
     * @param string $content Contenido original del archivo
     * @return string Contenido optimizado
     */
    public function clean(string $content): string
    {
        // 0. Extraer el Resumen Comercial ANTES de cualquier transformación
        // (está al final del archivo como comentarios #)
        $summaryTable = '';
        $rawLines = preg_split("/\r?\n/", $content);
        $foundSummary = false;
        foreach ($rawLines as $line) {
            if (str_contains(strtoupper($line), 'LICENSE PRODUCT')) {
                $foundSummary = true;
            }
            if ($foundSummary && str_starts_with(trim($line), '#')) {
                $summaryTable .= trim($line) . "\n";
            }
        }

        // 1. Normalizar saltos de línea a LF simple
        $content = str_replace("\r\n", "\n", $content);
        $content = str_replace("\r", "\n", $content);

        // 2. Unificar líneas de continuación FlexLM (barra invertida al final)
        // Patrón: backslash \ seguido de newline y espacios opcionales → un espacio
        $content = preg_replace('/\\\\\n\s*/u', ' ', $content);

        // 3. Eliminar firmas digitales (SIGN="...") — pueden ser muy largas
        $content = preg_replace('/SIGN\s*=\s*"[^"]*"/s', '', $content);
        $content = preg_replace('/SIGN\s*=\s*"[^"]*$/m', '', $content);

        // 4. Filtrar el cuerpo manteniendo solo líneas críticas
        $lines = explode("\n", $content);
        $header = array_slice($lines, 0, 60);
        $body   = array_slice($lines, 60);

        $filteredBody = array_filter($body, function($line) {
            $line = trim($line);
            if (empty($line)) return false;
            if (str_starts_with($line, '#')) return false;

            $keywords = ['SERVER', 'VENDOR', 'INCREMENT', 'PACKAGE', 'FEATURE'];
            foreach ($keywords as $kw) {
                if (str_starts_with(strtoupper($line), $kw)) return true;
            }
            return false;
        });

        // 5. Re-ensamblar con el resumen comercial al final
        $result = implode("\n", $header) . "\n"
                . implode("\n", $filteredBody) . "\n"
                . "### RESUMEN COMERCIAL DETECTADO AL FINAL DEL ARCHIVO ###\n"
                . $summaryTable;

        // 6. Limpieza final
        $result = preg_replace("/[ \t]+/", " ", $result);
        $result = preg_replace("/\n\s*\n+/", "\n", $result);

        return trim($result);
    }

    /**
     * Detecta Host IDs en líneas INCREMENT para dar contexto a la IA.
     */
    public function detectHostIds(string $content): array
    {
        $ids = [];
        // Buscar patrones como HOSTID=d8bbc1a8e357 o ETHER=...
        if (preg_match_all('/(?:HOSTID|ETHER|ID)\s*=\s*([a-zA-Z0-9]+)/i', $content, $matches)) {
            $ids = array_unique($matches[1]);
        }
        return array_values($ids);
    }
}
