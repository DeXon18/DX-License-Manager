<?php

namespace Tests\Unit;

use App\Services\Licensing\NXSuiteService;
use Tests\TestCase;

class NXSuiteMechanismTest extends TestCase
{
    protected $nxService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->nxService = new NXSuiteService();
    }

    /** @test */
    public function it_transforms_legacy_motor_correctly()
    {
        $content = "SERVER YourHostname ANY 28000\nVENDOR ugslmd";
        $transformed = $this->nxService->transform($content, 'legacy', true);

        $this->assertStringContainsString('SERVER localhost ANY 28000', $transformed);
        $this->assertStringContainsString('VENDOR ugslmd', $transformed);
    }

    /** @test */
    public function it_transforms_salt_motor_correctly()
    {
        $content = "SERVER Host1 COMPOSITE=X 28000\nVENDOR ugslmd";
        $transformed = $this->nxService->transform($content, 'salt');

        $this->assertStringContainsString('SERVER Host1 COMPOSITE=X 29000', $transformed);
        $this->assertStringContainsString('VENDOR saltd saltd PORT=29001', $transformed);
    }

    /** @test */
    public function it_detects_dongle_license_correctly()
    {
        $content = "FEATURE NX41000 ugslmd 2025.12 04-may-2027 uncounted HOSTID=UG_HWKEY_ID=24141";
        $type = $this->nxService->detectType($content);

        $this->assertEquals('Dongle', $type);
    }

    /** @test */
    public function it_generates_correct_filename_for_dongle()
    {
        $metadata = [
            'sold_to' => '123456',
            'client'  => 'Test Client',
            'version' => 'V1',
            'date'    => '20260507',
            'type'    => 'Dongle'
        ];
        $filename = $this->nxService->generateFilename($metadata);
        $expectedDate = date('dmY');

        $this->assertEquals("123456_TEST-CLIENT_V1_DongleUSB_Valida_{$expectedDate}.lic", $filename);
    }

    /** @test */
    public function it_generates_correct_filename_for_temporal()
    {
        $metadata = [
            'sold_to'  => '123456',
            'hostname' => 'localhost',
            'client'   => 'Test Client',
            'version'  => 'V1',
            'date'     => '20260507',
            'type'     => 'Temporal'
        ];
        $filename = $this->nxService->generateFilename($metadata);
        $expectedDate = date('dmY');

        // Sin hostname en temporales: SOLDTO_CLIENTE_VERSION_TEMP_Valida_FECHA.lic
        $this->assertEquals("123456_TEST-CLIENT_V1_TEMP_Valida_{$expectedDate}.lic", $filename);
    }
}
