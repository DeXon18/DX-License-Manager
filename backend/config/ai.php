<?php

return [
    'n8n_webhook_url' => env('N8N_AUDIT_WEBHOOK_URL'),
    'n8n_webhook_secret' => env('N8N_WEBHOOK_SECRET'),
    'callback_url' => env('AUDIT_CALLBACK_URL'),
    'gemini_key' => env('GEMINI_API_KEY'),
    'deepseek_key' => env('DEEPSEEK_API_KEY'),
    'openrouter_key' => env('OPENROUTER_API_KEY'),
    'groq_key'       => env('GROQ_API_KEY'),
    'bot_token' => env('BOT_API_TOKEN'),
    'telegram_bot_token' => env('TELEGRAM_BOT_TOKEN'),

    // Pricing per 1M tokens (USD)
    'pricing' => [
        'gemini-1.5-flash' => [
            'prompt' => 0.15,
            'completion' => 0.60,
        ],
        'gemini-3.1-flash-lite' => [
            'prompt' => 0.15, // Aproximado
            'completion' => 0.60,
        ],
        'deepseek-chat' => [
            'prompt' => 0.14,
            'completion' => 0.28,
        ],
        // n8n uses DeepSeek internally
        'n8n-deepseek' => [
            'prompt' => 0.14,
            'completion' => 0.28,
        ],
        'default' => [
            'prompt' => 0.00,
            'completion' => 0.00,
        ]
    ],
];
