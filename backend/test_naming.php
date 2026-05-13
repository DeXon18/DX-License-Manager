<?php

require __DIR__ . '/vendor/autoload.php';

use App\Services\Licensing\NXSuiteService;
use App\Services\Licensing\StarCcmService;
use App\Services\Licensing\HeedsService;

function test($title, $service, $content) {
    echo "--- $title ---\n";
    $metadata = $service->extractMetadata($content);
    $filename = $service->generateFilename($metadata);
    echo "Metadata Version: " . ($metadata['version'] ?? 'N/A') . "\n";
    echo "Filename: $filename\n\n";
}

$nxContent = <<<EOD
# Sold-To/Install: 10109274
# Customer Name: WALTER PACK SL
# Created: 03-05-2026
SERVER YourHostname ANY 28000
VENDOR ugslmd
INCREMENT server_id ugslmd 2025.12 14-may-2026 10
EOD;

$starContent = <<<EOD
# Sold-To/Install: 10301380
# Customer Name: UPM
# Version: 4.36
SERVER srv-star ANY 28000
VENDOR cdlmd
INCREMENT DOEtoken cdlmd 2029.03 12-mar-2026 500
EOD;

$heedsContent = <<<EOD
# Sold-To/Install: 12345678
# Other Installs: 87654321
# Customer Name: HEEDS CLIENT
# Version: 2025.10
SERVER ANY ANY 28000
VENDOR RCTECH
INCREMENT heeds RCTECH 2025.10 20-dec-2026 1
EOD;

test('NX (YY.MM)', new NXSuiteService(), $nxContent);
test('StarCCM (Keep Dots)', new StarCcmService(), $starContent);
test('Heeds (Unificada + YY.MM)', new HeedsService(), $heedsContent);
