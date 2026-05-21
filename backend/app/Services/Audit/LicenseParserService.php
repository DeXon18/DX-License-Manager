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
        // 1. Normalizar saltos de línea sin crear arrays masivos todavía
        $content = str_replace(["\r\n", "\r"], "\n", $content);

        // 2. Extraer Resumen Comercial de forma eficiente (buscando desde el final)
        $summaryTable = '';
        $lastHashPos = strrpos($content, 'LICENSE PRODUCT');
        if ($lastHashPos !== false) {
            $summaryPart = substr($content, $lastHashPos);
            $lines = explode("\n", $summaryPart);
            foreach ($lines as $line) {
                if (str_starts_with(trim($line), '#')) {
                    $summaryTable .= trim($line) . "\n";
                }
            }
        }

        // 3. Limpieza de firmas y continuaciones con límites de repetición para evitar backtracking
        // Usamos una aproximación más sencilla: eliminar bloques SIGN hasta el final de la comilla
        $content = preg_replace('/SIGN\s*=\s*"[^"]*"/s', 'SIGN="..."', $content);
        
        // 4. Filtrar el cuerpo manteniendo solo líneas críticas (Procesamiento por líneas)
        $allLines = explode("\n", $content);
        $totalLines = count($allLines);
        
        // Mantener las primeras 100 líneas (Header completo)
        $headerLines = array_slice($allLines, 0, min(100, $totalLines));
        
        // Filtrar el resto (solo palabras clave)
        $filteredBody = [];
        for ($i = 100; $i < $totalLines; $i++) {
            $line = trim($allLines[$i]);
            if (empty($line) || str_starts_with($line, '#')) continue;
            
            $upperLine = strtoupper($line);
            if (str_starts_with($upperLine, 'SERVER') || 
                str_starts_with($upperLine, 'VENDOR') || 
                str_starts_with($upperLine, 'INCREMENT') || 
                str_starts_with($upperLine, 'PACKAGE')) {
                $filteredBody[] = $line;
            }
        }

        // 5. Re-ensamblar
        $result = implode("\n", $headerLines) . "\n"
                . implode("\n", $filteredBody) . "\n"
                . "### RESUMEN COMERCIAL DETECTADO ###\n"
                . $summaryTable;

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
