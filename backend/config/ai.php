<?php

return [
    'n8n_webhook_url' => env('N8N_AUDIT_WEBHOOK_URL', 'https://n8n.dxpro.es/webhook/DX-Control-Center'),
    'callback_url' => env('AUDIT_CALLBACK_URL', 'https://beta.dxpro.es/api/audit/callback'),
];
