<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('slug', 'admin')->first();

        User::updateOrCreate(
            ['email' => 'dexon18@gmail.com'],
            [
                'name' => 'Oskar',
                'password' => Hash::make('Venganz@69!'),
                'role_id' => $adminRole->id ?? 1,
                'is_active' => true,
            ]
        );
    }
}
