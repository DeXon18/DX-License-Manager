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
        Schema::create('license_inventory_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daemon_id')->constrained('license_inventory_daemons')->onDelete('cascade');
            $table->string('product_code')->index();
            $table->string('description')->nullable();
            $table->integer('quantity')->default(1);
            $table->date('expiration_date')->nullable();
            $table->string('node_locked_host_id')->nullable()->index();
            $table->enum('status', ['active', 'expired', 'obsolete'])->default('active');
            $table->timestamps();

            $table->index(['product_code', 'node_locked_host_id'], 'prod_mac_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('license_inventory_products');
    }
};
