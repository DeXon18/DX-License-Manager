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

class BotQueryController extends Controller
{
    /**
     * Handle Telegram/Teams queries.
     */
    public function query(Request $request)
    {
        // 1. Authenticate Request
        $authHeader = $request->header('Authorization');
        $token = null;

        if ($authHeader && preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            $token = trim($matches[1]);
        } else {
            $token = trim($request->header('X-Bot-Token') ?: $request->input('token') ?: '');
        }

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

        // 2. Validate Parameters
        $validated = $request->validate([
            'command' => 'required|string|in:cliente,expiraciones,soldto',
            'argument' => 'nullable|string',
        ]);

        $command = $validated['command'];
        $argument = trim($validated['argument'] ?? '');

        // 3. Process Commands
        switch ($command) {
            case 'cliente':
                return $this->handleCliente($argument);
            case 'expiraciones':
                return $this->handleExpiraciones();
            case 'soldto':
                return $this->handleSoldTo($argument);
        }

        return response()->json(['error' => 'Command not implemented'], 501);
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

            if ($highestSimilarity >= 0.75) {
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
                'products' => $d->products->map(function ($p) {
                    $expDate = $p->expiration_date;
                    $days = null;
                    $status = 'healthy';

                    if ($expDate) {
                        $days = now()->diffInDays($expDate, false);
                        if ($days < 0) {
                            $status = 'expired';
                        } elseif ($days <= 30) {
                            $status = 'warning';
                        }
                    } else {
                        $status = 'permanent';
                    }

                    return [
                        'code' => $p->product_code,
                        'qty' => $p->quantity,
                        'expiration' => $expDate ? $expDate->format('Y-m-d') : 'permanent',
                        'days_left' => $days,
                        'status' => $status
                    ];
                })
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
        $allProducts = LicenseInventoryProduct::with('daemon.client')->get();
        $expiredLicenses = [];
        $expiringLicenses = [];

        foreach ($allProducts as $p) {
            if (!$p->expiration_date || !$p->daemon || !$p->daemon->client) {
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
            } elseif ($days <= 30) {
                $expiringLicenses[] = $item;
            }
        }

        $allContracts = Contract::with('client', 'vendor')->where('status', '!=', 'Baja')->get();
        $expiredContracts = [];
        $expiringContracts = [];

        foreach ($allContracts as $c) {
            if (!$c->end_date || !$c->client) {
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
            } elseif ($days <= 30) {
                $expiringContracts[] = $item;
            }
        }

        return response()->json([
            'status' => 'success',
            'type' => 'expirations',
            'data' => [
                'expired_licenses' => array_slice($expiredLicenses, 0, 50),
                'expiring_licenses' => array_slice($expiringLicenses, 0, 50),
                'expired_contracts' => array_slice($expiredContracts, 0, 50),
                'expiring_contracts' => array_slice($expiringContracts, 0, 50)
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

        $daemons = LicenseInventoryDaemon::with('client', 'products')->get()
            ->filter(function ($d) use ($soldTo) {
                if ($d->sold_to === $soldTo) {
                    return true;
                }
                if (is_array($d->additional_sold_tos) && in_array($soldTo, $d->additional_sold_tos, true)) {
                    return true;
                }
                return false;
            });

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
                'products' => $d->products->map(function ($p) {
                    $expDate = $p->expiration_date;
                    $days = null;
                    $status = 'healthy';

                    if ($expDate) {
                        $days = now()->diffInDays($expDate, false);
                        if ($days < 0) {
                            $status = 'expired';
                        } elseif ($days <= 30) {
                            $status = 'warning';
                        }
                    } else {
                        $status = 'permanent';
                    }

                    return [
                        'code' => $p->product_code,
                        'qty' => $p->quantity,
                        'expiration' => $expDate ? $expDate->format('Y-m-d') : 'permanent',
                        'days_left' => $days,
                        'status' => $status
                    ];
                })
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
        $str1 = mb_strtolower(trim($str1));
        $str2 = mb_strtolower(trim($str2));
        
        if ($str1 === $str2) {
            return 1.0;
        }

        $len1 = mb_strlen($str1);
        $len2 = mb_strlen($str2);
        $maxLen = max($len1, $len2);

        if ($maxLen === 0) {
            return 1.0;
        }

        $distance = levenshtein($str1, $str2);
        return 1 - ($distance / $maxLen);
    }
}
