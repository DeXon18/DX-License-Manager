<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NXSuiteToolTest extends TestCase
{
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware();
        Storage::fake('local');
        $this->user = User::factory()->create();
    }

    /** @test */
    public function an_authenticated_user_can_access_nx_suite_tool()
    {
        $response = $this->actingAs($this->user)->get(route('tools.nx-suite.index'));
        $response->assertStatus(200);
        $response->assertSee('Designcenter & TC');
    }

    /** @test */
    public function it_processes_and_stores_standard_license()
    {
        $content = "################################################################################\n#  Sold-To/Install: 10300000\n#  Customer Name: Test Client\nSERVER Host1 COMPOSITE=X 28000\nVENDOR ugslmd";
        $file = UploadedFile::fake()->createWithContent('test.lic', $content);

        $response = $this->actingAs($this->user)->post(route('tools.nx-suite.process'), [
            'license_file' => $file,
            'motor'        => 'salt'
        ]);

        $response->assertStatus(200);
        $expectedDate = date('dmY');
        $expectedFilename = "10300000_host1_TEST-CLIENT_2512_Valida_{$expectedDate}.lic";
        $response->assertHeader('Content-Disposition', "attachment; filename=\"{$expectedFilename}\"");

        // Verificar almacenamiento (Nueva ruta: licenses/siemens/{client}/...)
        $monthYear = date('m-Y');
        $path = "licenses/siemens/test-client/{$monthYear}/{$expectedFilename}";
        Storage::disk('local')->assertExists($path);
    }

    /** @test */
    public function it_handles_duplicate_filenames()
    {
        $content = "################################################################################\n#  Sold-To/Install: 10300000\n#  Customer Name: Test Client\nSERVER Host1 COMPOSITE=X 28000\nVENDOR ugslmd";
        $file = UploadedFile::fake()->createWithContent('test.lic', $content);

        $expectedDate = date('dmY');
        $expectedFilename = "10300000_host1_TEST-CLIENT_2512_Valida_{$expectedDate}.lic";
        $monthYear = date('m-Y');
        $storagePath = "licenses/siemens/test-client/{$monthYear}";

        // Pre-crear el archivo para forzar duplicado
        Storage::disk('local')->put("{$storagePath}/{$expectedFilename}", "existing content");

        $response = $this->actingAs($this->user)->post(route('tools.nx-suite.process'), [
            'license_file' => $file,
            'motor'        => 'salt'
        ]);

        $response->assertStatus(200);
        // Debe tener el sufijo _1
        $response->assertHeader('Content-Disposition', "attachment; filename=\"{$expectedFilename}_1\"");
        Storage::disk('local')->assertExists("{$storagePath}/{$expectedFilename}_1");
    }

    /** @test */
    public function it_processes_but_does_not_store_temporal_license()
    {
        $content = "SERVER YourHostname ANY 28000\nVENDOR ugslmd";
        $file = UploadedFile::fake()->createWithContent('temp.lic', $content);

        $response = $this->actingAs($this->user)->post(route('tools.nx-suite.process'), [
            'license_file' => $file,
            'motor'        => 'legacy'
        ]);

        $response->assertStatus(200);
        $expectedDate = date('dmY');
        $expectedFilename = "10300000_DEFAULT_2512_TEMP_Valida_{$expectedDate}.lic";
        $response->assertHeader('Content-Disposition', "attachment; filename=\"{$expectedFilename}\"");

        // Verificar NO almacenamiento
        Storage::disk('local')->assertDirectoryEmpty('licenses');
    }
}
