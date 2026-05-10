# Auditoría de Seguridad — Fase 2
**Fecha:** 2026-05-09  
**Metodología:** OWASP Top 10 + Laravel Security Best Practices  
**Auditor:** Antigravity (php-security-audit v2.0)  
**Ámbito:** Controllers de herramientas, Services IA, Vistas Blade, Dependencias

---

## 1. Hallazgos

---

### [MEDIA] A01 — Fallback `auth()->id() ?? 1` en tres controllers

- **Ubicación:**
  - `NXSuiteController.php:59`
  - `StarCcmController.php:57`
  - `HeedsController.php:57`
- **Descripción:** El comentario dice "Fallback a ID 1 si no hay auth (para tests)". Esto significa que si `auth()->id()` devuelve `null` (lo cual **no debería ocurrir** ya que la ruta está protegida por `auth.jwt`), la auditoría se registra con `user_id = 1` (admin). Es un residuo de código de desarrollo que no debería existir en producción.
- **Impacto:** Si por cualquier bug el middleware `auth.jwt` falla y no rechaza la petición, una auditoría de licencia podría registrarse como ejecutada por el admin, falseando el log de auditoría y violando el principio de trazabilidad.
- **Reproducción:** Teórico — requeriría un fallo previo en el middleware JWT.
- **Remediación:** Eliminar el fallback. Si no hay usuario autenticado, el middleware ya habrá rechazado la petición antes de llegar aquí.
```php
// Antes (los 3 controllers):
auth()->id() ?? 1, // Fallback a ID 1 si no hay auth (para tests)

// Después:
auth()->id(),
```
- **Referencias:** [CWE-285](https://cwe.mitre.org/data/definitions/285.html) — Improper Authorization

---

### [MEDIA] A04 — Subida sin MIME en NXSuite, StarCCM y HEEDS

- **Ubicación:**
  - `NXSuiteController.php:41` — `'license_file' => 'required|file'`
  - `StarCcmController.php:43` — `'license_file' => 'required|file'`
  - `HeedsController.php:43` — `'license_file' => 'required|file'`
- **Descripción:** Mismo problema que Moldex ya corregido en Fase 1 de la auditoría. Los tres controllers de licencias Siemens aceptan cualquier tipo de archivo sin restricción de extensión ni MIME.
- **Impacto:** Subida de archivos arbitrarios (PHP, ejecutables, scripts). Aunque se guardan fuera de `public/`, el riesgo persiste si el almacenamiento se expone accidentalmente.
- **Remediación:**
```php
// NXSuiteController y StarCcmController (archivos .lic):
$request->validate([
    'license_file' => 'required|file|max:10240|mimetypes:text/plain,application/octet-stream',
]);
$extension = strtolower($request->file('license_file')->getClientOriginalExtension());
if (!in_array($extension, ['lic', 'txt'])) {
    return back()->withErrors(['license_file' => 'Solo se permiten archivos .lic o .txt.']);
}

// HeedsController (también .lic):
// Misma validación que arriba
```
- **Referencias:** [CWE-434](https://cwe.mitre.org/data/definitions/434.html), [OWASP A04](https://owasp.org/Top10/A04_2021-Insecure_Design/)

---

### [BAJA] A02 — `laravel/sanctum` instalado pero no usado

- **Ubicación:** `composer.json:12` — `"laravel/sanctum": "^4.0"`
- **Descripción:** Sanctum está como dependencia de producción pero el proyecto usa JWT custom. Sanctum no está configurado ni en uso. Una dependencia innecesaria aumenta la superficie de ataque (si Sanctum tiene una CVE futura, el proyecto queda expuesto aunque no lo use).
- **Impacto:** Bajo. Solo superficie de ataque latente.
- **Remediación:** Eliminar si no hay planes de usarlo.
```bash
docker exec dx-php-beta composer remove laravel/sanctum
```
- **Referencias:** [CWE-1104](https://cwe.mitre.org/data/definitions/1104.html) — Use of Unmaintained Third Party Components

---

### [BAJA] A02 — Callback de n8n sin autenticación verificada

- **Ubicación:** `AuditService.php:52` — `handleCallback(array $data)`
- **Descripción:** El método `handleCallback` recibe datos del webhook de n8n. No se verifica ningún secreto compartido (`HMAC`, token de cabecera, etc.) para confirmar que la petición realmente viene de n8n y no de un tercero que conoce la URL del callback.
- **Impacto:** Un atacante que descubra la URL del callback podría inyectar resultados de auditoría falsos con cualquier `uuid`, `sold_to` o `customer_name` inventado, manipulando el inventario de licencias.
- **Reproducción:** `POST /callback-url` con `{"uuid": "uuid-existente", "sold_to": "FAKE", "customer_name": "CLIENTE FALSO"}`.
- **Remediación:**
```php
// En el controller que recibe el callback:
public function callback(Request $request)
{
    $secret = config('ai.n8n_webhook_secret');
    $signature = $request->header('X-N8N-Signature');
    
    if (!$signature || !hash_equals(
        hash_hmac('sha256', $request->getContent(), $secret),
        $signature
    )) {
        abort(401, 'Invalid webhook signature');
    }
    
    // Proceder con handleCallback...
}
```
- **Referencias:** [CWE-345](https://cwe.mitre.org/data/definitions/345.html) — Insufficient Verification of Data Authenticity

---

## 2. Puntos que pasan la auditoría (Fase 2)

| Área | Estado | Detalle |
|:-----|:-------|:--------|
| `{!! !!}` en Blade | ✅ | **Ninguno encontrado.** 100% escapado con `{{ }}` |
| `innerHTML` en Alpine.js | ✅ | **Ninguno encontrado.** Sin XSS en vistas |
| `x-html` Alpine (XSS) | ✅ | **Ninguno encontrado.** |
| Queries ClientController | ✅ | Eloquent con `like` parametrizado, sin raw concat |
| InventoryController | ✅ | Route model binding + delete simple |
| Política Solo Log (AuditService) | ✅ | No hay `file_path` en `AiAuditResult::create()` |
| Timeout en llamadas IA | ✅ | `Http::timeout(30)` en AuditService |
| Dependencias (composer.json) | ✅ | Laravel 11.31, dompdf 3.1 — sin CVEs conocidos activos |
| Secrets en código | ✅ | Todo via `config('ai.n8n_webhook_url')` — sin hardcode |

---

## 3. Plan de Acción Fase 2

| Prioridad | Hallazgo | Severidad | Tiempo est. |
|:----------|:---------|:----------|:------------|
| 🔴 1 | MIME validation en NXSuite + StarCCM + HEEDS | Media | 20 min |
| 🔴 2 | Eliminar `auth()->id() ?? 1` en 3 controllers | Media | 10 min |
| 🟡 3 | Webhook n8n sin verificación de firma HMAC | Baja | 1h |
| 🟢 4 | Eliminar `laravel/sanctum` si no se va a usar | Baja | 5 min |

**Total estimado: ~1.5 horas**

---

## 4. Resumen Acumulado (Fase 1 + Fase 2)

| Estado | Hallazgos |
|:-------|:----------|
| ✅ Corregido (Fase 1) | RBAC admin/contactos/inventario, MIME Moldex, MIME CSV, error disclosure |
| 🔴 Pendiente crítico | MIME en NXSuite/StarCCM/HEEDS, fallback auth ID 1 |
| 🟡 Pendiente medio | Webhook n8n sin firma, JWT sin blacklist, CSP header |
| 🟢 Pendiente bajo | Sanctum innecesario, Permissions-Policy header |

**Conclusión:** La aplicación no tiene vulnerabilidades críticas ni inyecciones SQL/XSS. Los hallazgos de Fase 2 son un patrón repetido del mismo problema de validación de archivos (ya corregido en Moldex) que hay que replicar en los otros 3 controllers Siemens.
