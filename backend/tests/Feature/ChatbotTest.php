<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\AI\ChatbotService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatbotTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(); // Bypass JWT session middleware for simplified isolation testing
        $this->user = User::factory()->create();
    }

    /** @test */
    public function chatbot_endpoint_requires_messages_parameter(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('chatbot.query'), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['messages']);
    }

    /** @test */
    public function chatbot_endpoint_validates_message_format(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('chatbot.query'), [
                'messages' => [
                    ['role' => 'invalid_role', 'content' => 'Hola']
                ]
            ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function chatbot_endpoint_successfully_invokes_service_and_returns_response(): void
    {
        // Mock del servicio de IA para evitar llamadas reales HTTP en tests automatizados
        $this->mock(ChatbotService::class, function ($mock) {
            $mock->shouldReceive('query')
                ->once()
                ->andReturn([
                    'success' => true,
                    'message' => 'Hola técnico. He consultado el inventario y todo está óptimo.',
                    'provider' => 'gemini-mock'
                ]);
        });

        $response = $this->actingAs($this->user)
            ->postJson(route('chatbot.query'), [
                'messages' => [
                    ['role' => 'user', 'content' => 'Hola, ¿cómo están las licencias?']
                ]
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Hola técnico. He consultado el inventario y todo está óptimo.',
            'provider' => 'gemini-mock'
        ]);
    }

    /** @test */
    public function chatbot_endpoint_returns_enriched_response_with_telemetry_and_data(): void
    {
        $this->mock(ChatbotService::class, function ($mock) {
            $mock->shouldReceive('query')
                ->once()
                ->andReturn([
                    'success' => true,
                    'message' => 'Resumen ejecutivo obtenido.',
                    'provider' => 'gemini-mock',
                    'usage_metadata' => [
                        'promptTokenCount' => 150,
                        'candidatesTokenCount' => 80,
                        'totalTokenCount' => 230
                    ],
                    'data' => [
                        'get_dashboard_summary' => [
                            'total_clients' => 25,
                            'critical_licenses_30_days' => 2,
                            'expired_licenses' => 1
                        ]
                    ]
                ]);
        });

        $response = $this->actingAs($this->user)
            ->postJson(route('chatbot.query'), [
                'messages' => [
                    ['role' => 'user', 'content' => 'Dame un resumen ejecutivo']
                ]
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Resumen ejecutivo obtenido.',
            'provider' => 'gemini-mock',
            'usage_metadata' => [
                'promptTokenCount' => 150,
                'candidatesTokenCount' => 80,
                'totalTokenCount' => 230
            ],
            'data' => [
                'get_dashboard_summary' => [
                    'total_clients' => 25,
                    'critical_licenses_30_days' => 2,
                    'expired_licenses' => 1
                ]
            ]
        ]);
    }

    /** @test */
    public function chatbot_service_respects_session_mutation_limits(): void
    {
        // Crear un request simulado con sesión activa en el contenedor
        $request = \Illuminate\Http\Request::create('/chatbot/query', 'POST');
        $request->setLaravelSession(resolve('session.store'));
        $this->app->instance('request', $request);

        // Forzar límite de mutaciones a 5 en la sesión actual
        session(['chatbot_mutations_count' => 5]);

        $service = resolve(ChatbotService::class);
        
        $method = new \ReflectionMethod(ChatbotService::class, 'callTool');
        $method->setAccessible(true);

        // Intentar invocar una herramienta mutacional (create_contact) con el límite alcanzado
        $result = $method->invoke($service, 'create_contact', [
            'client_id' => 1,
            'name' => 'Prueba Limitador',
            'email' => 'limitador@arquimea.com'
        ]);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Límite de mutaciones', $result['error']);

        // Intentar invocar otra herramienta mutacional (update_contact)
        $resultUpdate = $method->invoke($service, 'update_contact', [
            'contact_id' => 1,
            'role' => 'Technician'
        ]);

        $this->assertFalse($resultUpdate['success']);
        $this->assertStringContainsString('Límite de mutaciones', $resultUpdate['error']);
    }
}
