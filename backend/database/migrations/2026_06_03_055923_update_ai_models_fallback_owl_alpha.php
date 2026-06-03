<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $owlAlpha = DB::table('ai_models')->where('openrouter_id', 'openrouter/owl-alpha')->first();
        $geminiFlash = DB::table('ai_models')->where('openrouter_id', 'google/gemini-1.5-flash')->first();

        if ($owlAlpha && $geminiFlash) {
            DB::table('ai_routes')
                ->where('fallback_model_id', $geminiFlash->id)
                ->update(['fallback_model_id' => $owlAlpha->id]);

            DB::table('ai_models')->where('id', $geminiFlash->id)->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // En caso de rollback, reinsertamos el modelo si no existe
        DB::table('ai_models')->insertOrIgnore([
            'openrouter_id' => 'google/gemini-1.5-flash',
            'name' => 'Gemini 1.5 Flash (Pago)',
            'is_free' => false,
            'price_prompt' => 0.075,
            'price_completion' => 0.30,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $owlAlpha = DB::table('ai_models')->where('openrouter_id', 'openrouter/owl-alpha')->first();
        $geminiFlash = DB::table('ai_models')->where('openrouter_id', 'google/gemini-1.5-flash')->first();

        if ($owlAlpha && $geminiFlash) {
            DB::table('ai_routes')
                ->where('fallback_model_id', $owlAlpha->id)
                ->update(['fallback_model_id' => $geminiFlash->id]);
        }
    }
};
