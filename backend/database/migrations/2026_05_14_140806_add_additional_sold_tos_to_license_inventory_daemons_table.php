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
        Schema::table('license_inventory_daemons', function (Blueprint $table) {
            $table->json('additional_sold_tos')->nullable()->after('sold_to');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('license_inventory_daemons', function (Blueprint $table) {
            $table->dropColumn('additional_sold_tos');
        });
    }
};
