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
        Schema::create('ai_models', function (Blueprint $table) {
            $table->id();
            $table->string('openrouter_id')->unique()->comment('ID del modelo en OpenRouter (ej. deepseek/deepseek-r1:free)');
            $table->string('name')->comment('Nombre amigable para la interfaz administrativa');
            $table->boolean('is_free')->default(false)->comment('True si el modelo no tiene coste por token en OpenRouter');
            $table->decimal('price_prompt', 10, 6)->default(0)->comment('Precio por 1M de tokens de entrada (Prompt)');
            $table->decimal('price_completion', 10, 6)->default(0)->comment('Precio por 1M de tokens de salida (Completion)');
            $table->boolean('is_active')->default(true)->comment('Permite desactivar un modelo temporalmente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_models');
    }
};
