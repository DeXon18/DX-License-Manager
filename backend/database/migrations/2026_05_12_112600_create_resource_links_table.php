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
        Schema::create('resource_links', function (Blueprint $col) {
            $col->uuid('id')->primary();
            $col->string('vendor')->index(); // siemens, moldex3d
            $col->string('category')->default('official'); // official, internal, utility, support
            $col->string('label');
            $col->string('url');
            $col->text('description')->nullable();
            $col->string('icon')->nullable(); // link, book, shield, etc.
            $col->integer('order')->default(0);
            $col->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resource_links');
    }
};
