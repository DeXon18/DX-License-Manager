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

        // 2. Crear Usuarios de Prueba (uno de cada rol)
        $users = [
            [
                'name' => 'Oskar Admin',
                'email' => 'dexon18@gmail.com',
                'role' => 'admin'
            ],
            [
                'name' => 'Técnico Especialista',
                'email' => 'tecnico@dxpro.es',
                'role' => 'technician'
            ],
            [
                'name' => 'Staff Administrativo',
                'email' => 'staff@dxpro.es',
                'role' => 'staff'
            ],
            [
                'name' => 'Usuario Lector',
                'email' => 'viewer@dxpro.es',
                'role' => 'viewer'
            ],
        ];

        foreach ($users as $userData) {
            $role = Role::where('slug', $userData['role'])->first();
            User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('password123'),
                    'role_id' => $role->id,
                    'is_active' => true,
                ]
            );
        }
    }
}
