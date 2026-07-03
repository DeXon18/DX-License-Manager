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
        // Add status to daemons
        Schema::table('license_inventory_daemons', function (Blueprint $table) {
            $table->enum('status', ['active', 'dropped'])->default('active')->after('type');
        });

        // Add 'dropped' to products status enum
        if (DB::connection()->getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE license_inventory_products MODIFY COLUMN status ENUM('active', 'expired', 'obsolete', 'superseded', 'dropped') NOT NULL DEFAULT 'active'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('license_inventory_daemons', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        if (DB::connection()->getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE license_inventory_products MODIFY COLUMN status ENUM('active', 'expired', 'obsolete', 'superseded') NOT NULL DEFAULT 'active'");
        }
    }
};
