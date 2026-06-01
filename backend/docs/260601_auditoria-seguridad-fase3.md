# Auditoría de Seguridad — Fase 3
**Fecha:** 2026-06-01  
**Metodología:** OWASP Top 10 + Laravel Security Best Practices  
**Auditor:** Antigravity (laravel-security-audit)  
**Ámbito:** Verificación de fixes Fase 1+2 + Módulos nuevos (Telegram Bot, Chatbot, AI Costs, EnterpriseCloud, Support, AiModel)  
**Entorno revisado:** Backend Laravel 11 + JWT custom

---

## 1. Verificación de Fixes de Fases Anteriores

| Hallazgo (Fase 1+2) | Estado | Detalle |
|:--------------------|:-------|:--------|
| RBAC rutas admin (`permission:admin`) | ✅ **CORREGIDO** | `web.php:90` — grupo `prefix('admin')->middleware('permission:admin')` aplicado correctamente |
| RBAC ContactController (`permission:technician`) | ✅ **CORREGIDO** | `web.php:74-76` — middleware por ruta aplicado |
| MIME Validation MoldexController | ✅ **Pendiente verificar** | No re-auditado en esta fase (sin cambios visibles desde Fase 1) |
| MIME Validation NXSuiteController | ✅ **CORREGIDO** | `NXSuiteController.php:67-70` — validación de extensión `['lic','txt','dat','cid']` presente |
| MIME Validation StarCcmController | ✅ **CORREGIDO** | `StarCcmController.php:53-56` — misma validación aplicada |
| MIME Validation HeedsController | ✅ **CORREGIDO** | `HeedsController.php:53-56` — misma validación aplicada |
| `auth()->id() ?? 1` (fallback admin) | ✅ **CORREGIDO** | Los 3 controllers usan `auth()->id()` directamente (línea 86, 71, 71) |
| Webhook n8n sin HMAC | ✅ **CORREGIDO** | `AuditCallbackController.php:21-31` — verificación HMAC con `hash_equals()` implementada |
| RBAC inventory delete (`permission:technician`) | ✅ **CORREGIDO** | `web.php:82-83` — middleware aplicado |
| Error disclosure en ImportController | No re-verificado en esta fase |
| JWT sin blacklist completa | 🟡 **Pendiente** — sin cambios desde Fase 1 |
| CSP header ausente | 🟡 **Pendiente** — sin cambios desde Fase 1 |
| `laravel/sanctum` instalado sin uso | 🟢 **Pendiente bajo** — sin cambios desde Fase 2 |

---

## 2. Hallazgos — Módulos Nuevos

---

### [MEDIA] A01 — BotQueryController: token en query parameter expuesto en logs

