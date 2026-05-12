<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('license_archives', function (Blueprint $table) {
            $table->string('origin')->default('auto')->after('storage_path'); // auto / manual
        });
    }

    public function down(): void
    {
        Schema::table('license_archives', function (Blueprint $table) {
            $table->dropColumn('origin');
        });
    }
};
