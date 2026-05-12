<?php

use App\Services\AI\CompositeParserService;
use Illuminate\Support\Facades\Log;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$service = new CompositeParserService();
$testText = "COMPOSITE=5D1980276724 - Intel(R) Ethernet Connection (2) I219-LM (MAC : 84A93E9F0F54)";

echo "Probando Gemini con el texto de prueba...\n";
$result = $service->parse($testText);

if (isset($result['error'])) {
    echo "ERROR: " . $result['message'] . "\n";
} else {
    echo "ÉXITO:\n";
    print_r($result);
}
