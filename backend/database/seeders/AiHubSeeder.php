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
            // FREE MODELS
            ['openrouter_id' => 'deepseek/deepseek-v4-flash:free', 'name' => 'DeepSeek V4 Flash (Gratis)', 'is_free' => true, 'price_prompt' => 0, 'price_completion' => 0],
            ['openrouter_id' => 'deepseek/deepseek-r1:free', 'name' => 'DeepSeek R1 (Gratis)', 'is_free' => true, 'price_prompt' => 0, 'price_completion' => 0],
            ['openrouter_id' => 'meta-llama/llama-4-maverick:free', 'name' => 'Llama 4 Maverick (Gratis)', 'is_free' => true, 'price_prompt' => 0, 'price_completion' => 0],
            ['openrouter_id' => 'meta-llama/llama-4-scout:free', 'name' => 'Llama 4 Scout (Gratis)', 'is_free' => true, 'price_prompt' => 0, 'price_completion' => 0],
            ['openrouter_id' => 'qwen/qwen3-coder:free', 'name' => 'Qwen 3 Coder 480B (Gratis)', 'is_free' => true, 'price_prompt' => 0, 'price_completion' => 0],
            ['openrouter_id' => 'qwen/qwen3-235b-a22b:free', 'name' => 'Qwen 3 235B (Gratis)', 'is_free' => true, 'price_prompt' => 0, 'price_completion' => 0],
            ['openrouter_id' => 'z-ai/glm-4.5-air:free', 'name' => 'GLM 4.5 Air (Gratis)', 'is_free' => true, 'price_prompt' => 0, 'price_completion' => 0],
            ['openrouter_id' => 'openai/gpt-oss-120b:free', 'name' => 'GPT-OSS 120B (Gratis)', 'is_free' => true, 'price_prompt' => 0, 'price_completion' => 0],
            ['openrouter_id' => 'mistralai/mistral-small-3.1-24b-instruct:free', 'name' => 'Mistral Small 3.1 (Gratis)', 'is_free' => true, 'price_prompt' => 0, 'price_completion' => 0],
            ['openrouter_id' => 'google/gemma-4-31b-it:free', 'name' => 'Gemma 4 31B (Gratis)', 'is_free' => true, 'price_prompt' => 0, 'price_completion' => 0],
            
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
        $llamaMaverickFree = AiModel::where('openrouter_id', 'meta-llama/llama-4-maverick:free')->first()->id;
        $geminiFlashPaid = AiModel::where('openrouter_id', 'google/gemini-1.5-flash')->first()->id;

        $deepseekV4Free = AiModel::where('openrouter_id', 'deepseek/deepseek-v4-flash:free')->first()->id;
        $deepseekV4Paid = AiModel::where('openrouter_id', 'deepseek/deepseek-v4-flash')->first()->id;

        $gemmaFree = AiModel::where('openrouter_id', 'google/gemma-4-31b-it:free')->first()->id;

        // 2. Insert Routes
        $routes = [
            [
                'task_name' => 'chatbot',
                'primary_model_id' => $llamaMaverickFree,
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
                'primary_model_id' => $gemmaFree,
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
