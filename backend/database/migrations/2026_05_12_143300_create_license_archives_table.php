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
        Schema::create('license_archives', function (Blueprint $table) {
            $table->id();
            $table->string('filename')->unique();
            $table->integer('week_number');
            $table->integer('year');
            $table->integer('files_count')->default(0);
            $table->json('clients_summary')->nullable(); // Resumen de Sold-Tos y Clientes
            $table->string('storage_path');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('license_archives');
    }
};
