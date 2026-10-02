<?php

namespace Tests\Feature;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'admin', 'guard_name' => 'web']);
    }

    /** @test */
    public function unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function user_can_login_with_correct_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@dxpro.es',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $user->assignRole('admin');

        // Disable CSRF for this specific POST request
        $response = $this->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class])
            ->post('/login', [
                'email' => 'test@dxpro.es',
                'password' => 'password',
            ]);

        $response->assertRedirect('/');
        $response->assertCookie('jwt_token');
    }

    /** @test */
    public function user_cannot_login_with_wrong_password(): void
    {
        $user = User::factory()->create([
            'email' => 'test@dxpro.es',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $user->assignRole('admin');

        $response = $this->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class])
            ->from('/login')
            ->post('/login', [
                'email' => 'test@dxpro.es',
                'password' => 'wrong-password',
            ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function inactive_user_cannot_login(): void
    {
        $user = User::factory()->create([
            'email' => 'inactive@dxpro.es',
            'password' => bcrypt('password'),
            'is_active' => false,
        ]);
        $user->assignRole('admin');

        $response = $this->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class])
            ->from('/login')
            ->post('/login', [
                'email' => 'inactive@dxpro.es',
                'password' => 'password',
            ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function jwt_service_embeds_nbf_and_rejects_future_tokens(): void
    {
        $jwtService = app(\App\Services\Auth\JwtService::class);
        $token = $jwtService->generate(['sub' => 1, 'name' => 'Test User']);

        $decoded = $jwtService->decode($token);
        $this->assertNotNull($decoded);
        $this->assertArrayHasKey('nbf', $decoded);
        $this->assertArrayHasKey('iat', $decoded);

        // Token with future nbf (>60s) should fail decode
        $reflection = new \ReflectionClass($jwtService);
        $property = $reflection->getProperty('secret');
        $property->setAccessible(true);
        $secret = $property->getValue($jwtService);

        $futureHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode(['typ' => 'JWT', 'alg' => 'HS256'])));
        $futurePayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode([
            'sub' => 1,
            'iat' => time(),
            'nbf' => time() + 300,
            'exp' => time() + 3600
        ])));
        $sig = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(hash_hmac('sha256', "{$futureHeader}.{$futurePayload}", $secret, true)));
        $futureToken = "{$futureHeader}.{$futurePayload}.{$sig}";

        $this->assertNull($jwtService->decode($futureToken));
    }
}

