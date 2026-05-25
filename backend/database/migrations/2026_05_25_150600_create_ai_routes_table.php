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
        Schema::create('ai_routes', function (Blueprint $table) {
            $table->string('task_name')->primary()->comment('Identificador de la tarea (ej. chatbot, normalizacion, auditoria)');
            $table->foreignId('primary_model_id')->constrained('ai_models')->onDelete('restrict')->comment('Modelo principal a utilizar');
            $table->foreignId('fallback_model_id')->nullable()->constrained('ai_models')->onDelete('set null')->comment('Modelo de respaldo si el principal falla (Anti-Rate-Limit)');
            $table->string('description')->nullable()->comment('Descripción interna de qué hace esta ruta');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_routes');
    }
};
