<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Role;
use App\Services\Auth\JwtService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SystemDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Ensure we are using sqlite for tests
        config(['database.default' => 'sqlite']);
        config(['database.connections.sqlite.database' => ':memory:']);

        // Force migrations for sqlite memory
        $this->artisan('migrate');

        // Seed roles for each test
        Role::create(['name' => 'Admin', 'slug' => 'admin', 'level' => 100]);
        Role::create(['name' => 'Technician', 'slug' => 'technician', 'level' => 50]);
        Role::create(['name' => 'Viewer', 'slug' => 'viewer', 'level' => 10]);
    }

    public function test_admin_can_access_system_dashboard()
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role_id' => 1,
            'is_active' => true,
        ]);

        $jwtService = app(JwtService::class);
        $token = $jwtService->generate([
            'sub' => $admin->id,
            'email' => $admin->email,
            'role' => 'admin'
        ]);

        $response = $this->withCookie('jwt_token', $token)
            ->get('/admin/system');

        $response->assertStatus(200);
        $response->assertSee('Salud del Sistema');
    }

    public function test_technician_cannot_access_system_dashboard()
    {
        $tech = User::create([
            'name' => 'Tech User',
            'email' => 'tech@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role_id' => 2,
            'is_active' => true,
        ]);

        $jwtService = app(JwtService::class);
        $token = $jwtService->generate([
            'sub' => $tech->id,
            'email' => $tech->email,
            'role' => 'technician'
        ]);

        $response = $this->withCookie('jwt_token', $token)
            ->get('/admin/system');

        $response->assertStatus(403);
    }
}
