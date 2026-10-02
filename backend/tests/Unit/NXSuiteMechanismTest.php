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
    public function it_transforms_yourhostname_with_composite_to_localhost()
    {
        $content = "SERVER YourHostname COMPOSITE=2F1A76CA1F5C 28000\nVENDOR ugslmd";
        $transformed = $this->nxService->transform($content, 'legacy');

        $this->assertStringContainsString('SERVER localhost COMPOSITE=2F1A76CA1F5C 28000', $transformed);
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
            'sold_to'    => '123456',
            'client'     => 'Test Client',
            'version'    => 'V1',
            'expiration' => '07-may-2026',
            'type'       => 'Dongle'
        ];
        $filename = $this->nxService->generateFilename($metadata);

        $this->assertEquals("123456_TEST_CLIENT_V1_DongleUSB_Valida_07-May-2026.lic", $filename);
    }

    /** @test */
    public function it_generates_correct_filename_for_temporal()
    {
        $metadata = [
            'sold_to'    => '123456',
            'hostname'   => 'localhost',
            'client'     => 'Test Client',
            'version'    => 'V1',
            'expiration' => '07-may-2026',
            'type'       => 'Temporal'
        ];
        $filename = $this->nxService->generateFilename($metadata);

        // Sin hostname en temporales: SOLDTO_CLIENTE_VERSION_TEMP_Valida_FECHA.lic
        $this->assertEquals("123456_TEST_CLIENT_V1_TEMP_Valida_07-May-2026.lic", $filename);
    }

    /** @test */
    public function it_extracts_minimum_expiration_date_when_multiple_dates_exist()
    {
        $content = <<<LIC
SERVER srv1 COMPOSITE=1234567890AB 28000
VENDOR ugslmd
INCREMENT module_future ugslmd 2026.06 09-jun-2027 4 SUPERSEDE SIGN="ABC"
INCREMENT module_expiring_soon ugslmd 2026.06 29-jul-2026 1 SUPERSEDE SIGN="DEF"
INCREMENT module_permanent ugslmd 2026.06 permanent 1 SIGN="GHI"
LIC;

        $metadata = $this->nxService->extractMetadata($content);

        // Debe tomar la fecha mínima (29-jul-2026), no la primera (09-jun-2027) ni permanent
        $this->assertEquals('29-jul-2026', $metadata['expiration']);
    }

    /** @test */
    public function it_extracts_permanent_if_all_modules_are_permanent()
    {
        $content = <<<LIC
SERVER srv1 COMPOSITE=1234567890AB 28000
VENDOR ugslmd
INCREMENT module_a ugslmd 2026.06 permanent 1 SIGN="ABC"
INCREMENT module_b ugslmd 2026.06 permanent 1 SIGN="DEF"
LIC;

        $metadata = $this->nxService->extractMetadata($content);
        $this->assertEquals('permanent', $metadata['expiration']);
    }

    /** @test */
    public function it_extracts_29_jul_2026_as_minimum_date_for_goimek_license_type()
    {
        $content = <<<LIC
SERVER Giz-Datos COMPOSITE=CCB45DBBF408 29000
VENDOR saltd saltd PORT=29001
INCREMENT NCFOUND ugslmd 2026.06 09-jun-2027 4 SUPERSEDE DUP_GROUP=UHD
INCREMENT nx_isv_vm_hmi ugslmd 2026.06 29-jul-2026 1 SUPERSEDE DUP_GROUP=UHD
INCREMENT borrowing ugslmd 2026.06 permanent 1 DUP_GROUP=NONE
LIC;

        $metadata = $this->nxService->extractMetadata($content);
        $this->assertEquals('29-jul-2026', $metadata['expiration']);

        $filename = $this->nxService->generateFilename([
            'sold_to' => '1112630',
            'hostname' => 'Giz-Datos',
            'client' => 'GOIMEK S.COOP',
            'version' => '26.06',
            'expiration' => $metadata['expiration'],
            'type' => 'Standard'
        ]);

        $this->assertEquals('1112630_GIZ-DATOS_GOIMEK_S_COOP_V26.06_Valida_29-Jul-2026.lic', $filename);
    }
}
