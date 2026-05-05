<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'Admin', 'slug' => 'admin']);
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
            'role_id' => Role::where('slug', 'admin')->first()->id,
            'is_active' => true,
        ]);

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
            'role_id' => Role::where('slug', 'admin')->first()->id,
            'is_active' => true,
        ]);

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
            'role_id' => Role::where('slug', 'admin')->first()->id,
            'is_active' => false,
        ]);

        $response = $this->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class])
            ->from('/login')
            ->post('/login', [
                'email' => 'inactive@dxpro.es',
                'password' => 'password',
            ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
    }
}
