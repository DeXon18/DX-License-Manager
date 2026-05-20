<?php

namespace Tests\Unit;

use App\Models\Client;
use App\Models\ClientAlias;
use App\Services\Data\ClientNormalizationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientNormalizationTest extends TestCase
{
    use RefreshDatabase;

    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ClientNormalizationService();
    }

    public function test_it_finds_exact_match()
    {
        Client::create(['name' => 'Universidad Pontificia Comillas']);

        $result = $this->service->resolve('Universidad Pontificia Comillas');

        $this->assertEquals('exact', $result['status']);
        $this->assertNotNull($result['id']);
    }

    public function test_it_finds_alias_match()
    {
        $client = Client::create(['name' => 'Universidad Pontificia Comillas']);
        ClientAlias::create([
            'client_id' => $client->id,
            'name' => 'Universidad Pontifica Comillas'
        ]);

        $result = $this->service->resolve('Universidad Pontifica Comillas');

        $this->assertEquals('alias', $result['status']);
        $this->assertEquals($client->id, $result['id']);
    }

    public function test_it_detects_suspicion()
    {
        Client::create(['name' => 'Universidad Pontificia Comillas']);

        // "Pontifica" (missing 'i') should be very similar to "Pontificia"
        $result = $this->service->resolve('Universidad Pontifica Comillas');

        $this->assertEquals('suspicion', $result['status']);
        $this->assertNotNull($result['id']);
        $this->assertNotNull($result['suggested_id']);
    }

    public function test_it_creates_new_client_if_no_similarity()
    {
        Client::create(['name' => 'Universidad Pontificia Comillas']);

        $result = $this->service->resolve('Indra Sistemas');

        $this->assertEquals('new', $result['status']);
        $this->assertNotNull($result['id']);
        $this->assertDatabaseHas('clients', ['name' => 'Indra Sistemas']);
    }

    public function test_it_falls_back_to_ai_on_low_levenshtein_similarity()
    {
        // 1. Create a client that the AI will match
        $existingClient = Client::create(['name' => 'Urovesa']);
        
        // 2. Mock the ClientAiNormalizationService
        $mockAiService = $this->createMock(\App\Services\AI\ClientAiNormalizationService::class);
        $mockAiService->expects($this->once())
            ->method('evaluate')
            ->with('Uro Vehiculos Especiales Sa (Urovesa)')
            ->willReturn([
                'matched' => true,
                'matched_id' => $existingClient->id,
                'confidence' => 0.95,
                'provider' => 'gemini',
                'reason' => 'Sigla coincide'
            ]);
            
        // Bind the mock in the service container
        $this->app->instance(\App\Services\AI\ClientAiNormalizationService::class, $mockAiService);
        
        // 3. Resolve the new name
        $result = $this->service->resolve('Uro Vehiculos Especiales Sa (Urovesa)');
        
        // 4. Assertions
        $this->assertEquals('suspicion', $result['status']);
        $this->assertEquals($existingClient->id, $result['suggested_id']);
        $this->assertEquals(95.0, $result['similarity']);
        $this->assertStringContainsString('GEMINI', $result['warning']);
        $this->assertStringContainsString('Sigla coincide', $result['warning']);
    }
}
