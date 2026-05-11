<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Crear Roles
        $roles = [
            [
                'name' => 'Administrador',
                'slug' => 'admin',
                'description' => 'Control total — gestión, configuración, herramientas, feature flags'
            ],
            [
                'name' => 'Técnico',
                'slug' => 'technician',
                'description' => 'Acceso operativo completo — clientes, contratos, licencias y herramientas técnicas'
            ],
            [
                'name' => 'Staff',
                'slug' => 'staff',
                'description' => 'Gestión administrativa y técnica operativa — acceso a herramientas según configuración'
            ],
            [
                'name' => 'Invitado',
                'slug' => 'viewer',
                'description' => 'Solo lectura o acceso únicamente a herramientas asignadas explícitamente'
            ],
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate(['slug' => $roleData['slug']], $roleData);
        }

        // 2. Crear Usuario Admin Inicial (Oskar)
        $adminRole = Role::where('slug', 'admin')->first();

        User::firstOrCreate(
            ['email' => 'dexon18@gmail.com'],
            [
                'name' => 'Oskar',
                'password' => Hash::make('password'), // El usuario deberá cambiarla
                'role_id' => $adminRole->id,
                'is_active' => true,
            ]
        );
    }
}
