<?php

namespace Tests\Feature\Admin;

use App\Models\AiModel;
use App\Models\AiRoute;
use App\Models\User;
use App\Services\AI\OpenRouterSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OpenRouterSyncTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $this->withoutMiddleware();
        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole('admin');
    }

    public function test_sync_updates_prices_and_marks_missing_models_inactive(): void
    {
        // 1. Setup existing models
        $existingActive = AiModel::create([
            'openrouter_id' => 'openai/gpt-4o-mini',
            'name' => 'GPT-4o Mini',
            'price_prompt' => 0.00015,
            'price_completion' => 0.00060,
            'is_active' => true,
        ]);

        $deprecatedModel = AiModel::create([
            'openrouter_id' => 'legacy/old-model-v1',
            'name' => 'Old Model',
            'price_prompt' => 0.001,
            'price_completion' => 0.002,
            'is_active' => true,
        ]);

        // 2. Setup mock response from OpenRouter
        Http::fake([
            'openrouter.ai/api/v1/models' => Http::response([
                'data' => [
                    [
                        'id' => 'openai/gpt-4o-mini',
                        'name' => 'GPT-4o Mini Updated',
                        'pricing' => [
                            'prompt' => '0.000140', // price lowered
                            'completion' => '0.000550',
                        ],
                    ],
                ],
            ], 200),
        ]);

        // 3. Execute sync service
        $service = new OpenRouterSyncService();
        $result = $service->sync();

        $this->assertTrue($result['success']);
        $this->assertEquals(1, $result['updated']);
        $this->assertEquals(1, $result['deprecated']);

        // Check updated price
        $existingActive->refresh();
        $this->assertEquals(0.000140, $existingActive->price_prompt);
        $this->assertEquals(0.000550, $existingActive->price_completion);
        $this->assertTrue($existingActive->is_active);

        // Check deprecated model became inactive
        $deprecatedModel->refresh();
        $this->assertFalse($deprecatedModel->is_active);
    }

    public function test_sync_warns_if_inactivated_model_is_assigned_to_route(): void
    {
        $deprecatedModel = AiModel::create([
            'openrouter_id' => 'legacy/broken-primary',
            'name' => 'Broken Model',
            'is_active' => true,
        ]);

        $route = AiRoute::create([
            'task_name' => 'chat',
            'primary_model_id' => $deprecatedModel->id,
            'description' => 'Chatbot principal',
        ]);

        Http::fake([
            'openrouter.ai/api/v1/models' => Http::response([
                'data' => [
                    [
                        'id' => 'google/gemini-2.0-flash-001',
                        'name' => 'Gemini 2.0 Flash',
                        'pricing' => ['prompt' => '0.0001', 'completion' => '0.0004'],
                    ],
                ],
            ], 200),
        ]);

        $service = new OpenRouterSyncService();
        $result = $service->sync();

        $this->assertNotEmpty($result['broken_routes']);
        $this->assertEquals($route->task_name, $result['broken_routes'][0]['task']);
        $this->assertEquals('primary', $result['broken_routes'][0]['type']);
        $this->assertEquals('Broken Model', $result['broken_routes'][0]['model_name']);
    }

    public function test_admin_can_trigger_sync_via_controller(): void
    {
        Http::fake([
            'openrouter.ai/api/v1/models' => Http::response([
                'data' => [
                    [
                        'id' => 'openai/gpt-4o-mini',
                        'name' => 'GPT-4o Mini',
                        'pricing' => ['prompt' => '0', 'completion' => '0'],
                    ]
                ],
            ], 200),
        ]);

        $response = $this->actingAs($this->adminUser)
            ->from(route('admin.system.ai-routing.index'))
            ->post(route('admin.system.ai-routing.sync'));

        $response->assertRedirect(route('admin.system.ai-routing.index'));
        $response->assertSessionHas('success');
    }
}
