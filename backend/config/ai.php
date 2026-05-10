<?php

return [
    'n8n_webhook_url' => env('N8N_AUDIT_WEBHOOK_URL'),
    'n8n_webhook_secret' => env('N8N_WEBHOOK_SECRET'),
    'callback_url' => env('AUDIT_CALLBACK_URL'),
];
