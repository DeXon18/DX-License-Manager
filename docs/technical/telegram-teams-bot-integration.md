# Integración de Canal Interactivo (Telegram / Teams Bot)

Este documento detalla la arquitectura técnica, el funcionamiento del canal interactivo de consulta para técnicos a través de bots de mensajería (Telegram y Teams), y las optimizaciones a nivel de base de datos implementadas.

---

## 1. Arquitectura General y Canales de Comunicación

La comunicación entre los clientes de mensajería y el portal Laravel está diseñada de forma híbrida y desacoplada, soportando dos modos de funcionamiento:

### A. Procesamiento Nativo vía Webhooks (Telegram Directo)
El portal Laravel actúa directamente como el procesador del Webhook de Telegram. Cuando se configura el Webhook del bot de Telegram apuntando a la URL del portal, el flujo de ejecución es directo y óptimo:

```
[ Técnico (Telegram) ] ──(HTTPS POST Webhook)──> [ Laravel API: /api/bot/query ]
                                                              │ (Procesamiento SQL)
[ Técnico (Telegram) ] <──(HTTPS POST API Message)─────── [ Laravel Backend ]
```

1. **Recepción Directa**: El webhook de Telegram golpea `/api/bot/query`.
2. **Detección Automática**: El backend identifica que la petición proviene de un webhook de Telegram al evaluar el campo `message.text`.
3. **Procesamiento y Formateo Nativo**: Ejecuta la consulta SQL optimizada, consolida los resultados en un formato Markdown ultra-denso adaptado para móviles, y realiza una llamada POST asíncrona a la API de Telegram (`https://api.telegram.org/bot<token>/sendMessage`).
4. **Respuesta Rápida**: Retorna inmediatamente un HTTP `200 OK` con `{"status":"success"}` a los servidores de Telegram para liberar la cola de entrega.

### B. Integración vía Orquestadores (n8n / Teams)
Para canales externos como Microsoft Teams u automatizaciones complejas, el backend mantiene compatibilidad con llamadas JSON estándar:

```
[ Técnico ] <---> [ Bot (Teams) ] <---> [ Orquestador n8n ] <---> [ Laravel API ]
```

---

## 2. Endpoint de Laravel: `/api/bot/query`

* **Método**: `POST`
* **Ruta**: `/api/bot/query`
* **Controlador**: `App\Http\Controllers\Api\BotQueryController`

### Seguridad y Autenticación
El acceso al endpoint está estrictamente protegido mediante autenticación de tokens. Soporta tres vías de detección (sanitizadas con `trim()` defensivo contra espacios o CRLFs):
1. **Bearer Token**: Cabecera `Authorization: Bearer <BOT_TOKEN>`
2. **Custom Header**: Cabeceras `X-Bot-Token` o `X-Telegram-Bot-Api-Secret-Token` (enviado por Telegram).
3. **Query Parameter**: Parámetro de URL `?token=<BOT_TOKEN>`.

Los tokens autorizados se configuran en el entorno a través de las variables de entorno `BOT_API_TOKEN`, `TELEGRAM_BOT_TOKEN`, y `N8N_WEBHOOK_SECRET`.

---

## 3. Optimizaciones Técnicas Clave en el Backend

### ⚡ 1. Consultas SQL Nativas en Eloquent
Para evitar la degradación de rendimiento provocada por la hidratación y el filtrado de colecciones enteras en memoria RAM de PHP, todos los filtros de negocio se han migrado a la capa de base de datos (Eloquent / MariaDB):
* **Filtro de Sold-To en JSON**: En `/soldto`, la búsqueda evalúa campos planos y elementos embebidos en el campo JSON `additional_sold_tos` de forma nativa en la base de datos usando `where('sold_to', ...)->orWhereJsonContains('additional_sold_tos', ...)`.
* **Filtro de Expiraciones Directo en SQL**: En `/expiraciones`, los productos y contratos se pre-filtran en base de datos evaluando solo aquellos cuya fecha sea menor o igual al umbral crítico (`expiration_date <= NOW() + 30 días`), reduciendo el throughput de red y el consumo de CPU.

### 🧠 2. Similitud Multibyte UTF-8 para Búsqueda Fuzzy (Levenshtein)
La función nativa de PHP `levenshtein()` trabaja a nivel de bytes simples y distorsiona la distancia en nombres que contienen acentos (tildes) o eñes (ñ). Implementamos una normalización de cadenas mediante `iconv()` para asegurar búsquedas precisas:

```php
$normalize = fn($s) => mb_strtolower(trim(
    iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s)
));
```
Esto permite mapear clientes como `"Gurutzpe"` o `"Andaltec"` de forma óptima a pesar de variaciones ortográficas del técnico. El umbral de aceptación está definido por la constante de clase `FUZZY_MATCH_THRESHOLD = 0.75`.

### 📱 3. Consolidación de Salida y Formato Ultra-Compacto para Móviles
El formato del mensaje retornado se ha compactado estratégicamente para priorizar la legibilidad en pantallas pequeñas:
* **Sin Ruido de Contratos**: La salida de `/expiraciones` en Telegram omite bloques de contratos innecesarios y se enfoca exclusivamente en la monitorización de vencimientos inminentes de licencias físicas.
* **Consolidación por Daemon**: Evita duplicaciones repetitivas de productos agrupando a nivel de `Sold-To` + `Daemon`, logrando listados densos ideales para una rápida auditoría técnica desde el teléfono.

---

## 4. Registro y Configuración de Comandos en Telegram (`/setMyCommands`)

Para registrar los comandos directamente en la interfaz de usuario de Telegram y permitir la autocompletación interactiva de los técnicos, se realizó un registro en los servidores centrales usando la API `setMyCommands`:

```bash
curl -s -X POST \
     -H "Content-Type: application/json" \
     -d '{"commands": [
       {"command": "cliente", "description": "Consultar ficha de cliente"},
       {"command": "expiraciones", "description": "Diagnóstico de vencimientos ≤30 días"},
       {"command": "soldto", "description": "Buscar por Sold-To"}
     ]}' \
     "https://api.telegram.org/bot<TELEGRAM_BOT_TOKEN>/setMyCommands"
```

Esto habilita el botón nativo de comandos `/` en el cliente móvil o desktop de los usuarios del bot.
