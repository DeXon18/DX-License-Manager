<?php

require __DIR__ . '/../../backend/vendor/autoload.php';

use App\Services\Licensing\NXSuiteService;

$service = new NXSuiteService();

$content = <<<EOD
SERVER YourHostname ANY 28000
VENDOR ugslmd
INCREMENT server_id ugslmd 2025.12 permanent 1 \
        VENDOR_STRING="10109274 - WALTER PACK SL" user_info=DV2VCQJYF3 \
        ISSUER=SIEMENS BORROW=2880 SIGN="0007 F6CE D2C6 36B0 E472 FCF0 \
        E623 8543 4A7A A87B 2958 AFAD 8F5A 5BEA BC78 0498 7230 648A \
        185A F37A F594 690F 9398 37BB 797C 58F3 1963 FAA9 2892 7654"
EOD;

echo "--- ORIGINAL ---\n";
echo $content . "\n\n";

echo "--- TRANSFORMED (SALT, Temporal) ---\n";
$transformed = $service->transform($content, 'salt', true);
echo $transformed . "\n";
