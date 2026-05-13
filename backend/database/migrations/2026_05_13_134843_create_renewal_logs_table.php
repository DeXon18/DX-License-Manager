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
        Schema::create('renewal_logs', function (Blueprint $column) {
            $column->id();
            $column->foreignId('client_id')->constrained()->onDelete('cascade');
            $column->foreignId('user_id')->constrained()->onDelete('cascade');
            $column->integer('month'); // 1-12
            $column->integer('year');
            $column->string('file_path')->nullable();
            $column->text('notes')->nullable();
            $column->timestamp('sent_at')->useCurrent();
            $column->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('renewal_logs');
    }
};
