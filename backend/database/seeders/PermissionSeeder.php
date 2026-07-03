<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Define granular permissions
        $permissions = [
            // Admin only
            'manage system',
            'manage users',
            'manage backups',
            'manage ai',
            'manage imports',
            'manage normalization',
            
            // Technician/Admin
            'manage inventory',
            'manage clients',
            'manage cloud accounts',
            'manage contacts',
            'manage alerts',
            
            // Staff/Technician/Admin
            'manage resources',
            
            // Tools (Can be given to anyone)
            'access nx suite tool',
            'access star ccm tool',
            'access heeds tool',
            'access moldex3d tool',
            'access cod tool',
            'access time tracking tool',
            'access renewal planner',
            
            // View only
            'view clients',
            'view inventory',
            'view reports'
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // 2. Fetch existing roles
        $admin = Role::where('name', 'admin')->first();
        $technician = Role::where('name', 'technician')->first();
        $staff = Role::where('name', 'staff')->first();
        $viewer = Role::where('name', 'viewer')->first();

        // 3. Assign base permissions to roles
        if ($admin) {
            // Admin gets EVERYTHING
            $admin->givePermissionTo(Permission::all());
        }

        if ($technician) {
            $technician->givePermissionTo([
                'manage inventory',
                'manage clients',
                'manage cloud accounts',
                'manage contacts',
                'manage alerts',
                'manage resources',
                
                'access nx suite tool',
                'access star ccm tool',
                'access heeds tool',
                'access moldex3d tool',
                'access cod tool',
                'access time tracking tool',
                'access renewal planner',
                
                'view clients',
                'view inventory',
                'view reports'
            ]);
        }

        if ($staff) {
            $staff->givePermissionTo([
                'manage resources',
                
                'access nx suite tool',
                'access star ccm tool',
                'access heeds tool',
                'access moldex3d tool',
                'access cod tool',
                'access time tracking tool',
                'access renewal planner',
                
                'view clients',
                'view inventory',
            ]);
        }

        if ($viewer) {
            $viewer->givePermissionTo([
                'access time tracking tool', // Assuming basic tools are available
                
                'view clients',
                'view inventory',
            ]);
        }
    }
}