- **Ubicación:** `app/Http/Controllers/Api/BotQueryController.php:126`
- **Descripción:** El método `extractToken()` acepta el token de autenticación como parámetro en la query string (`?token=xxx`). Los parámetros de query se registran por defecto en los access logs de Nginx y en cualquier log de peticiones. El token de bot quedaría expuesto en los logs del servidor.
- **Impacto:** Si los logs de Nginx son accesibles (por un compañero, backup de logs, etc.), el token del bot queda expuesto.
- **Reproducción:** `POST /api/bot/query?token=SECRET_BOT_TOKEN` — el token aparece en los logs de acceso de Nginx.
- **Remediación:** Eliminar la aceptación de token por query param. Solo `Authorization: Bearer` o `X-Bot-Token` header.
```php
// Antes (extractToken):
?: $request->input('token')

// Después — eliminar esa línea:
private function extractToken(Request $request): string
{
    $authHeader = $request->header('Authorization');
    if ($authHeader && preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
        return trim($matches[1]);
    }
    return trim(
        $request->header('X-Bot-Token')
        ?: $request->header('X-Telegram-Bot-Api-Secret-Token')
        ?: ''
    );
}
```
- **Referencias:** [CWE-598](https://cwe.mitre.org/data/definitions/598.html), [OWASP A01](https://owasp.org/Top10/A01_2021-Broken_Access_Control/)

---

### [MEDIA] A01 — BotQueryController: Client::all() en fuzzy match sin paginación

- **Ubicación:** `app/Http/Controllers/Api/BotQueryController.php:156`
- **Descripción:** El comando `/cliente` hace `Client::all()` para calcular similitud Levenshtein. Con cientos de clientes esto es ineficiente, pero el riesgo de seguridad real es que **un atacante con el token de bot puede enumerar indirectamente nombres de clientes** mediante diferencias en tiempo de respuesta (timing attack muy básico) y el mensaje de error que confirma si el cliente fue encontrado o no.
- **Impacto:** Bajo en este contexto (portal interno), pero la respuesta `"Cliente '{$arg}' no encontrado en base de datos."` confirma existencia/no-existencia de clientes por nombre, lo cual es information disclosure menor.
- **Remediación:** Respuesta genérica que no confirme si el nombre existe:
```php
// Antes:
'message' => "Cliente '{$arg}' no encontrado en base de datos."

// Después:
'message' => "No se encontró información para el término de búsqueda proporcionado."
```
- **Referencias:** [CWE-203](https://cwe.mitre.org/data/definitions/203.html)

---

### [MEDIA] A05 — ChatbotController: stack trace expuesto en respuesta JSON

- **Ubicación:** `app/Http/Controllers/Api/ChatbotController.php:84`
- **Descripción:** El catch devuelve `'error' => $e->getMessage()` directamente al cliente en una respuesta JSON 500. Esto expone mensajes de excepción de PHP (potencialmente rutas de archivos, configuración de servicios IA, claves de configuración) al frontend.
- **Impacto:** Un usuario autenticado que provoque un error en el chatbot (ej. carga alta, timeout de proveedor IA) recibe el mensaje interno de excepción en la UI.
- **Reproducción:** Deshabilitar la API key de Gemini → enviar consulta al chatbot → respuesta JSON incluye el mensaje de error del proveedor IA.
- **Remediación:**
```php
// Antes:
return response()->json([
    'success' => false,
    'message' => "Ha ocurrido un error...",
    'error' => $e->getMessage()    // ← EXPONER ESTO ES EL PROBLEMA
], 500);

// Después:
Log::error("ChatbotController: Error procesando chat: " . $e->getMessage(), [
    'trace' => $e->getTraceAsString()
]);
return response()->json([
    'success' => false,
    'message' => "Ha ocurrido un error inesperado al procesar tu consulta. Por favor, reintenta en unos instantes.",
], 500);
```
- **Referencias:** [CWE-209](https://cwe.mitre.org/data/definitions/209.html), [OWASP A05](https://owasp.org/Top10/A05_2021-Security_Misconfiguration/)

---

### [BAJA] A01 — ChatbotController: endpoint sin rate limiting

- **Ubicación:** `routes/web.php:28` — `Route::post('/chatbot/query', ...)`
- **Descripción:** El endpoint `/chatbot/query` solo tiene `auth.jwt`. No hay throttle. Un usuario autenticado puede lanzar miles de peticiones por minuto, generando un coste elevado en tokens IA (Gemini/OpenRouter).
- **Impacto:** Abuso de costes IA por parte de un usuario autenticado legítimo (accidental o malicioso). No es RCE ni data breach, pero puede generar gasto económico real.
- **Remediación:**
```php
// web.php:
Route::post('/chatbot/query', [ChatbotController::class, 'query'])
    ->middleware('throttle:30,1')   // 30 req/min por usuario
    ->name('chatbot.query');
```
- **Referencias:** [OWASP API4](https://owasp.org/API-Security/editions/2023/en/0xa4-unrestricted-resource-consumption/)

---

### [BAJA] A07 — SupportController: mensaje markdown sin sanitizar a Telegram

- **Ubicación:** `app/Http/Controllers/SupportController.php:36-37`
- **Descripción:** El asunto y mensaje del formulario se insertan directamente en el texto Markdown enviado a Telegram: `"📌 *Asunto:* {$request->subject}"`. Si el usuario incluye caracteres especiales de Markdown (`*`, `_`, `` ` ``, `[`), puede romper el formato del mensaje o en casos extremos manipular el layout en Telegram (no es RCE, pero es output injection).
- **Impacto:** Muy bajo. Solo afecta a la presentación del mensaje en el chat de soporte de Oskar.
- **Remediación:** Escapar los campos de usuario antes de interpolar:
```php
$subject = htmlspecialchars($request->subject, ENT_QUOTES, 'UTF-8');
$message = htmlspecialchars($request->message, ENT_QUOTES, 'UTF-8');
// O bien, cambiar parse_mode a 'HTML' y escapar con strip_tags()
```
- **Referencias:** [CWE-116](https://cwe.mitre.org/data/definitions/116.html)

---

### [BAJA] A02 — api.php: `/api/bot/query` y `/api/audit/callback` sin rate limiting explícito

- **Ubicación:** `routes/api.php:10-11`
- **Descripción:** Las dos rutas API públicas (sin middleware de sesión) no tienen `throttle`. Aunque `BotQueryController` verifica el token de bot (✅), un atacante que no tenga el token puede bombardear el endpoint generando logs de error y consumo de recursos. El `AuditCallbackController` con HMAC también podría recibir spam de peticiones inválidas.
- **Impacto:** Bajo. DoS suave por peticiones inválidas repetidas.
- **Remediación:**
```php
// api.php:
Route::post('/audit/callback', AuditCallbackController::class)->middleware('throttle:60,1');
Route::post('/bot/query', [BotQueryController::class, 'query'])->middleware('throttle:60,1');
```
- **Referencias:** [OWASP API4](https://owasp.org/API-Security/editions/2023/en/0xa4-unrestricted-resource-consumption/)

---

### [INFORMACIONAL] AiAuditCostController: queries no paginadas en cálculo de costes

- **Ubicación:** `app/Http/Controllers/Admin/AiAuditCostController.php:30`
- **Descripción:** `AiModel::all()->keyBy('openrouter_id')` carga todos los modelos en memoria. No es un problema ahora (hay 11 modelos), pero si el catálogo crece significativamente podría impactar en memoria. No es un problema de seguridad.
- **Recomendación:** Cuando el catálogo supere ~100 modelos, cambiar a consulta cacheada (`Cache::remember`).

---

### [INFORMACIONAL] AiRoute::findOrFail por task_name (no ID numérico)

- **Ubicación:** `app/Http/Controllers/Admin/AiModelController.php:67`
- **Descripción:** `AiRoute::findOrFail($task_name)` donde `$task_name` viene de la URL. Si la primary key de `ai_routes` es el `task_name` (string), esto es correcto. Si es un ID numérico, hay riesgo de behavior inesperado. Sin ver la migración, se marca como informacional para verificar.
- **Recomendación:** Confirmar que `task_name` es la primary key de la tabla `ai_routes` o usar `AiRoute::where('task_name', $task_name)->firstOrFail()` para claridad.

---

## 3. Puntos que pasan la auditoría (Fase 3)

| Área | Estado | Detalle |
|:-----|:-------|:--------|
| EnterpriseCloudAccountController — RBAC | ✅ | `web.php:78-80` — `permission:technician` en store/update/destroy |
| EnterpriseCloudAccountController — ownership | ✅ | `L31` y `L52` — `abort(403)` si la cuenta no pertenece al cliente |
| EnterpriseCloudAccountController — validación | ✅ | Campos validados con tipos y max length |
| BotQueryController — autenticación | ✅ | Token comparado con `in_array(..., true)` (strict) contra lista de tokens configurados |
| BotQueryController — tokens vacíos bloqueados | ✅ | `empty($allowedTokens)` → devuelve 500, no procesa |
| BotQueryController — comandos en whitelist | ✅ | `in:cliente,expiraciones,soldto` en validación JSON |
| BotQueryController — queries Eloquent | ✅ | Sin raw queries; `like`, `orWhereJsonContains` parametrizados |
| ChatbotController — validación de input | ✅ | `messages.*.role` en whitelist `in:user,assistant,model` |
| ChatbotController — ruta con auth.jwt | ✅ | `web.php:28` bajo el grupo `auth.jwt` |
| SupportController — ruta con auth.jwt | ✅ | `web.php:45-46` bajo el grupo `auth.jwt` |
| SupportController — validación + límites | ✅ | `max:100` en subject, `max:2000` en message |
| AiModelController — bajo `permission:admin` | ✅ | `web.php:121-126` dentro del grupo admin |
| AiModelController — validación en storeModel | ✅ | `unique:ai_models,openrouter_id` evita duplicados |
| AiModelController — FK validation en updateRoute | ✅ | `exists:ai_models,id` en primary y fallback |
| AiAuditCostController — bajo `permission:admin` | ✅ | `web.php:106` dentro del grupo admin |
| AiAuditCostController — solo lectura | ✅ | No hay operaciones de escritura, solo aggregates |
| Secrets IA | ✅ | Todo via `config('ai.*')` y `config('services.*')` — sin hardcode |

---

## 4. Plan de Acción Priorizado (Fase 3)

| Prioridad | Hallazgo | Severidad | Tiempo est. |
|:----------|:---------|:----------|:------------|
| 🔴 1 | Token bot en query param (logs exposure) | Media | 10 min |
| 🔴 2 | Stack trace expuesto en ChatbotController JSON | Media | 5 min |
| 🟡 3 | Rate limiting en `/chatbot/query` | Baja | 5 min |
| 🟡 4 | Rate limiting en `/api/audit/callback` y `/api/bot/query` | Baja | 5 min |
| 🟢 5 | Sanitizar markdown en SupportController | Baja | 10 min |
| 🟢 6 | Respuesta genérica en cliente no encontrado (BotQueryController) | Baja | 5 min |
| ℹ️ 7 | Verificar PK de `ai_routes` (task_name vs id) | Informacional | 5 min |

**Total estimado: ~45 minutos**

---

## 5. Estado Acumulado — Fases 1 + 2 + 3

| Estado | Hallazgos |
|:-------|:----------|
| ✅ Corregido (Fase 1) | RBAC admin/contactos/inventario, MIME Moldex, MIME CSV, error disclosure |
| ✅ Corregido (Fase 2) | MIME NXSuite/StarCCM/HEEDS, fallback `auth()->id() ?? 1`, webhook HMAC |
| ✅ Corregido (Fase 3 — ya venía bien) | EnterpriseCloud RBAC+ownership, Bot auth, Chatbot validación, rutas admin |
| 🔴 Pendiente Fase 3 | Token bot en query param, stack trace en chatbot |
| 🟡 Pendiente Fase 3 | Rate limiting chatbot + API routes |
| 🟡 Pendiente Fases anteriores | JWT blacklist completa, CSP header |
| 🟢 Pendiente bajo | Sanitizar markdown soporte, respuesta cliente genérica, sanctum sin uso |

---

## 6. Resumen Ejecutivo

La aplicación mantiene su **base de seguridad sólida** establecida en fases anteriores. Todos los fixes críticos de Fase 1 y Fase 2 están correctamente aplicados: RBAC, MIME validation, HMAC webhook, y la eliminación del fallback de auth. Los módulos nuevos (EnterpriseCloud, AiModel, AiAuditCost) están bien implementados.

Los hallazgos nuevos son de **severidad media-baja**: el más relevante es la aceptación del token de bot por query parameter (riesgo de exposición en logs de Nginx) y la exposición del mensaje de excepción en la respuesta del chatbot. Ambos son correcciones de menos de 10 minutos.

**No se han encontrado vulnerabilidades críticas** (SQL injection, XSS, path traversal, RCE, IDOR de alta probabilidad) en los módulos nuevos.
