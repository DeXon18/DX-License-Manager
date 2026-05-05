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
                'description' => 'Acceso total al sistema e infraestructura.'
            ],
            [
                'name' => 'Técnico',
                'slug' => 'technician',
                'description' => 'Gestión de licencias, auditorías y clientes.'
            ],
            [
                'name' => 'Staff',
                'slug' => 'staff',
                'description' => 'Gestión de clientes, contratos, contactos y documentación.'
            ],
            [
                'name' => 'Visor',
                'slug' => 'viewer',
                'description' => 'Solo lectura de reportes y estado de licencias.'
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
