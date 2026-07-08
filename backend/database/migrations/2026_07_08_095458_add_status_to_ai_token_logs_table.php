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
        Schema::table('ai_token_logs', function (Blueprint $table) {
            $table->string('status', 20)->default('success')->after('user_id');
            $table->text('error_message')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_token_logs', function (Blueprint $table) {
            $table->dropColumn(['status', 'error_message']);
        });
    }
};
