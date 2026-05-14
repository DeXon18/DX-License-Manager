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
        Schema::create('renewal_log_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('renewal_log_id')->constrained()->onDelete('cascade');
            $table->string('file_path');
            $table->string('file_name');
            $table->timestamps();
        });

        // Opcional: Eliminar file_path de renewal_logs si queremos limpiar, 
        // pero lo dejamos por si acaso o lo ignoramos.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('renewal_log_files');
    }
};
