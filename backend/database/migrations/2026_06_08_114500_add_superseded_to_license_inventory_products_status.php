<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE license_inventory_products MODIFY COLUMN status ENUM('active', 'expired', 'obsolete', 'superseded') NOT NULL DEFAULT 'active'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE license_inventory_products MODIFY COLUMN status ENUM('active', 'expired', 'obsolete') NOT NULL DEFAULT 'active'");
    }
};
