<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientAlias;
use App\Models\Contract;
use App\Models\LicenseInventoryDaemon;
use App\Models\LicenseInventoryProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class BotQueryController extends Controller
{
    private const EXPIRATION_WARNING_DAYS  = 30;
    private const FUZZY_MATCH_THRESHOLD    = 0.75;
    private const MAX_PRODUCTS_PER_DAEMON  = 15;
    private const MAX_ITEMS_PER_CATEGORY   = 50;

    /**
     * Handle Telegram/Teams queries.
     */
    public function query(Request $request)
    {
        // 1. Authenticate Request
        $token = $this->extractToken($request);

        $allowedTokens = array_map('trim', array_filter([
            config('ai.bot_token'),
            config('ai.telegram_bot_token'),
            config('ai.n8n_webhook_secret'),
        ]));

        if (empty($allowedTokens)) {
            Log::error("Bot query attempt blocked: No tokens configured on server.");
            return response()->json(['error' => 'Bot token not configured on server'], 500);
        }

        if (!$token || !in_array($token, $allowedTokens, true)) {
            Log::warning("Bot query unauthorized attempt from IP: " . $request->ip());
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // 2. Detect & Parse Telegram Webhook
        $isTelegramWebhook = $request->has('message.text');
        $chatId = null;
        $command = null;
        $argument = '';

        if ($isTelegramWebhook) {
            $chatId = $request->input('message.chat.id');
            $text = trim($request->input('message.text') ?? '');

            if (preg_match('/^\/([a-zA-Z0-9_]+)(?:\s+(.*))?$/', $text, $matches)) {
                $rawCommand = strtolower($matches[1]);
                $argument = trim($matches[2] ?? '');

                if ($rawCommand === 'cliente') {
                    $command = 'cliente';
                } elseif ($rawCommand === 'expiraciones') {
                    $command = 'expiraciones';
                } elseif ($rawCommand === 'soldto') {
                    $command = 'soldto';
                }

                if (!$command) {
                    $this->sendTelegramMessage($chatId, "⚠️ *Comando no reconocido.*\n\nComandos disponibles:\n• `/cliente [Nombre]` - Consultar ficha de cliente\n• `/expiraciones` - Diagnóstico de vencimientos ≤30 días\n• `/soldto [ID]` - Buscar por Sold-To");
                    return response()->json(['status' => 'ignored', 'message' => 'Unknown Telegram command']);
                }
            } else {
                $this->sendTelegramMessage($chatId, "💡 *Portal DX* — Envía un comando válido para interactuar. Ejemplo: `/cliente Gurutzpe` o `/expiraciones`.");
                return response()->json(['status' => 'ignored', 'message' => 'No command found']);
            }
        } else {
            // Standard JSON Request
            $validated = $request->validate([
                'command' => 'required|string|in:cliente,expiraciones,soldto',
                'argument' => 'nullable|string',
            ]);

            $command = $validated['command'];
            $argument = trim($validated['argument'] ?? '');
        }

        // 3. Process Commands
        $jsonResponse = null;
        switch ($command) {
            case 'cliente':
                $jsonResponse = $this->handleCliente($argument);
                break;
            case 'expiraciones':
                $jsonResponse = $this->handleExpiraciones();
                break;
            case 'soldto':
                $jsonResponse = $this->handleSoldTo($argument);
                break;
        }

        // 4. Return Output
        if ($isTelegramWebhook && $jsonResponse) {
            $responseData = $jsonResponse->getData(true);
            $formattedMessage = $this->formatResponseForTelegram($command, $responseData);
            $this->sendTelegramMessage($chatId, $formattedMessage);
            return response()->json(['status' => 'success', 'message' => 'Message sent to Telegram']);
        }

        return $jsonResponse;
    }

    /**
     * Extract bot token from request header or query parameter.
     */
    private function extractToken(Request $request): string
    {
        $authHeader = $request->header('Authorization');

        if ($authHeader && preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return trim($matches[1]);
        }

        return trim(
            $request->header('X-Bot-Token')
            ?: $request->header('X-Telegram-Bot-Api-Secret-Token')
            ?: $request->input('token')
            ?: ''
        );
    }

    /**
     * Process /cliente command.
     */
    protected function handleCliente(string $arg)
    {
        if (empty($arg)) {
            return response()->json(['error' => 'Argument required for client command'], 422);
        }

        // Exact Match
        $client = Client::where('name', 'like', "%{$arg}%")->first();

        // Alias Match
        if (!$client) {
            $alias = ClientAlias::where('name', 'like', "%{$arg}%")->first();
            if ($alias) {
                $client = $alias->client;
            }
        }

        // Fuzzy match Levenshtein (threshold >= 0.75)
        if (!$client) {
            $allClients = Client::all();
            $bestMatch = null;
            $highestSimilarity = 0;

            foreach ($allClients as $c) {
                $sim = $this->calculateSimilarity($arg, $c->name);
                if ($sim > $highestSimilarity) {
                    $highestSimilarity = $sim;
                    $bestMatch = $c;
                }
            }

            if ($highestSimilarity >= self::FUZZY_MATCH_THRESHOLD) {
                $client = $bestMatch;
            }
        }

        if (!$client) {
            return response()->json([
                'status' => 'not_found',
                'message' => "Cliente '{$arg}' no encontrado en base de datos."
            ]);
        }

        // Gather Inventory
        $daemons = $client->inventoryDaemons()->with('products')->get()->map(function ($d) {
            return [
                'daemon' => $d->daemon,
                'sold_to' => $d->sold_to,
                'additional_sold_tos' => $d->additional_sold_tos ?: [],
                'hostname' => $d->hostname,
                'composite' => $d->composite ?: $d->hardware_id,
                'vendor' => $d->vendor,
                'products_count' => $d->products->count(),
                'products' => $d->products->map(fn($p) => $this->mapProduct($p))
            ];
        });

        // Gather Active Contracts
        $contracts = $client->contracts()->with('vendor')->where('status', '!=', 'Baja')->get()->map(function ($c) {
            $expDate = $c->end_date;
            $days = null;
            $status = 'active';

            if ($expDate) {
                $days = now()->diffInDays($expDate, false);
                if ($days < 0) {
                    $status = 'expired';
                } elseif ($days <= 30) {
                    $status = 'expiring';
                }
            }

            return [
                'number' => $c->contract_number,
                'vendor' => $c->vendor ? $c->vendor->name : 'Siemens',
                'product' => $c->type_product,
                'sub_product' => $c->sub_product,
                'expiration' => $expDate ? $expDate->format('Y-m-d') : null,
                'days_left' => $days,
                'status' => $status
            ];
        });

        return response()->json([
            'status' => 'success',
            'type' => 'client_info',
            'data' => [
                'client_id' => $client->id,
                'client_name' => $client->name,
                'daemons' => $daemons,
                'contracts' => $contracts
            ]
        ]);
    }

    /**
     * Process /expiraciones command.
     */
    protected function handleExpiraciones()
    {
        $allProducts = LicenseInventoryProduct::with('daemon.client')
            ->whereNotNull('expiration_date')
            ->where('expiration_date', '<=', now()->addDays(self::EXPIRATION_WARNING_DAYS))
            ->get();
        $expiredLicenses = [];
        $expiringLicenses = [];

        foreach ($allProducts as $p) {
            if (!$p->daemon || !$p->daemon->client) {
                continue;
            }

            $days = now()->diffInDays($p->expiration_date, false);
            $item = [
                'client' => $p->daemon->client->name,
                'daemon' => $p->daemon->daemon,
                'sold_to' => $p->daemon->sold_to,
                'code' => $p->product_code,
                'expiration' => $p->expiration_date->format('Y-m-d'),
                'days_left' => $days
            ];

            if ($days < 0) {
                $expiredLicenses[] = $item;
            } elseif ($days <= self::EXPIRATION_WARNING_DAYS) {
                $expiringLicenses[] = $item;
            }
        }

        $allContracts = Contract::with('client', 'vendor')
            ->where('status', '!=', 'Baja')
            ->whereNotNull('end_date')
            ->where('end_date', '<=', now()->addDays(self::EXPIRATION_WARNING_DAYS))
            ->get();
        $expiredContracts = [];
        $expiringContracts = [];

        foreach ($allContracts as $c) {
            if (!$c->client) {
                continue;
            }

            $days = now()->diffInDays($c->end_date, false);
            $item = [
                'client' => $c->client->name,
                'number' => $c->contract_number,
                'vendor' => $c->vendor ? $c->vendor->name : 'Siemens',
                'product' => $c->type_product,
                'expiration' => $c->end_date->format('Y-m-d'),
                'days_left' => $days
            ];

            if ($days < 0) {
                $expiredContracts[] = $item;
            } elseif ($days <= self::EXPIRATION_WARNING_DAYS) {
                $expiringContracts[] = $item;
            }
        }

        return response()->json([
            'status' => 'success',
            'type' => 'expirations',
            'data' => [
                'expired_licenses' => array_slice($expiredLicenses, 0, self::MAX_ITEMS_PER_CATEGORY),
                'expiring_licenses' => array_slice($expiringLicenses, 0, self::MAX_ITEMS_PER_CATEGORY),
                'expired_contracts' => array_slice($expiredContracts, 0, self::MAX_ITEMS_PER_CATEGORY),
                'expiring_contracts' => array_slice($expiringContracts, 0, self::MAX_ITEMS_PER_CATEGORY)
            ]
        ]);
    }

    /**
     * Process /soldto [ID] command.
     */
    protected function handleSoldTo(string $soldTo)
    {
        if (empty($soldTo)) {
            return response()->json(['error' => 'Argument required for soldto command'], 422);
        }

        $daemons = LicenseInventoryDaemon::with('client', 'products')
            ->where(function ($q) use ($soldTo) {
                $q->where('sold_to', $soldTo)
                  ->orWhereJsonContains('additional_sold_tos', $soldTo);
            })
            ->get();

        if ($daemons->isEmpty()) {
            return response()->json([
                'status' => 'not_found',
                'message' => "No se encontró inventario asociado al Sold-To '{$soldTo}'."
            ]);
        }

        $results = $daemons->map(function ($d) {
            return [
                'client_name' => $d->client ? $d->client->name : 'Desconocido',
                'daemon' => $d->daemon,
                'sold_to' => $d->sold_to,
                'additional_sold_tos' => $d->additional_sold_tos ?: [],
                'hostname' => $d->hostname,
                'composite' => $d->composite ?: $d->hardware_id,
                'vendor' => $d->vendor,
                'products_count' => $d->products->count(),
                'products' => $d->products->map(fn($p) => $this->mapProduct($p))
            ];
        })->values();

        return response()->json([
            'status' => 'success',
            'type' => 'soldto_info',
            'data' => [
                'sold_to' => $soldTo,
                'results' => $results
            ]
        ]);
    }

    /**
     * Calculate similarity between two strings (0 to 1).
     */
    private function calculateSimilarity(string $str1, string $str2): float
    {
        $normalize = fn($s) => mb_strtolower(trim(
            iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s)
        ));

        $str1 = $normalize($str1);
        $str2 = $normalize($str2);

        if ($str1 === $str2) return 1.0;

        $maxLen = max(strlen($str1), strlen($str2));
        if ($maxLen === 0) return 1.0;

        return 1 - (levenshtein($str1, $str2) / $maxLen);
    }

    /**
     * Map active products with formatted metrics and status tags.
     */
    private function mapProduct(LicenseInventoryProduct $p): array
    {
        $expDate = $p->expiration_date ? \Carbon\Carbon::parse($p->expiration_date) : null;
        $days = $expDate ? now()->diffInDays($expDate, false) : null;

        $status = match(true) {
            $expDate === null                      => 'permanent',
            $days < 0                              => 'expired',
            $days <= self::EXPIRATION_WARNING_DAYS => 'warning',
            default                                => 'healthy',
        };

        return [
            'code'       => $p->product_code,
            'qty'        => $p->quantity,
            'expiration' => $expDate ? $expDate->format('Y-m-d') : 'permanent',
            'days_left'  => $days,
            'status'     => $status,
        ];
    }

    /**
     * Send Markdown message to Telegram chat.
     */
    protected function sendTelegramMessage($chatId, string $message)
    {
        $botToken = config('services.telegram-bot-api.token') ?: config('ai.telegram_bot_token');
        if (!$botToken) {
            Log::error("Cannot send Telegram message: Token not configured.");
            return;
        }

        $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
        
        try {
            $response = Http::timeout(10)->post($url, [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown',
            ]);

            if (!$response->successful()) {
                Log::error("Telegram API error: " . $response->body());
            }
        } catch (\Exception $e) {
            Log::error("Failed to send Telegram message: " . $e->getMessage());
        }
    }

    /**
     * Format raw response data into elegant Markdown for Telegram.
     */
    protected function formatResponseForTelegram(string $command, array $response): string
    {
        if (($response['status'] ?? '') !== 'success') {
            return "⚠️ *" . ($response['message'] ?? 'Error desconocido al procesar la solicitud.') . "*";
        }

        $data = $response['data'] ?? [];

        switch ($command) {
            case 'cliente':
                $name = $data['client_name'] ?? 'Cliente';
                $daemons = $data['daemons'] ?? [];
                $contracts = $data['contracts'] ?? [];

                $msg = "🏢 *Ficha de Cliente:*\n*{$name}*\n\n";

                if (!empty($daemons)) {
                    $msg .= "🔐 *Inventario de Licencias:*\n";
                    foreach ($daemons as $d) {
                        $daemonName = strtoupper($d['daemon'] ?? 'Desconocido');
                        $vendor = $d['vendor'] ?? 'Siemens';
                        $soldTo = $d['sold_to'] ?? '';
                        $hostname = $d['hostname'] ?? '';
                        $composite = $d['composite'] ?? '';
                        $prodCount = $d['products_count'] ?? 0;

                        $msg .= "• *{$daemonName}* ({$vendor})\n";
                        $msg .= "  Sold-To: `{$soldTo}`\n";
                        if ($hostname) $msg .= "  Host: `{$hostname}`\n";
                        if ($composite) $msg .= "  Hardware ID / Composite: `{$composite}`\n";
                        $msg .= "  Productos registrados ({$prodCount}):\n";

                        if (!empty($d['products'])) {
                            $products = array_slice($d['products'], 0, 15);
                            foreach ($products as $p) {
                                $code = $p['code'] ?? '';
                                $qty = $p['qty'] ?? 1;
                                $exp = $p['expiration'] ?? 'permanent';
                                $status = $p['status'] ?? 'healthy';

                                $statusEmoji = '🟢';
                                if ($status === 'expired') $statusEmoji = '🔴';
                                if ($status === 'warning') $statusEmoji = '🟡';
                                if ($status === 'permanent') $statusEmoji = '🔵';

                                $msg .= "  └ `{$code}` x{$qty} ({$exp}) {$statusEmoji}\n";
                            }
                            if (count($d['products']) > 15) {
                                $msg .= "  └ ... y " . (count($d['products']) - 15) . " productos más.\n";
                            }
                        } else {
                            $msg .= "  └ (Sin productos activos)\n";
                        }
                        $msg .= "\n";
                    }
                } else {
                    $msg .= "🔐 *Inventario de Licencias:* (Ninguno registrado)\n\n";
                }

                if (!empty($contracts)) {
                    $msg .= "💼 *Contratos Activos:*\n";
                    foreach ($contracts as $c) {
                        $num = $c['number'] ?? '';
                        $vendor = $c['vendor'] ?? 'Siemens';
                        $prod = $c['product'] ?? '';
                        $sub = $c['sub_product'] ?? '';
                        $exp = $c['expiration'] ?? '';
                        $days = $c['days_left'] !== null ? round($c['days_left']) : null;
                        $status = $c['status'] ?? 'active';

                        $statusEmoji = '🟢';
                        if ($status === 'expired') $statusEmoji = '🔴';
                        if ($status === 'expiring') $statusEmoji = '🟡';

                        $daysStr = $days !== null ? " ({$days} días restantes)" : "";
                        $msg .= "• *{$num}* [{$vendor}] - {$prod} / {$sub}\n";
                        $msg .= "  Expira: `{$exp}`{$daysStr} {$statusEmoji}\n";
                    }
                } else {
                    $msg .= "💼 *Contratos Activos:* (Ninguno activo)\n";
                }

                return $msg;

            case 'soldto':
                $soldTo = $data['sold_to'] ?? '';
                $results = $data['results'] ?? [];

                $msg = "🔍 *Resultado Sold-To: `{$soldTo}`*\n\n";

                foreach ($results as $d) {
                    $clientName = $d['client_name'] ?? 'Cliente';
                    $daemonName = strtoupper($d['daemon'] ?? 'Desconocido');
                    $vendor = $d['vendor'] ?? 'Siemens';
                    $hostname = $d['hostname'] ?? '';
                    $composite = $d['composite'] ?? '';
                    $prodCount = $d['products_count'] ?? 0;

                    $msg .= "🏢 *Cliente:* *{$clientName}*\n";
                    $msg .= "🔐 *Daemon:* *{$daemonName}* ({$vendor})\n";
                    if ($hostname) $msg .= "  Host: `{$hostname}`\n";
                    if ($composite) $msg .= "  Hardware ID / Composite: `{$composite}`\n";
                    $msg .= "  Productos registrados ({$prodCount}):\n";

                    if (!empty($d['products'])) {
                        $products = array_slice($d['products'], 0, 15);
                        foreach ($products as $p) {
                            $code = $p['code'] ?? '';
                            $qty = $p['qty'] ?? 1;
                            $exp = $p['expiration'] ?? 'permanent';
                            $status = $p['status'] ?? 'healthy';

                            $statusEmoji = '🟢';
                            if ($status === 'expired') $statusEmoji = '🔴';
                            if ($status === 'warning') $statusEmoji = '🟡';
                            if ($status === 'permanent') $statusEmoji = '🔵';

                            $msg .= "  └ `{$code}` x{$qty} ({$exp}) {$statusEmoji}\n";
                        }
                        if (count($d['products']) > 15) {
                            $msg .= "  └ ... y " . (count($d['products']) - 15) . " productos más.\n";
                        }
                    } else {
                        $msg .= "  └ (Sin productos activos)\n";
                    }
                    $msg .= "\n";
                }

                return $msg;

            case 'expiraciones':
                $expiredLicenses = $this->consolidateLicenses($data['expired_licenses'] ?? []);
                $expiringLicenses = $this->consolidateLicenses($data['expiring_licenses'] ?? []);

                $msg = "⚠️ *Alerta de Expiraciones Portal DX*\n\n";
                $hasCritical = false;

                if (!empty($expiredLicenses)) {
                    $hasCritical = true;
                    $msg .= "🔴 *Licencias/Sold-To Caducados:*\n";
                    foreach ($expiredLicenses as $l) {
                        $client = $l['client'] ?? '';
                        $soldTo = $l['sold_to'] ?? '';
                        $daemon = strtoupper($l['daemon'] ?? '');
                        $exp = $l['expiration'] ?? '';
                        $days = abs(round($l['days_left'] ?? 0));
                        $prodCount = count($l['products']);
                        
                        $msg .= "• *{$client}* (Sold-To: `{$soldTo}` / {$daemon}) - Vencido el `{$exp}` (hace {$days} días) [{$prodCount} prod]\n";
                    }
                    $msg .= "\n";
                }

                if (!empty($expiringLicenses)) {
                    $hasCritical = true;
                    $msg .= "🟡 *Licencias/Sold-To Próximas a Caducar (≤30 días):*\n";
                    foreach ($expiringLicenses as $l) {
                        $client = $l['client'] ?? '';
                        $soldTo = $l['sold_to'] ?? '';
                        $daemon = strtoupper($l['daemon'] ?? '');
                        $exp = $l['expiration'] ?? '';
                        $days = round($l['days_left'] ?? 0);
                        $prodCount = count($l['products']);
                        
                        $msg .= "• *{$client}* (Sold-To: `{$soldTo}` / {$daemon}) - Expira el `{$exp}` (en {$days} días) [{$prodCount} prod]\n";
                    }
                    $msg .= "\n";
                }

                if (!$hasCritical) {
                    $msg .= "🟢 *¡Todo en orden! No hay licencias caducadas o próximas a caducar en los siguientes 30 días.*\n";
                }

                return $msg;
        }

        return "⚠️ *Comando procesado correctamente.*";
    }

    /**
     * Group and deduplicate license list by Client + Sold-To + Daemon + Expiration.
     */
    protected function consolidateLicenses(array $licenses): array
    {
        $grouped = [];
        foreach ($licenses as $l) {
            $client = $l['client'] ?? '';
            $soldTo = $l['sold_to'] ?? '';
            $daemon = $l['daemon'] ?? '';
            $exp = $l['expiration'] ?? '';
            
            $key = $client . '_' . $soldTo . '_' . $daemon . '_' . $exp;
            
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'client' => $client,
                    'sold_to' => $soldTo,
                    'daemon' => $daemon,
                    'expiration' => $exp,
                    'days_left' => $l['days_left'] ?? 0,
                    'products' => [$l['code'] ?? '']
                ];
            } else {
                $code = $l['code'] ?? '';
                if ($code && !in_array($code, $grouped[$key]['products'], true)) {
                    $grouped[$key]['products'][] = $code;
                }
            }
        }
        
        return array_values($grouped);
    }
}
