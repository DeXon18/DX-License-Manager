<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alert_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('threshold_alerta')->default(7);
            $table->integer('threshold_aviso')->default(15);
            $table->integer('threshold_recordatorio')->default(30);
            $table->text('internal_copy_emails')->nullable(); // JSON or comma-separated
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alert_settings');
    }
};
