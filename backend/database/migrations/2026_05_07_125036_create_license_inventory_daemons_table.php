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
        Schema::create('license_inventory_daemons', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->string('sold_to')->index();
            $table->string('daemon')->index();
            $table->string('hostname')->nullable();
            $table->string('composite')->nullable();
            $table->string('hardware_id')->nullable();
            $table->string('version')->nullable();
            $table->enum('type', ['floating', 'node-locked', 'dongle'])->default('floating');
            $table->timestamps();

            $table->unique(['client_id', 'sold_to', 'daemon'], 'client_sold_to_daemon_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('license_inventory_daemons');
    }
};
