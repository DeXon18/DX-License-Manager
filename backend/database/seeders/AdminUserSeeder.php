<?php

namespace Database\Seeders;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'dexon18@gmail.com'],
            [
                'name' => 'Oskar',
                'password' => Hash::make('Venganza69'),
                'is_active' => true,
            ]
        );

        if (method_exists($user, 'assignRole')) {
            $user->assignRole('admin');
        }
    }
}
