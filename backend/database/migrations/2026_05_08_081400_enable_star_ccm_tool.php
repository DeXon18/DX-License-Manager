<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('feature_flags')->updateOrInsert(
            ['key' => 'siemens_star_ccm'],
            [
                'label' => 'STAR-CCM+',
                'description' => 'Motor de transformación cdlmd → saltd con auditoría IA integrada.',
                'vendor' => 'Siemens',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('feature_flags')->where('key', 'siemens_star_ccm')->update(['is_active' => false]);
    }
};
