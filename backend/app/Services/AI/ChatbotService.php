<?php

namespace App\Services\AI;

use App\Models\Client;
use App\Models\ClientAlias;
use App\Models\Contact;
use App\Models\Contract;
use App\Models\LicenseInventoryDaemon;
use App\Models\LicenseInventoryProduct;
use App\Models\ResourceLink;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ChatbotService
{
    /**
     * Procesa la consulta conversacional utilizando Gemini con Function Calling y fallback.
     *
     * @param array $chatHistory Historial de mensajes en formato [role => user|model, parts => [[text => ...]]]
     * @return array [message => string, provider => string, success => bool]
     */
    public function query(array $chatHistory): array
    {
        $geminiKey = config('ai.gemini_key');
        if (empty($geminiKey)) {
            Log::error("ChatbotService: GEMINI_API_KEY no está configurada.");
            return $this->fallbackToTextChain($chatHistory, 'API Key faltante');
        }

        try {
            return $this->executeGeminiFunctionCallingLoop($chatHistory, $geminiKey);
        } catch (\Exception $e) {
            Log::error("ChatbotService: Excepción en el bucle principal de Gemini: " . $e->getMessage());
            return $this->fallbackToTextChain($chatHistory, $e->getMessage());
        }
    }

    /**
     * Bucle de ejecución de Function Calling para Google Gemini v1beta.
     */
    private function executeGeminiFunctionCallingLoop(array $chatHistory, string $apiKey): array
    {
        // 1. Declarar las herramientas (tools) disponibles para la IA
        $tools = [
            [
                'functionDeclarations' => [
                    [
                        'name' => 'search_clients',
                        'description' => 'Buscar clientes existentes por nombre, alias o dirección Sold-To.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'query' => [
                                    'type' => 'STRING',
                                    'description' => 'Término de búsqueda fuzzy (ej: andaltec, 10303508)'
                                ]
                            ],
                            'required' => ['query']
                        ]
                    ],
                    [
                        'name' => 'get_client_details',
                        'description' => 'Obtener la ficha completa de un cliente por su ID: servidores de licencias, productos activos, contactos y contratos.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'client_id' => [
                                    'type' => 'INTEGER',
                                    'description' => 'ID numérico único del cliente en base de datos'
                                ]
                            ],
                            'required' => ['client_id']
                        ]
                    ],
                    [
                        'name' => 'get_expirations',
                        'description' => 'Diagnosticar qué licencias o productos vencen dentro de un umbral de días.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'days_threshold' => [
                                    'type' => 'INTEGER',
                                    'description' => 'Días límite para la expiración (ej: 30 para los próximos 30 días)'
                                ]
                            ],
                            'required' => ['days_threshold']
                        ]
                    ],
                    [
                        'name' => 'search_servers_by_hardware',
                        'description' => 'Buscar servidores de licencias activos por composite, MAC address o hostname.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'hw_query' => [
                                    'type' => 'STRING',
                                    'description' => 'Composite, dirección MAC o hostname a buscar'
                                ]
                            ],
                            'required' => ['hw_query']
                        ]
                    ],
                    [
                        'name' => 'create_contact',
                        'description' => 'Registrar un nuevo contacto (Contact) para un cliente, con control automático de duplicados.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'client_id' => [
                                    'type' => 'INTEGER',
                                    'description' => 'ID del cliente en base de datos al que asociar el contacto'
                                ],
                                'name' => [
                                    'type' => 'STRING',
                                    'description' => 'Nombre completo del contacto'
                                ],
                                'email' => [
                                    'type' => 'STRING',
                                    'description' => 'Correo electrónico del contacto'
                                ],
                                'role' => [
                                    'type' => 'STRING',
                                    'description' => 'Puesto o rol asignado (ej: Responsable de IT, Técnico de Diseño)'
                                ],
                                'phone' => [
                                    'type' => 'STRING',
                                    'description' => 'Teléfono del contacto (opcional)'
                                ]
                            ],
                            'required' => ['client_id', 'name', 'email']
                        ]
                    ],
                    [
                        'name' => 'get_resource_links',
                        'description' => 'Obtener la lista de enlaces y recursos de Siemens o Moldex3D guardados en el sistema.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => (object) []
                        ]
                    ],
                    [
                        'name' => 'get_contract_details',
                        'description' => 'Obtener el detalle técnico completo de un contrato específico por su ID o número (ej: CONH1006420).',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'contract_id' => [
                                    'type' => 'STRING',
                                    'description' => 'Número de contrato o ID (ej: CONH1006420)'
                                ]
                            ],
                            'required' => ['contract_id']
                        ]
                    ],
                    [
                        'name' => 'search_contacts',
                        'description' => 'Buscar personas de contacto por nombre o correo electrónico en toda la base de datos.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'query' => [
                                    'type' => 'STRING',
                                    'description' => 'Nombre, apellido o correo electrónico del contacto'
                                ]
                            ],
                            'required' => ['query']
                        ]
                    ],
                    [
                        'name' => 'update_contact',
                        'description' => 'Actualizar los datos (rol/puesto, teléfono, email) de un contacto existente.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'contact_id' => [
                                    'type' => 'INTEGER',
                                    'description' => 'ID numérico único del contacto en la base de datos'
                                ],
                                'role' => [
                                    'type' => 'STRING',
                                    'description' => 'Puesto o rol asignado (ej: Responsable de IT, Técnico de Diseño) (opcional)'
                                ],
                                'phone' => [
                                    'type' => 'STRING',
                                    'description' => 'Nuevo teléfono de contacto (opcional)'
                                ],
                                'email' => [
                                    'type' => 'STRING',
                                    'description' => 'Nuevo correo electrónico del contacto (opcional)'
                                ]
                            ],
                            'required' => ['contact_id']
                        ]
                    ],
                    [
                        'name' => 'get_dashboard_summary',
                        'description' => 'Obtener un resumen ejecutivo del sistema (total clientes, licencias críticas <= 30 días, contratos por vencer en el trimestre, clientes sin contacto).',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => (object) []
                        ]
                    ],
                    [
                        'name' => 'list_clients_without_contacts',
                        'description' => 'Listar los clientes que se encuentran huérfanos de información (sin ningún contacto registrado).',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => (object) []
                        ]
                    ]
                ]
            ]
        ];

        // 2. Preparar system instruction para dotar a la IA de personalidad y directrices técnicas
        $systemInstruction = [
            'parts' => [
                [
                    'text' => "Eres Antigravity Chatbot, el asistente de soporte técnico IA de élite para el portal de gestión de licencias DX-License-Manager.\n" .
                             "Tu audiencia son ingenieros y administradores de sistemas. Debes ser extremadamente técnico, preciso y profesional.\n" .
                             "Reglas críticas:\n" .
                             "1. Si el usuario te pide información de clientes, licencias o hardware, debes decidir llamar a las herramientas correspondientes. NUNCA inventes IDs, composites o datos de clientes.\n" .
                             "2. Para datos técnicos (Composite, MAC, fechas ISO, file paths, IDs), usa formato monoespaciado en Markdown (ej: `COMPOSITE=123ABC`).\n" .
                             "3. Utiliza la escala del semáforo de expiración: Vencido (rojo), Próximo a expirar <= 30 días (amarillo), Saludable (verde).\n" .
                             "4. Si te piden añadir contactos, utiliza la herramienta `create_contact` tras buscar el cliente correspondiente con `search_clients`. Ignora emails de ATS/Soporte interno de ATS-Global.\n" .
                             "5. Sé conciso y estructurado. Si la respuesta contiene listas de productos, preséntalos en tablas Markdown compactas y profesionales."
                ]
            ]
        ];

        // Mapear historial de chat a formato Gemini v1beta
        // Gemini espera: {role: "user"|"model", parts: [{text: "..."} | {functionCall: ...} | {functionResponse: ...}]}
        $contents = [];
        foreach ($chatHistory as $msg) {
            $role = $msg['role'] === 'assistant' ? 'model' : $msg['role'];
            $contents[] = [
                'role' => $role,
                'parts' => $msg['parts'] ?? [['text' => $msg['content'] ?? '']]
            ];
        }

        $maxLoops = 5;
        $loopCount = 0;

        while ($loopCount < $maxLoops) {
            $payload = [
                'contents' => $contents,
                'systemInstruction' => $systemInstruction,
                'tools' => $tools,
                'generationConfig' => [
                    'temperature' => 0.1,
                ]
            ];

            $response = Http::timeout(30)->post(
                "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}",
                $payload
            );

            if (!$response->successful()) {
                throw new \Exception("Fallo en API Gemini: (Status " . $response->status() . ") " . $response->body());
            }

            $resData = $response->json();
            $candidate = $resData['candidates'][0] ?? null;

            if (!$candidate) {
                throw new \Exception("Gemini no retornó candidatos.");
            }

            if (isset($candidate['finishReason'])) {
                Log::info("ChatbotService: Gemini candidate finishReason: " . $candidate['finishReason']);
            }

            $parts = $candidate['content']['parts'] ?? [];
            $functionCalls = [];

            // Extraer todas las llamadas a funciones paralelas
            foreach ($parts as $part) {
                if (isset($part['functionCall'])) {
                    $functionCalls[] = $part['functionCall'];
                }
            }

            if (!empty($functionCalls)) {
                // Registrar el llamado completo retornado por la IA en el historial
                $contents[] = [
                    'role' => 'model',
                    'parts' => $parts // Enviamos todas las partes de este turno (que contienen los functionCalls)
                ];

                // Preparar el bloque de respuestas de función
                $functionResponsesParts = [];

                foreach ($functionCalls as $fc) {
                    $funcName = $fc['name'];
                    $args = $fc['args'] ?? [];
                    
                    Log::info("ChatbotService: IA ejecutando llamada paralela a función '{$funcName}' con argumentos: " . json_encode($args));

                    try {
                        $result = $this->callTool($funcName, $args);
                    } catch (\Exception $e) {
                        Log::error("ChatbotService: Error ejecutando función '{$funcName}': " . $e->getMessage());
                        $result = ['error' => $e->getMessage(), 'success' => false];
                    }

                    $functionResponsesParts[] = [
                        'functionResponse' => [
                            'name' => $funcName,
                            'response' => ['output' => $result]
                        ]
                    ];
                }

                // Registrar las respuestas en el historial para el próximo turno de Gemini
                $contents[] = [
                    'role' => 'function',
                    'parts' => $functionResponsesParts
                ];

                $loopCount++;
            } else {
                // Si no hay más llamadas a funciones, este es el mensaje final del asistente
                $finalText = $parts[0]['text'] ?? 'No he podido procesar una respuesta.';
                return [
                    'message' => $finalText,
                    'provider' => 'gemini-flash',
                    'success' => true
                ];
            }
        }

        throw new \Exception("Bucle de function calling superó el límite de llamadas simultáneas.");
    }

    /**
     * Ejecuta una herramienta localmente según el nombre especificado.
     */
    private function callTool(string $name, array $args)
    {
        switch ($name) {
            case 'search_clients':
                return $this->toolSearchClients($args['query'] ?? '');
            case 'get_client_details':
                return $this->toolGetClientDetails((int) ($args['client_id'] ?? 0));
            case 'get_expirations':
                return $this->toolGetExpirations((int) ($args['days_threshold'] ?? 30));
            case 'search_servers_by_hardware':
                return $this->toolSearchServersByHardware($args['hw_query'] ?? '');
            case 'create_contact':
                return $this->toolCreateContact(
                    (int) ($args['client_id'] ?? 0),
                    $args['name'] ?? '',
                    $args['email'] ?? '',
                    $args['role'] ?? null,
                    $args['phone'] ?? null
                );
            case 'get_resource_links':
                return $this->toolGetResourceLinks();
            case 'get_contract_details':
                return $this->toolGetContractDetails($args['contract_id'] ?? '');
            case 'search_contacts':
                return $this->toolSearchContacts($args['query'] ?? '');
            case 'update_contact':
                return $this->toolUpdateContact(
                    (int) ($args['contact_id'] ?? 0),
                    $args['role'] ?? null,
                    $args['phone'] ?? null,
                    $args['email'] ?? null
                );
            case 'get_dashboard_summary':
                return $this->toolGetDashboardSummary();
            case 'list_clients_without_contacts':
                return $this->toolListClientsWithoutContacts();
            default:
                throw new \Exception("Herramienta '{$name}' no está registrada en el sistema.");
        }
    }

    // ==========================================
    // IMPLEMENTACIÓN DE HERRAMIENTAS (TOOLS)
    // ==========================================

    private function toolSearchClients(string $query): array
    {
        $query = trim($query);
        if (empty($query)) {
            return [];
        }

        // Sanitización fuzzy estricta contra inyección SQL
        $query = preg_replace('/[^a-zA-Z0-9\s\-_]/', '', $query);
        if (empty($query)) {
            return [];
        }

        // Buscar por nombre
        $clients = Client::where('name', 'LIKE', "%{$query}%")
            ->limit(10)
            ->get(['id', 'name'])
            ->toArray();

        // Si hay pocos resultados, buscar por alias
        if (count($clients) < 5) {
            $aliasClients = Client::whereHas('aliases', function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%");
            })
            ->limit(10)
            ->get(['id', 'name'])
            ->toArray();

            $clients = array_unique(array_merge($clients, $aliasClients), SORT_REGULAR);
        }

        // Buscar por Sold-To en inventario
        if (count($clients) < 5) {
            $soldToClients = Client::whereHas('inventoryDaemons', function ($q) use ($query) {
                $q->where('sold_to', 'LIKE', "%{$query}%")
                  ->orWhereJsonContains('additional_sold_tos', $query);
            })
            ->limit(10)
            ->get(['id', 'name'])
            ->toArray();

            $clients = array_unique(array_merge($clients, $soldToClients), SORT_REGULAR);
        }

        return array_values($clients);
    }

    private function toolGetClientDetails(int $clientId): array
    {
        $client = Client::with(['aliases', 'contacts', 'contracts'])->find($clientId);

        if (!$client) {
            return ['error' => "No se encontró ningún cliente con el ID {$clientId}"];
        }

        // Obtener daemons y sus productos de inventario
        $daemons = LicenseInventoryDaemon::with(['products'])
            ->where('client_id', $clientId)
            ->get();

        $parsedDaemons = [];
        $today = Carbon::today();

        foreach ($daemons as $d) {
            $parsedProducts = [];
            foreach ($d->products as $p) {
                $exp = $p->expiration_date;
                $status = 'healthy';
                $days = null;

                if ($exp) {
                    $days = $today->diffInDays($exp, false);
                    if ($days < 0) {
                        $status = 'expired';
                    } elseif ($days <= 30) {
                        $status = 'warning';
                    }
                }

                $parsedProducts[] = [
                    'product_code' => $p->product_code,
                    'description' => $p->description,
                    'quantity' => $p->quantity,
                    'expiration_date' => $exp ? $exp->format('Y-m-d') : 'Permanent',
                    'days_remaining' => $days,
                    'status' => $status
                ];
            }

            $parsedDaemons[] = [
                'id' => $d->id,
                'sold_to' => $d->sold_to,
                'additional_sold_tos' => $d->additional_sold_tos,
                'daemon' => $d->daemon,
                'vendor' => $d->vendor,
                'hostname' => $d->hostname,
                'composite' => $d->composite,
                'hardware_id' => $d->hardware_id,
                'version' => $d->version,
                'products' => $parsedProducts
            ];
        }

        return [
            'id' => $client->id,
            'name' => $client->name,
            'aliases' => $client->aliases->pluck('name')->toArray(),
            'contacts' => $client->contacts->map(function ($c) {
                return [
                    'name' => $c->name,
                    'email' => $c->email,
                    'position' => $c->position,
                    'phone' => $c->phone
                ];
            })->toArray(),
            'contracts' => $client->contracts->map(function ($c) {
                return [
                    'contract_number' => $c->contract_number,
                    'vendor' => $c->vendor,
                    'end_date' => $c->end_date ? $c->end_date->format('Y-m-d') : null
                ];
            })->toArray(),
            'license_servers' => $parsedDaemons
        ];
    }

    private function toolGetExpirations(int $daysThreshold): array
    {
        $daysThreshold = max(1, min(365, $daysThreshold));
        $cacheKey = "chatbot_expirations_{$daysThreshold}";

        return Cache::remember($cacheKey, 300, function () use ($daysThreshold) {
            $today = Carbon::today();
            $limitDate = Carbon::today()->addDays($daysThreshold);

            // Buscar productos activos que expiren antes del límite
            $products = LicenseInventoryProduct::with(['daemon.client'])
                ->whereNotNull('expiration_date')
                ->whereBetween('expiration_date', [$today->copy()->subYear(5), $limitDate]) // Incluye vencidos recientes
                ->orderBy('expiration_date', 'asc')
                ->get();

            $expirations = [];
            foreach ($products as $p) {
                $clientName = $p->daemon->client->name ?? 'Desconocido';
                $days = $today->diffInDays($p->expiration_date, false);

                $status = $days < 0 ? 'expired' : 'warning';

                $expirations[] = [
                    'client_name' => $clientName,
                    'client_id' => $p->daemon->client_id ?? null,
                    'sold_to' => $p->daemon->sold_to ?? 'N/A',
                    'daemon' => $p->daemon->daemon ?? 'N/A',
                    'product_code' => $p->product_code,
                    'expiration_date' => $p->expiration_date->format('Y-m-d'),
                    'days_remaining' => $days,
                    'status' => $status
                ];
            }

            return $expirations;
        });
    }

    private function toolSearchServersByHardware(string $hwQuery): array
    {
        $hwQuery = trim($hwQuery);
        if (empty($hwQuery)) {
            return [];
        }

        $daemons = LicenseInventoryDaemon::with(['client'])
            ->where('composite', 'LIKE', "%{$hwQuery}%")
            ->orWhere('hardware_id', 'LIKE', "%{$hwQuery}%")
            ->orWhere('hostname', 'LIKE', "%{$hwQuery}%")
            ->limit(10)
            ->get();

        $results = [];
        foreach ($daemons as $d) {
            $results[] = [
                'daemon_id' => $d->id,
                'client_name' => $d->client->name ?? 'Desconocido',
                'client_id' => $d->client_id,
                'sold_to' => $d->sold_to,
                'daemon' => $d->daemon,
                'hostname' => $d->hostname,
                'composite' => $d->composite,
                'hardware_id' => $d->hardware_id
            ];
        }

        return $results;
    }

    private function toolCreateContact(int $clientId, string $name, string $email, ?string $role, ?string $phone): array
    {
        $client = Client::find($clientId);
        if (!$client) {
            return ['success' => false, 'error' => "El cliente con ID {$clientId} no existe."];
        }

        $email = strtolower(trim($email));
        $name = trim($name);

        if (empty($name) || empty($email)) {
            return ['success' => false, 'error' => "El nombre y el correo electrónico son obligatorios."];
        }

        // Evitar duplicados por correo en el mismo cliente
        $exists = Contact::where('client_id', $clientId)
            ->where('email', $email)
            ->exists();

        if ($exists) {
            return [
                'success' => true,
                'status' => 'duplicated',
                'message' => "El contacto '{$name}' con el correo '{$email}' ya estaba registrado para el cliente '{$client->name}'."
            ];
        }

        $contact = Contact::create([
            'client_id' => $clientId,
            'name' => $name,
            'email' => $email,
            'position' => $role ?? 'Técnico',
            'phone' => $phone,
            'receives_alerts' => true
        ]);

        return [
            'success' => true,
            'status' => 'created',
            'contact_id' => $contact->id,
            'message' => "Contacto '{$name}' creado con éxito para el cliente '{$client->name}'."
        ];
    }

    private function toolGetResourceLinks(): array
    {
        return Cache::remember('chatbot_resource_links', 300, function () {
            return ResourceLink::orderBy('category')
                ->get(['title', 'url', 'category', 'description'])
                ->toArray();
        });
    }

    private function toolGetContractDetails(string $contractId): array
    {
        $contractId = trim($contractId);
        if (empty($contractId)) {
            return ['error' => 'El número de contrato es obligatorio.'];
        }

        $contract = Contract::with(['client'])->where('contract_number', $contractId)->first();
        if (!$contract) {
            // Intento de búsqueda parcial si no hay coincidencia exacta
            $contract = Contract::with(['client'])->where('contract_number', 'LIKE', "%{$contractId}%")->first();
        }

        if (!$contract) {
            return ['error' => "No se encontró ningún contrato con el número o ID '{$contractId}'."];
        }

        // Recuperar daemons de inventario asociados a este cliente para dar contexto adicional
        $daemons = LicenseInventoryDaemon::where('client_id', $contract->client_id)->get(['id', 'sold_to', 'daemon', 'hostname']);

        return [
            'id' => $contract->id,
            'contract_number' => $contract->contract_number,
            'client_name' => $contract->client->name ?? 'Desconocido',
            'client_id' => $contract->client_id,
            'vendor' => $contract->vendor,
            'start_date' => $contract->start_date ? $contract->start_date->format('Y-m-d') : null,
            'end_date' => $contract->end_date ? $contract->end_date->format('Y-m-d') : null,
            'cost_center' => $contract->cost_center,
            'active_daemons' => $daemons->toArray()
        ];
    }

    private function toolSearchContacts(string $query): array
    {
        $query = trim($query);
        if (empty($query)) {
            return [];
        }

        $contacts = Contact::with(['client'])
            ->where('name', 'LIKE', "%{$query}%")
            ->orWhere('email', 'LIKE', "%{$query}%")
            ->limit(15)
            ->get();

        return $contacts->map(function ($c) {
            return [
                'id' => $c->id,
                'name' => $c->name,
                'email' => $c->email,
                'position' => $c->position,
                'phone' => $c->phone,
                'client_name' => $c->client->name ?? 'Desconocido',
                'client_id' => $c->client_id
            ];
        })->toArray();
    }

    private function toolUpdateContact(int $contactId, ?string $role, ?string $phone, ?string $email): array
    {
        $contact = Contact::with(['client'])->find($contactId);
        if (!$contact) {
            return ['success' => false, 'error' => "El contacto con ID {$contactId} no existe."];
        }

        $updates = [];
        if ($role !== null) {
            $updates['position'] = trim($role);
        }
        if ($phone !== null) {
            $updates['phone'] = trim($phone);
        }
        if ($email !== null) {
            $updates['email'] = strtolower(trim($email));
        }

        if (empty($updates)) {
            return ['success' => false, 'error' => 'No se especificaron campos para actualizar.'];
        }

        $contact->update($updates);

        $clientName = $contact->client->name ?? 'Desconocido';

        return [
            'success' => true,
            'contact_id' => $contact->id,
            'message' => "Contacto '{$contact->name}' actualizado con éxito para el cliente '{$clientName}'.",
            'updated_fields' => $updates
        ];
    }

    private function toolGetDashboardSummary(): array
    {
        $today = Carbon::today();
        $limitDate = Carbon::today()->addDays(30);

        $totalClients = Client::count();
        
        $criticalLicenses = LicenseInventoryProduct::whereNotNull('expiration_date')
            ->whereBetween('expiration_date', [$today, $limitDate])
            ->count();

        $expiredLicenses = LicenseInventoryProduct::whereNotNull('expiration_date')
            ->where('expiration_date', '<', $today)
            ->count();

        // Contratos venciendo en este trimestre
        $startOfQuarter = Carbon::today()->startOfQuarter();
        $endOfQuarter = Carbon::today()->endOfQuarter();
        $expiringContracts = Contract::whereBetween('end_date', [$startOfQuarter, $endOfQuarter])->count();

        $orphanClients = Client::doesntHave('contacts')->count();

        return [
            'total_clients' => $totalClients,
            'critical_licenses_30_days' => $criticalLicenses,
            'expired_licenses' => $expiredLicenses,
            'contracts_expiring_current_quarter' => $expiringContracts,
            'clients_without_contacts' => $orphanClients
        ];
    }

    private function toolListClientsWithoutContacts(): array
    {
        $clients = Client::doesntHave('contacts')
            ->limit(20)
            ->get(['id', 'name']);

        return $clients->toArray();
    }

    // ==========================================
    // PLAN DE FALLBACK ALTERNATIVO (TEXT-ONLY)
    // ==========================================

    /**
     * Si Gemini o Function Calling fallan, cae en un modelo puramente textual en DeepSeek o OpenRouter.
     */
    private function fallbackToTextChain(array $chatHistory, string $originalError): array
    {
        Log::warning("ChatbotService: Entrando en cadena de Fallback de Texto debido a: " . $originalError);

        // Intentar DeepSeek Text-only
        $deepseekKey = config('ai.deepseek_key');
        if ($deepseekKey) {
            try {
                $messages = [];
                // Formatear historial
                foreach ($chatHistory as $msg) {
                    $messages[] = [
                        'role' => $msg['role'] === 'assistant' ? 'assistant' : 'user',
                        'content' => $msg['content'] ?? ($msg['parts'][0]['text'] ?? '')
                    ];
                }

                // Insertar directiva del sistema
                array_unshift($messages, [
                    'role' => 'system',
                    'content' => "Eres un asistente de soporte técnico para DX-License-Manager. El motor inteligente de base de datos está temporalmente indisponible, por lo que debes responder conversacionalmente y avisar de forma amable que en este momento solo dispones de respuestas basadas en conocimiento estático."
                ]);

                $response = Http::timeout(15)
                    ->withHeaders([
                        'Authorization' => "Bearer {$deepseekKey}",
                        'Content-Type' => 'application/json'
                    ])
                    ->post('https://api.deepseek.com/chat/completions', [
                        'model' => 'deepseek-chat',
                        'messages' => $messages,
                        'temperature' => 0.5
                    ]);

                if ($response->successful()) {
                    $resData = $response->json();
                    $text = $resData['choices'][0]['message']['content'] ?? '';
                    return [
                        'message' => $text,
                        'provider' => 'deepseek-fallback',
                        'success' => true
                    ];
                }
            } catch (\Exception $e) {
                Log::error("ChatbotService: Fallo en Fallback DeepSeek: " . $e->getMessage());
            }
        }

        // Si todo falla, respuesta local estática muy descriptiva
        return [
            'message' => "⚠️ **Servicio IA Temporalmente Indisponible**\n\nNo he podido establecer conexión con los proveedores de Inteligencia Artificial de la cadena de fallback (Gemini/DeepSeek).\n\nPor favor, verifica el estado de la conexión a internet en el servidor LXC o reintenta la consulta en unos instantes.",
            'provider' => 'local-fallback',
            'success' => false
        ];
    }
}
