<?php

namespace App\Services\AI;

use App\Models\Client;
use App\Models\ClientAlias;
use App\Models\Contact;
use App\Models\Contract;
use App\Models\EnterpriseCloudAccount;
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
        // 1. Verificar Caché Semántica
        $cacheKey = 'chatbot_query_' . md5(json_encode($chatHistory));
        
        if (Cache::has($cacheKey)) {
            Log::info("ChatbotService: Sirviendo respuesta desde Caché Semántica (0 tokens consumidos).");
            $cachedResponse = Cache::get($cacheKey);
            // Evitar que el controlador registre tokens repetidos
            $cachedResponse['usage_metadata'] = null; 
            $cachedResponse['provider'] = 'redis-cache';
            return $cachedResponse;
        }

        $openrouterKey = config('ai.openrouter_key');
        if (empty($openrouterKey)) {
            Log::error("ChatbotService: OPENROUTER_API_KEY no está configurada.");
            $response = $this->fallbackToTextChain($chatHistory, 'API Key faltante');
        } else {
            try {
                // Leer modelo primario desde la base de datos de rutas (AiRoute)
                $route = \App\Models\AiRoute::with(['primaryModel', 'fallbackModel'])->find('chatbot');
                $modelId = $route && $route->primaryModel ? $route->primaryModel->openrouter_id : 'meta-llama/llama-4-maverick:free';

                $response = $this->executeOpenAIFunctionCallingLoop(
                    'https://openrouter.ai/api/v1/chat/completions',
                    $openrouterKey,
                    $modelId,
                    $chatHistory,
                    ['HTTP-Referer' => 'https://beta.dxpro.es', 'X-Title' => 'DX License Manager'],
                    'openrouter'
                );
            } catch (\Exception $e) {
                // Si el error es 429 (Rate Limit agotado) o 404 (Modelo no encontrado / deprecado)
                if (in_array($e->getCode(), [429, 404])) {
                    $route = \App\Models\AiRoute::with(['fallbackModel'])->find('chatbot');
                    if ($route && $route->fallbackModel) {
                        Log::warning("ChatbotService: Error {$e->getCode()} en modelo primario ({$modelId}), saltando a fallback de pago: {$route->fallbackModel->openrouter_id}");
                        try {
                            $response = $this->executeOpenAIFunctionCallingLoop(
                                'https://openrouter.ai/api/v1/chat/completions',
                                $openrouterKey,
                                $route->fallbackModel->openrouter_id,
                                $chatHistory,
                                ['HTTP-Referer' => 'https://beta.dxpro.es', 'X-Title' => 'DX License Manager'],
                                'openrouter'
                            );
                            $response['used_fallback'] = true;
                        } catch (\Exception $fallbackE) {
                            Log::error("ChatbotService: Fallback falló: " . $fallbackE->getMessage());
                            $response = $this->fallbackToTextChain($chatHistory, "Error {$e->getCode()} y Fallback falló: " . $fallbackE->getMessage());
                        }
                    } else {
                        Log::error("ChatbotService: Error {$e->getCode()} y no hay fallback configurado.");
                        $response = $this->fallbackToTextChain($chatHistory, "Servicio saturado o modelo no disponible sin respaldo configurado.");
                    }
                } else {
                    Log::error("ChatbotService: Excepción en el bucle principal de OpenRouter: " . $e->getMessage());
                    $errorMsg = $e->getCode() >= 500 ? "El servicio central de Inteligencia Artificial está temporalmente saturado o en mantenimiento. Por favor, inténtelo de nuevo en unos minutos." : $e->getMessage();
                    $response = $this->fallbackToTextChain($chatHistory, $errorMsg);
                }
            }
        }

        // 2. Guardar en caché si fue exitoso (TTL 12h)
        if ($response['success']) {
            Cache::put($cacheKey, $response, now()->addHours(12));
        }

        return $response;
    }

    /**
     * Clasificador ultraligero para ahorrar tokens si la pregunta es teórica.
     */
    private function isDatabaseQueryNeeded(array $chatHistory, string $apiKey): bool
    {
        $lastMessage = end($chatHistory);
        $userText = $lastMessage['parts'][0]['text'] ?? ($lastMessage['content'] ?? '');
        if (empty($userText)) return true;

        $payload = [
            'contents' => [
                ['role' => 'user', 'parts' => [['text' => "Pregunta del usuario: \"{$userText}\"\n\nClasifica la intención. Responde ÚNICAMENTE con 'DB' si la pregunta requiere buscar datos de clientes, licencias, contratos, fechas de expiración o contactos en la base de datos. Responde ÚNICAMENTE con 'GENERAL' si es una pregunta teórica, de soporte, saludos, o si no requiere buscar en la BD."]]]
            ],
            'generationConfig' => [
                'temperature' => 0.0,
                'maxOutputTokens' => 10,
            ]
        ];

        try {
            $response = Http::timeout(5)->post(
                "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key={$apiKey}",
                $payload
            );
            
            if ($response->successful()) {
                $resData = $response->json();
                $text = trim($resData['candidates'][0]['content']['parts'][0]['text'] ?? 'DB');
                if (strtoupper($text) === 'GENERAL') {
                    Log::info("ChatbotService: Intención clasificada como GENERAL. Omitiendo herramientas.");
                    return false;
                }
            }
        } catch (\Exception $e) {
            // Ignoramos error y asumimos BD por seguridad
        }
        
        return true;
    }

    /**
     * Bucle de ejecución de Function Calling para Google Gemini v1beta.
     */
    private function executeGeminiFunctionCallingLoop(array $chatHistory, string $apiKey, bool $useTools = true): array
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
                    ],
                    [
                        'name' => 'create_enterprise_cloud_account',
                        'description' => 'Añadir o crear una cuenta Enterprise Cloud (asociando Sold-To y email del admin) a un cliente.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'client_id' => [
                                    'type' => 'INTEGER',
                                    'description' => 'ID numérico único del cliente'
                                ],
                                'sold_to' => [
                                    'type' => 'STRING',
                                    'description' => 'Sold-To de la licencia asociado a esta cuenta'
                                ],
                                'account_id' => [
                                    'type' => 'STRING',
                                    'description' => 'ID de la cuenta Enterprise Cloud'
                                ],
                                'admin_email' => [
                                    'type' => 'STRING',
                                    'description' => 'Email del administrador de la cuenta'
                                ]
                            ],
                            'required' => ['client_id', 'sold_to', 'account_id', 'admin_email']
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
                             "4. Si te piden añadir contactos, utiliza la herramienta `create_contact` tras buscar el cliente correspondiente con `search_clients`. Si la búsqueda devuelve varios clientes, PREGUNTA al usuario en cuál de ellos insertarlos antes de crear nada. Ignora emails de ATS/Soporte interno de ATS-Global.\n" .
                             "5. Sé conciso y estructurado. Si la respuesta contiene listas de productos, preséntalos en tablas Markdown compactas y profesionales.\n" .
                             "6. Si te piden añadir un Enterprise Cloud Account, PRIMERO busca el cliente por Sold-To o dominio (email) usando `search_clients`. Si la búsqueda no es concluyente, PREGUNTA al usuario a qué cliente se refiere antes de crear nada. Al confirmar, muestra SOLO: Cliente, Sold-To, Account ID y Admin Email. NO añades columnas como WebKey."
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

        $toolResults = [];
        $usageMetadata = null;

        while ($loopCount < $maxLoops) {
            $payload = [
                'contents' => $contents,
                'systemInstruction' => $systemInstruction,
                'generationConfig' => [
                    'temperature' => 0.1,
                ]
            ];
            
            if ($useTools) {
                $payload['tools'] = $tools;
            }

            $response = Http::timeout(30)->post(
                "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key={$apiKey}",
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

            if (isset($resData['usageMetadata'])) {
                $usageMetadata = $resData['usageMetadata'];
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
                        $toolResults[$funcName] = $result;
                    } catch (\Exception $e) {
                        Log::error("ChatbotService: Error ejecutando función '{$funcName}': " . $e->getMessage());
                        $result = ['error' => $e->getMessage(), 'success' => false];
                        $toolResults[$funcName] = $result;
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
                    'provider' => 'gemini',
                    'model' => 'gemini-1.5-flash',
                    'success' => true,
                    'usage_metadata' => $usageMetadata,
                    'data' => $toolResults
                ];
            }
        }

        throw new \Exception("Bucle de function calling superó el límite de llamadas simultáneas.");
    }

    /**
     * Herramientas en formato OpenAI (para DeepSeek, OpenRouter, Groq).
     */
    private function getOpenAIToolsDefinition(): array
    {
        return [
            ['type' => 'function', 'function' => ['name' => 'search_clients', 'description' => 'Buscar clientes existentes por nombre, alias o dirección Sold-To.', 'parameters' => ['type' => 'object', 'properties' => ['query' => ['type' => 'string', 'description' => 'Término de búsqueda fuzzy (ej: andaltec, 10303508)']], 'required' => ['query']]]],
            ['type' => 'function', 'function' => ['name' => 'get_client_details', 'description' => 'Obtener la ficha completa de un cliente por su ID: servidores, productos, contactos y contratos.', 'parameters' => ['type' => 'object', 'properties' => ['client_id' => ['type' => 'integer', 'description' => 'ID numérico único del cliente']], 'required' => ['client_id']]]],
            ['type' => 'function', 'function' => ['name' => 'get_expirations', 'description' => 'Diagnosticar qué licencias o productos vencen dentro de un umbral de días.', 'parameters' => ['type' => 'object', 'properties' => ['days_threshold' => ['type' => 'integer', 'description' => 'Días límite para la expiración (ej: 30)']], 'required' => ['days_threshold']]]],
            ['type' => 'function', 'function' => ['name' => 'search_servers_by_hardware', 'description' => 'Buscar servidores de licencias activos por composite, MAC address o hostname.', 'parameters' => ['type' => 'object', 'properties' => ['hw_query' => ['type' => 'string', 'description' => 'Composite, MAC o hostname a buscar']], 'required' => ['hw_query']]]],
            ['type' => 'function', 'function' => ['name' => 'create_contact', 'description' => 'Crear un nuevo contacto técnico para un cliente.', 'parameters' => ['type' => 'object', 'properties' => ['client_id' => ['type' => 'integer'], 'name' => ['type' => 'string'], 'email' => ['type' => 'string'], 'role' => ['type' => 'string'], 'phone' => ['type' => 'string']], 'required' => ['client_id', 'name', 'email']]]],
            ['type' => 'function', 'function' => ['name' => 'get_resource_links', 'description' => 'Obtener enlaces a recursos técnicos y documentación del portal.', 'parameters' => ['type' => 'object', 'properties' => (object)[]]]],
            ['type' => 'function', 'function' => ['name' => 'get_contract_details', 'description' => 'Obtener el detalle completo de un contrato específico por su número o ID.', 'parameters' => ['type' => 'object', 'properties' => ['contract_id' => ['type' => 'string', 'description' => 'Número de contrato (ej: CONH1006420) o ID numérico']], 'required' => ['contract_id']]]],
            ['type' => 'function', 'function' => ['name' => 'search_contacts', 'description' => 'Buscar un contacto por email o nombre entre todos los clientes.', 'parameters' => ['type' => 'object', 'properties' => ['query' => ['type' => 'string', 'description' => 'Email o nombre del contacto']], 'required' => ['query']]]],
            ['type' => 'function', 'function' => ['name' => 'update_contact', 'description' => 'Editar rol, teléfono o email de un contacto existente.', 'parameters' => ['type' => 'object', 'properties' => ['contact_id' => ['type' => 'integer'], 'role' => ['type' => 'string'], 'phone' => ['type' => 'string'], 'email' => ['type' => 'string']], 'required' => ['contact_id']]]],
            ['type' => 'function', 'function' => ['name' => 'get_dashboard_summary', 'description' => 'Obtener un resumen ejecutivo del sistema (total clientes, licencias críticas, contratos por vencer).', 'parameters' => ['type' => 'object', 'properties' => (object)[]]]],
            ['type' => 'function', 'function' => ['name' => 'list_clients_without_contacts', 'description' => 'Listar clientes sin ningún contacto registrado.', 'parameters' => ['type' => 'object', 'properties' => (object)[]]]],
            ['type' => 'function', 'function' => ['name' => 'create_enterprise_cloud_account', 'description' => 'Añadir cuenta Enterprise Cloud a un cliente.', 'parameters' => ['type' => 'object', 'properties' => ['client_id' => ['type' => 'integer'], 'sold_to' => ['type' => 'string'], 'account_id' => ['type' => 'string'], 'admin_email' => ['type' => 'string']], 'required' => ['client_id', 'sold_to', 'account_id', 'admin_email']]]],
        ];
    }

    /**
     * Loop de function calling reutilizable para cualquier API OpenAI-compatible
     * (DeepSeek, OpenRouter, Groq, etc.). Devuelve el mismo contrato que el loop de Gemini.
     */
    private function executeOpenAIFunctionCallingLoop(
        string $endpoint,
        string $apiKey,
        string $model,
        array  $chatHistory,
        array  $extraHeaders = [],
        string $providerName = 'openai-compatible'
    ): array {
        $systemPrompt = "Eres Antigravity Chatbot, el asistente de soporte técnico IA de élite para el portal de gestión de licencias DX-License-Manager.\n" .
                        "Tu audiencia son ingenieros y administradores de sistemas. Sé técnico, preciso y profesional.\n" .
                        "Reglas:\n" .
                        "1. Para info de clientes, licencias o hardware, usa SIEMPRE las herramientas disponibles. NUNCA inventes datos.\n" .
                        "2. Datos técnicos (Composite, MAC, fechas, IDs) en formato monoespaciado Markdown.\n" .
                        "3. Semáforo: Vencido (rojo), <= 30 días (amarillo), Saludable (verde).\n" .
                        "4. Si te piden añadir contactos, utiliza la herramienta `create_contact` tras buscar el cliente correspondiente con `search_clients`. Si la búsqueda devuelve varios clientes, PREGUNTA al usuario en cuál de ellos insertarlos antes de crear nada. Ignora emails de ATS/Soporte interno de ATS-Global.\n" .
                        "5. Listas de productos en tablas Markdown compactas.\n" .
                        "6. Para añadir cuentas Cloud, busca PRIMERO el cliente. Si hay dudas, PREGUNTA. Al confirmar, muestra SOLO Cliente, Sold-To, Account ID y Admin Email. SIN WebKey.";

        $messages = [['role' => 'system', 'content' => $systemPrompt]];
        foreach ($chatHistory as $msg) {
            $messages[] = [
                'role'    => $msg['role'] === 'assistant' ? 'assistant' : 'user',
                'content' => $msg['content'] ?? ($msg['parts'][0]['text'] ?? '')
            ];
        }

        $tools      = $this->getOpenAIToolsDefinition();
        $maxLoops   = 5;
        $loopCount  = 0;
        $toolResults = [];

        while ($loopCount < $maxLoops) {
            $headers = array_merge(
                ['Authorization' => "Bearer {$apiKey}", 'Content-Type' => 'application/json'],
                $extraHeaders
            );

            $response = Http::timeout(30)
                ->withHeaders($headers)
                ->post($endpoint, [
                    'model'       => $model,
                    'messages'    => $messages,
                    'tools'       => $tools,
                    'temperature' => 0.1,
                ]);

            if (!$response->successful()) {
                throw new \Exception("Fallo en API {$providerName}: (Status " . $response->status() . ") " . $response->body(), $response->status());
            }

            $resData   = $response->json();
            $message   = $resData['choices'][0]['message'] ?? null;

            if (!$message) {
                throw new \Exception("{$providerName} no retornó mensaje válido.");
            }

            $toolCalls = $message['tool_calls'] ?? [];

            if (!empty($toolCalls)) {
                // Añadir turno del asistente con las llamadas a herramientas
                $messages[] = $message;

                foreach ($toolCalls as $tc) {
                    $funcName   = $tc['function']['name'];
                    $args       = json_decode($tc['function']['arguments'], true) ?? [];
                    $toolCallId = $tc['id'];

                    Log::info("ChatbotService: [{$providerName}] ejecutando función '{$funcName}' con args: " . json_encode($args));

                    try {
                        $result = $this->callTool($funcName, $args);
                        $toolResults[$funcName] = $result;
                    } catch (\Exception $e) {
                        $result = ['error' => $e->getMessage(), 'success' => false];
                        $toolResults[$funcName] = $result;
                    }

                    $messages[] = [
                        'role'         => 'tool',
                        'tool_call_id' => $toolCallId,
                        'content'      => json_encode($result)
                    ];
                }
                $loopCount++;
            } else {
                $finalText = $message['content'] ?? 'No he podido procesar una respuesta.';
                return [
                    'message'        => $finalText,
                    'provider'       => $providerName,
                    'model'          => $model,
                    'success'        => true,
                    'usage_metadata' => $resData['usage'] ?? null,
                    'data'           => $toolResults
                ];
            }
        }

        throw new \Exception("Bucle OpenAI [{$providerName}] superó el límite de iteraciones.");
    }

    private function checkAndIncrementMutationLimit(): bool
    {
        $session = request()->hasSession() ? request()->session() : null;
        if ($session) {
            $count = $session->get('chatbot_mutations_count', 0);
            if ($count >= 50) {
                return false;
            }
            $session->put('chatbot_mutations_count', $count + 1);
        }
        return true;
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
                if (!$this->checkAndIncrementMutationLimit()) {
                    return ['success' => false, 'error' => 'Límite de mutaciones de contacto por sesión superado (máximo 50). Por favor, contacta con un administrador.'];
                }
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
                if (!$this->checkAndIncrementMutationLimit()) {
                    return ['success' => false, 'error' => 'Límite de mutaciones de contacto por sesión superado (máximo 50). Por favor, contacta con un administrador.'];
                }
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
            case 'create_enterprise_cloud_account':
                if (!$this->checkAndIncrementMutationLimit()) {
                    return ['success' => false, 'error' => 'Límite de mutaciones por sesión superado. Por favor, recarga la página.'];
                }
                return $this->toolCreateEnterpriseCloudAccount(
                    (int) ($args['client_id'] ?? 0),
                    $args['sold_to'] ?? '',
                    $args['account_id'] ?? '',
                    $args['admin_email'] ?? ''
                );
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

        // Sanitización fuzzy estricta (permitiendo @ y . para correos y dominios)
        $query = preg_replace('/[^a-zA-Z0-9\s\-_@.]/', '', $query);
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

        // Buscar por email/dominio en contactos
        if (count($clients) < 5) {
            $contactClients = Client::whereHas('contacts', function ($q) use ($query) {
                $q->where('email', 'LIKE', "%{$query}%");
            })
            ->limit(10)
            ->get(['id', 'name'])
            ->toArray();

            $clients = array_unique(array_merge($clients, $contactClients), SORT_REGULAR);
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
                ->whereBetween('expiration_date', [$today->copy()->subYears(5), $limitDate]) // Incluye vencidos recientes
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
            'end_date' => $contract->end_date ? Carbon::parse($contract->end_date)->format('Y-m-d') : null,
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

    private function toolCreateEnterpriseCloudAccount(int $clientId, string $soldTo, string $accountId, string $adminEmail): array
    {
        $client = Client::find($clientId);
        if (!$client) {
            return ['success' => false, 'error' => "El cliente con ID {$clientId} no existe."];
        }

        try {
            $account = EnterpriseCloudAccount::create([
                'client_id' => $clientId,
                'sold_to' => $soldTo,
                'account_id' => $accountId,
                'admin_email' => $adminEmail
            ]);

            return [
                'success' => true,
                'message' => "Cuenta Enterprise Cloud guardada correctamente.",
                'account' => [
                    'id' => $account->id,
                    'sold_to' => $account->sold_to,
                    'account_id' => $account->account_id,
                    'admin_email' => $account->admin_email
                ]
            ];
        } catch (\Exception $e) {
            Log::error("ChatbotService: Error al crear Enterprise Cloud Account: " . $e->getMessage());
            return [
                'success' => false,
                'error' => "Error al guardar en base de datos: " . $e->getMessage()
            ];
        }
    }

    // ==========================================
    // CADENA DE FALLBACK CON FUNCTION CALLING
    // ==========================================

    /**
     * Si Gemini falla, reintenta con DeepSeek → OpenRouter → Groq,
     * todos con function calling (acceso completo a la base de datos).
     */
    private function fallbackToTextChain(array $chatHistory, string $originalError): array
    {
        Log::warning("ChatbotService: Entrando en cadena de Fallback debido a: " . $originalError);

        // Fallback 1: API de pago directa de DeepSeek
        $deepseekKey = config('ai.deepseek_key');
        if ($deepseekKey) {
            try {
                return $this->executeOpenAIFunctionCallingLoop(
                    'https://api.deepseek.com/chat/completions',
                    $deepseekKey,
                    'deepseek-chat',
                    $chatHistory,
                    [],
                    'deepseek'
                );
            } catch (\Exception $e) {
                Log::error("ChatbotService: Fallo en Fallback DeepSeek de pago: " . $e->getMessage());
            }
        }

        // Último recurso: mensaje estático
        return [
            'message'        => "⚠️ **Servicio IA Temporalmente Indisponible**\n\nNo he podido establecer conexión con ningún proveedor de IA de pago.\n\nPor favor, reintenta en unos instantes.",
            'provider'       => 'local-fallback',
            'success'        => false,
            'usage_metadata' => null,
            'data'           => []
        ];
    }
}
