<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE license_inventory_products MODIFY COLUMN status ENUM('active', 'expired', 'obsolete', 'superseded') NOT NULL DEFAULT 'active'");
        }
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE license_inventory_products MODIFY COLUMN status ENUM('active', 'expired', 'obsolete') NOT NULL DEFAULT 'active'");
        }
    }
};
