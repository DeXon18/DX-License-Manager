<?php

return [
    'n8n_webhook_url' => env('N8N_AUDIT_WEBHOOK_URL'),
    'n8n_webhook_secret' => env('N8N_WEBHOOK_SECRET'),
    'callback_url' => env('AUDIT_CALLBACK_URL'),
    'gemini_key' => env('GEMINI_API_KEY'),
    'deepseek_key' => env('DEEPSEEK_API_KEY'),
    'openrouter_key' => env('OPENROUTER_API_KEY'),
];
