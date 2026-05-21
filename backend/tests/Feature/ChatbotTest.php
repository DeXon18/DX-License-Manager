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
}
