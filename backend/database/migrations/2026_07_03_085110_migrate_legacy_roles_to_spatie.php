<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Get legacy roles
        $legacyRoles = DB::table('legacy_roles')->get();
        
        // 2. Insert into Spatie's roles table
        foreach ($legacyRoles as $legacyRole) {
            DB::table('roles')->insert([
                'name' => $legacyRole->slug, // Spatie uses 'name' as the slug normally
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        // 3. Migrate users -> model_has_roles
        $users = DB::table('users')->whereNotNull('role_id')->get();
        foreach ($users as $user) {
            $legacyRole = DB::table('legacy_roles')->where('id', $user->role_id)->first();
            if ($legacyRole) {
                $spatieRole = DB::table('roles')->where('name', $legacyRole->slug)->first();
                if ($spatieRole) {
                    DB::table('model_has_roles')->insert([
                        'role_id' => $spatieRole->id,
                        'model_type' => 'App\Models\User',
                        'model_id' => $user->id,
                    ]);
                }
            }
        }
        
        // 4. Drop legacy columns/tables
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });
        
        Schema::dropIfExists('legacy_roles');
    }

    public function down(): void
    {
        // We will not implement a full rollback here as it's complex and this is a one-way migration to Spatie.
    }
};
