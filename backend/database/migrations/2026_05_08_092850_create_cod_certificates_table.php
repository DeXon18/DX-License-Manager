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
        Schema::create('cod_certificates', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('client_id')->nullable()->constrained()->onDelete('set null');
            $table->string('sold_to');
            $table->string('type'); // FULL_CHANGE, COMPOSITE, NODELOCKED
            $table->string('os');   // WINDOWS, LINUX
            $table->string('language'); // SPANISH, ENGLISH
            $table->string('status')->default('PENDING'); // PENDING, SIGNED
            $table->string('file_path')->nullable();
            $table->json('form_data');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cod_certificates');
    }
};
