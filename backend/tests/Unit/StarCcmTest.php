<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\Licensing\StarCcmService;

class StarCcmTest extends TestCase
{
    protected $starService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->starService = new StarCcmService();
    }

    public function test_it_extracts_metadata_correctly()
    {
        $content = "# Sold-To/Install: 1905294\n" .
                   "# Customer Name: ESCUELA TECNICA SUPERIOR INGENIERIA\n" .
                   "# Version: 4.36\n" .
                   "SERVER DELL_LM 001d09116e14 1999\n" .
                   "VENDOR cdlmd\n" .
                   "INCREMENT DOEtoken cdlmd 2027.04 28-apr-2027 5000 SUPERSEDE";

        $metadata = $this->starService->extractMetadata($content);

        $this->assertEquals('1905294', $metadata['sold_to']);
        $this->assertEquals('ESCUELA TECNICA SUPERIOR INGENIERIA', $metadata['client']);
        $this->assertEquals('4.36', $metadata['version']);
        $this->assertEquals('DELL_LM', $metadata['hostname']);
        $this->assertEquals('001d09116e14', $metadata['hostid']);
        $this->assertEquals('Contractual', $metadata['type']);
    }

    public function test_it_detects_temporal_licenses()
    {
        $content = "SERVER ANY ANY 1999\n" .
                   "VENDOR cdlmd";

        $metadata = $this->starService->extractMetadata($content);
        $this->assertEquals('Temporal', $metadata['type']);
    }

    public function test_it_generates_correct_contractual_filename()
    {
        $metadata = [
            'sold_to'    => '1905294',
            'client'     => 'ESCUELA TECNICA',
            'hostname'   => 'DELL_LM',
            'version'    => '4.36',
            'expiration' => '07-may-2026',
            'type'       => 'Contractual'
        ];

        $filename = $this->starService->generateFilename($metadata);

        $this->assertEquals("1905294_DELL_LM_ESCUELA_TECNICA_STARCCM_V4.36_Valida_07-May-2026.lic", $filename);
    }

    public function test_it_generates_correct_temporal_filename()
    {
        $metadata = [
            'sold_to'    => '1905294',
            'client'     => 'ESCUELA TECNICA',
            'hostname'   => 'ANY',
            'version'    => '4.36',
            'expiration' => '07-may-2026',
            'type'       => 'Temporal'
        ];

        $filename = $this->starService->generateFilename($metadata);

        $this->assertEquals("1905294_ESCUELA_TECNICA_STARCCM_V4.36_TEMP_Valida_07-May-2026.lic", $filename);
    }

    public function test_it_transforms_to_salt_correctly()
    {
        $content = "SERVER myhost myid 1999\n" .
                   "VENDOR cdlmd\n" .
                   "INCREMENT feature cdlmd ...";

        $transformed = $this->starService->transform($content);

        $this->assertStringContainsString('SERVER myhost myid 29000', $transformed);
        $this->assertStringContainsString('VENDOR saltd saltd PORT=29001', $transformed);
    }
}
