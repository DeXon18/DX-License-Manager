<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Contract;
use App\Models\ImportLog;
use App\Models\Vendor;
use App\Services\Data\CsvImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CsvImportTest extends TestCase
{
    use RefreshDatabase;

    protected $importService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->importService = new CsvImportService();
    }

    public function test_it_can_import_csv_data_correctly()
    {
        // 1. Prepare dummy CSV data
        $csvContent = "Contraheader;Cost Center;Client Name;Vendor;Product;End Date;Status;Comment\n";
        $csvContent .= "CONH12345;710-PDM;METALOGENIA SA;SIEMENS;NX;01/01/2027;Activo;Test Comment";
        
        $filePath = tempnam(sys_get_temp_dir(), 'test_import_');
        file_put_contents($filePath, $csvContent);

        // 2. Execute Import
        $result = $this->importService->import($filePath, 'test_import.csv');

        // 3. Assertions
        $this->assertEquals(1, $result['processed']);
        $this->assertDatabaseHas('clients', ['name' => 'Metalogenia Sa']); // Title Case
        $this->assertDatabaseHas('vendors', ['name' => 'SIEMENS']);
        $this->assertDatabaseHas('contracts', [
            'contract_number' => 'CONH12345',
            'cost_center' => '710-PDM',
            'type_product' => 'NX',
            'status' => 'Activo',
            'comment' => 'Test Comment'
        ]);

        $contract = Contract::first();
        $this->assertEquals('2027-01-01', $contract->end_date->format('Y-m-d'));

        $this->assertDatabaseHas('import_logs', [
            'filename' => 'test_import.csv',
            'status' => 'success',
            'total_rows' => 1
        ]);

        unlink($filePath);
    }

    public function test_it_updates_existing_contracts_and_marks_absent_as_baja()
    {
        // 1. First Import
        $csv1 = "Contraheader;Cost Center;Client Name;Vendor;Product;End Date;Status;Comment\n";
        $csv1 .= "CONH_001;CC_1;CLIENT A;VENDOR A;PROD_1;01/01/2026;Activo;Initial\n";
        $csv1 .= "CONH_002;CC_2;CLIENT B;VENDOR B;PROD_2;01/01/2026;Activo;To be Baja";
        
        $path1 = tempnam(sys_get_temp_dir(), 'test_import_1_');
        file_put_contents($path1, $csv1);
        $this->importService->import($path1, 'import1.csv');
        unlink($path1);

        $this->assertDatabaseCount('contracts', 2);

        // 2. Second Import (Update CONH_001, Remove CONH_002)
        $csv2 = "Contraheader;Cost Center;Client Name;Vendor;Product;End Date;Status;Comment\n";
        $csv2 .= "CONH_001;CC_NEW;CLIENT A;VENDOR A;PROD_1;01/01/2026;Activo;Updated";
        
        $path2 = tempnam(sys_get_temp_dir(), 'test_import_2_');
        file_put_contents($path2, $csv2);
        $this->importService->import($path2, 'import2.csv');
        unlink($path2);

        // 3. Final Assertions
        $this->assertDatabaseHas('contracts', [
            'contract_number' => 'CONH_001',
            'cost_center' => 'CC_NEW',
            'comment' => 'Updated'
        ]);

        $this->assertDatabaseHas('contracts', [
            'contract_number' => 'CONH_002',
            'status' => 'Baja'
        ]);
    }
}
