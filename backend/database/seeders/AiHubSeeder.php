<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AiModel;
use App\Models\AiRoute;

class AiHubSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Insert OpenRouter Models
        $models = [
            // FREE MODELS (Top Ranking)
            ['openrouter_id' => 'openrouter/owl-alpha', 'name' => 'Owl Alpha (Gratis)', 'is_free' => true, 'weekly_tokens_limit' => 1260000000000, 'price_prompt' => 0, 'price_completion' => 0],
            ['openrouter_id' => 'nvidia/nemotron-3-super-120b-a12b:free', 'name' => 'Nemotron 3 Super (Gratis)', 'is_free' => true, 'weekly_tokens_limit' => 669000000000, 'price_prompt' => 0, 'price_completion' => 0],
            ['openrouter_id' => 'poolside/laguna-m.1:free', 'name' => 'Laguna M.1 (Gratis)', 'is_free' => true, 'weekly_tokens_limit' => 288000000000, 'price_prompt' => 0, 'price_completion' => 0],
            ['openrouter_id' => 'openai/gpt-oss-120b:free', 'name' => 'GPT-OSS 120B (Gratis)', 'is_free' => true, 'weekly_tokens_limit' => 174000000000, 'price_prompt' => 0, 'price_completion' => 0],
            ['openrouter_id' => 'z-ai/glm-4.5-air:free', 'name' => 'GLM 4.5 Air (Gratis)', 'is_free' => true, 'weekly_tokens_limit' => 98700000000, 'price_prompt' => 0, 'price_completion' => 0],
            ['openrouter_id' => 'arcee-ai/trinity-large-thinking:free', 'name' => 'Trinity Large Thinking (Gratis)', 'is_free' => true, 'weekly_tokens_limit' => 65000000000, 'price_prompt' => 0, 'price_completion' => 0],
            ['openrouter_id' => 'poolside/laguna-xs.2:free', 'name' => 'Laguna XS.2 (Gratis)', 'is_free' => true, 'weekly_tokens_limit' => 54600000000, 'price_prompt' => 0, 'price_completion' => 0],
            ['openrouter_id' => 'deepseek/deepseek-v4-flash:free', 'name' => 'DeepSeek V4 Flash (Gratis)', 'is_free' => true, 'weekly_tokens_limit' => 49200000000, 'price_prompt' => 0, 'price_completion' => 0],
            ['openrouter_id' => 'baidu/cobuddy:free', 'name' => 'CoBuddy (Gratis)', 'is_free' => true, 'weekly_tokens_limit' => 28900000000, 'price_prompt' => 0, 'price_completion' => 0],
            ['openrouter_id' => 'nvidia/nemotron-3-nano-omni-30b-a3b-reasoning:free', 'name' => 'Nemotron 3 Nano Omni (Gratis)', 'is_free' => true, 'weekly_tokens_limit' => 18800000000, 'price_prompt' => 0, 'price_completion' => 0],
            ['openrouter_id' => 'minimax/minimax-m2.5:free', 'name' => 'MiniMax M2.5 (Gratis)', 'is_free' => true, 'weekly_tokens_limit' => 16100000000, 'price_prompt' => 0, 'price_completion' => 0],
            
            // PAID MODELS (Fallbacks)
            ['openrouter_id' => 'deepseek/deepseek-v4-flash', 'name' => 'DeepSeek V4 Flash (Pago)', 'is_free' => false, 'price_prompt' => 0.14, 'price_completion' => 0.28],
            ['openrouter_id' => 'deepseek/deepseek-r1', 'name' => 'DeepSeek R1 (Pago)', 'is_free' => false, 'price_prompt' => 0.55, 'price_completion' => 2.19],
            ['openrouter_id' => 'meta-llama/llama-4-maverick', 'name' => 'Llama 4 Maverick (Pago)', 'is_free' => false, 'price_prompt' => 0.20, 'price_completion' => 0.20],
            ['openrouter_id' => 'google/gemini-1.5-flash', 'name' => 'Gemini 1.5 Flash (Pago)', 'is_free' => false, 'price_prompt' => 0.075, 'price_completion' => 0.30],
        ];

        foreach ($models as $m) {
            AiModel::updateOrCreate(
                ['openrouter_id' => $m['openrouter_id']],
                $m
            );
        }

        // Fetch IDs for routing
        $nemotronSuperFree = AiModel::where('openrouter_id', 'nvidia/nemotron-3-super-120b-a12b:free')->first()->id;
        $geminiFlashPaid = AiModel::where('openrouter_id', 'google/gemini-1.5-flash')->first()->id;

        $deepseekV4Free = AiModel::where('openrouter_id', 'deepseek/deepseek-v4-flash:free')->first()->id;
        $deepseekV4Paid = AiModel::where('openrouter_id', 'deepseek/deepseek-v4-flash')->first()->id;

        $owlAlphaFree = AiModel::where('openrouter_id', 'openrouter/owl-alpha')->first()->id;

        // 2. Insert Routes
        $routes = [
            [
                'task_name' => 'chatbot',
                'primary_model_id' => $nemotronSuperFree,
                'fallback_model_id' => $geminiFlashPaid,
                'description' => 'Motor principal del Chatbot para asistencia general',
            ],
            [
                'task_name' => 'auditoria',
                'primary_model_id' => $deepseekV4Free,
                'fallback_model_id' => $deepseekV4Paid,
                'description' => 'Auditoría y parseo de licencias (.lic / .mac)',
            ],
            [
                'task_name' => 'normalizacion',
                'primary_model_id' => $owlAlphaFree,
                'fallback_model_id' => $geminiFlashPaid,
                'description' => 'Normalización de nombres de clientes y matching',
            ]
        ];

        foreach ($routes as $r) {
            AiRoute::updateOrCreate(
                ['task_name' => $r['task_name']],
                $r
            );
        }
    }
}
