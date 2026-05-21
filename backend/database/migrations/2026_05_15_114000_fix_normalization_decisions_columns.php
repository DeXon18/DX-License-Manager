<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('normalization_decisions', function (Blueprint $table) {
            if (!Schema::hasColumn('normalization_decisions', 'detected_name')) {
                $table->string('detected_name')->index()->after('id');
            }
            if (!Schema::hasColumn('normalization_decisions', 'decision')) {
                $table->string('decision')->default('ignore')->after('detected_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('normalization_decisions', function (Blueprint $table) {
            $table->dropColumn(['detected_name', 'decision']);
        });
    }
};
