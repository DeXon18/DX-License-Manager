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
        Schema::create('ncmatic_licenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->string('serial_number');
            $table->string('license_type')->default('MNTO'); // MNTO, ALQ, PERMANENT, TRIAL
            $table->integer('seats')->default(1);
            $table->dateTime('expiration_date')->nullable();
            $table->string('status')->default('active'); // active, dropped, expired
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ncmatic_licenses');
    }
};
