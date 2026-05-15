# Auditoría de Seguridad — DX License Manager
**Fecha:** 2026-05-09  
**Metodología:** OWASP Top 10 + Laravel Security Best Practices  
**Auditor:** Antigravity (php-security-audit v2.0)  
**Entorno revisado:** Backend Laravel 11 + Nginx + JWT custom

---

## 1. Superficie de Ataque Mapeada

### Puntos de Entrada Identificados

| Endpoint | Método | Auth | Tipo |
|:---------|:-------|:-----|:-----|
| `/login` | GET/POST | ❌ Público | Formulario + throttle |
| `/logout` | POST | ❌ Público | Cookie clear |
| `/` (dashboard) | GET | ✅ JWT | Vista |
| `/clientes` | GET | ✅ JWT | Vista |
| `/clientes/{client}` | GET | ✅ JWT | Vista con ID numérico |
| `/herramientas/nx-suite` | POST | ✅ JWT | Proceso de texto |
| `/herramientas/star-ccm` | POST | ✅ JWT | Proceso de texto |
| `/herramientas/heeds` | POST | ✅ JWT | Proceso de texto |
| `/herramientas/moldex3d` | POST | ✅ JWT | **Subida de archivo .mac** |
| `/herramientas/cod/preview` | POST | ✅ JWT | Generación PDF |
| `/herramientas/cod/store` | POST | ✅ JWT | Generación PDF + guardado |
| `/herramientas/cod/download` | GET | ✅ JWT | Descarga por UUID |
| `/herramientas/cod/{uuid}/upload-signed` | POST | ✅ JWT | **Subida de archivo PDF** |
| `/herramientas/cod/download-signed` | GET | ✅ JWT | Descarga por UUID |
| `/herramientas/cod/{uuid}` | DELETE | ✅ JWT | Borrado de certificado |
| `/clientes/{client}/contactos` | POST/PUT/DELETE | ✅ JWT | CRUD contactos |
| `/inventory/daemon/{daemon}` | DELETE | ✅ JWT | Borrado inventario |
| `/admin/import` | POST | ✅ JWT | **Subida CSV** |
| `/admin/import/logs` | GET/DELETE | ✅ JWT | Logs de importación |
| `/admin/normalization` | GET/POST | ✅ JWT | Normalización datos |

**Trust Boundaries:**
- Externo → Nginx (HTTP) → PHP-FPM → Laravel
- SSL terminado en Cloudflare (no en Nginx)

---

## 2. Hallazgos por OWASP Top 10

---

### [MEDIA] A01 — Falta de RBAC en rutas de escritura (ContactController)

- **Ubicación:** `app/Http/Controllers/ContactController.php` (store, update, destroy)
- **Descripción:** Las rutas `POST/PUT/DELETE /clientes/{client}/contactos` están protegidas por `auth.jwt` pero **no** por `CheckPermission`. Cualquier usuario autenticado (incluso `viewer`) puede crear, modificar o eliminar contactos.
- **Impacto:** Usuario con rol `viewer` puede modificar datos de contacto de cualquier cliente.
- **Reproducción:** Login como `viewer` → `POST /clientes/1/contactos` con datos válidos → contacto creado.
- **Remediación:**
```php
// web.php — añadir middleware de rol a las rutas de contactos
Route::post('/clientes/{client}/contactos', [ContactController::class, 'store'])
    ->middleware('permission:technician')
    ->name('contacts.store');

Route::put('/clientes/{client}/contactos/{contact}', [ContactController::class, 'update'])
    ->middleware('permission:technician')
    ->name('contacts.update');

Route::delete('/clientes/{client}/contactos/{contact}', [ContactController::class, 'destroy'])
    ->middleware('permission:technician')
    ->name('contacts.destroy');
```
- **Referencias:** [OWASP A01](https://owasp.org/Top10/A01_2021-Broken_Access_Control/), [CWE-284](https://cwe.mitre.org/data/definitions/284.html)

---

### [MEDIA] A01 — Falta de RBAC en rutas de inventario y admin

- **Ubicación:** `routes/web.php` L51-64 (inventory, admin)
- **Descripción:** Las rutas de borrado de inventario (`DELETE /inventory/daemon/{daemon}`, `DELETE /inventory/product/{product}`) y **todas las rutas `/admin/*`** solo tienen `auth.jwt`. No tienen `CheckPermission`. Un `viewer` o `technician` podría acceder al panel de importación CSV o borrar registros de inventario.
- **Impacto:** Acceso no autorizado a operaciones destructivas de datos.
- **Reproducción:** Login como `viewer` → `GET /admin/import` → acceso concedido.
- **Remediación:**
```php
// web.php
Route::delete('/inventory/daemon/{daemon}', [...])
    ->middleware('permission:technician');

Route::delete('/inventory/product/{product}', [...])
    ->middleware('permission:technician');

Route::prefix('admin')->middleware('permission:admin')->name('admin.')->group(function () {
    // todas las rutas admin ya aquí
});
```
- **Referencias:** [OWASP A01](https://owasp.org/Top10/A01_2021-Broken_Access_Control/), [CWE-862](https://cwe.mitre.org/data/definitions/862.html)

---

### [MEDIA] A04 — Subida de archivos sin validación de tipo MIME (MoldexController)

- **Ubicación:** `app/Http/Controllers/Tools/MoldexController.php:43`
- **Descripción:** La validación solo dice `'license_file' => 'required|file'`. No valida extensión ni MIME type. Se puede subir cualquier archivo (PHP, exe, shell script).
- **Impacto:** Aunque el archivo se guarda en `Storage::disk('local')` (fuera de public), si un atacante encontrara una forma de servir ese archivo directamente, podría ejecutar código.
- **Reproducción:** `POST /herramientas/moldex3d` con un archivo `.php` renombrado a `.mac`.
- **Remediación:**
```php
$request->validate([
    'license_file' => 'required|file|max:10240|mimetypes:text/plain,application/octet-stream',
]);

// O validación de extensión explícita:
$extension = $request->file('license_file')->getClientOriginalExtension();
if (!in_array(strtolower($extension), ['mac', 'txt'])) {
    abort(422, 'Solo se permiten archivos .mac');
}
```
- **Referencias:** [OWASP A04](https://owasp.org/Top10/A04_2021-Insecure_Design/), [CWE-434](https://cwe.mitre.org/data/definitions/434.html)

---

### [MEDIA] A04 — Subida de CSV sin validación de tipo (ImportController)

- **Ubicación:** `app/Http/Controllers/Admin/ImportController.php:32`
- **Descripción:** `'csv_file' => 'required|file'` — sin validación de extensión ni MIME. Se acepta cualquier archivo como CSV.
- **Impacto:** Inyección de datos maliciosos en el importador. Fórmulas Excel en CSV (`=CMD(...)`). Aunque el riesgo de RCE es bajo en este stack, los datos corruptos pueden causar behavior inesperado.
- **Remediación:**
```php
$request->validate([
    'csv_file' => 'required|file|max:51200|mimes:csv,txt|mimetypes:text/csv,text/plain,application/csv',
]);
```
- **Referencias:** [CWE-434](https://cwe.mitre.org/data/definitions/434.html)

---

### [BAJA] A02 — JWT implementado manualmente (sin librería estándar)

- **Ubicación:** `app/Services/Auth/JwtService.php`
- **Descripción:** El JWT está implementado desde cero con `hash_hmac`. Aunque usa `hash_equals` (timing-safe ✅) y valida expiración (✅), hay aspectos críticos ausentes:
  1. **Sin blacklist/revocación**: Un token robado válido durante 60 min no se puede invalidar (no hay Redis blacklist implementada).
  2. **Sin refresh token**: El token dura 60 min con `generate([], 60)`. Si se roba en esos 60 min, no hay forma de invalidarlo.
  3. **Fallback peligroso**: `config('auth.jwt_secret') ?? config('app.key')` — si `JWT_SECRET` no está en `.env`, usa `APP_KEY` como secreto JWT. Si `APP_KEY` se rota (artisan key:generate), todos los tokens son inválidos de golpe.
- **Impacto:** Token robado usable hasta expiración. Rotación de APP_KEY = logout masivo inesperado.
- **Remediación:**
```php
// config/auth.php — verificar que JWT_SECRET existe siempre
'jwt_secret' => env('JWT_SECRET') ?: throw new RuntimeException('JWT_SECRET not set'),

// Añadir blacklist en Redis (al hacer logout):
Redis::sadd('jwt_blacklist', $token);
Redis::expireat('jwt_blacklist:' . $token, $decoded['exp']);

// En JwtAuth::handle():
if (Redis::sismember('jwt_blacklist', $token)) {
    return redirect('/login');
}
```
- **Referencias:** [CWE-613](https://cwe.mitre.org/data/definitions/613.html), [RFC 7519](https://tools.ietf.org/html/rfc7519)

---

### [BAJA] A01 — IDOR potencial en descarga COD (baja probabilidad)

- **Ubicación:** `app/Http/Controllers/Tools/CodController.php:94`
- **Descripción:** `download()` busca por UUID (`CodCertificate::where('uuid', $uuid)->firstOrFail()`). UUID v4 es prácticamente imposible de adivinar. Sin embargo, **no verifica que el certificado pertenezca al usuario actual**. Cualquier usuario autenticado con un UUID válido puede descargar el PDF de otro usuario.
- **Impacto:** Bajo en la práctica (portal interno, todos los usuarios son empleados), pero viola el principio de mínimo privilegio.
- **Remediación:**
```php
// Añadir verificación de ownership o rol:
$certificate = CodCertificate::where('uuid', $uuid)
    ->where(function ($q) {
        $q->where('created_by', Auth::id())
          ->orWhereHas('creator', fn($q) => $q->where('role_id', Role::ADMIN));
    })
    ->firstOrFail();

// O simplemente verificar que el usuario es admin/technician:
// middleware('permission:technician') en la ruta
```
- **Referencias:** [OWASP A01](https://owasp.org/Top10/A01_2021-Broken_Access_Control/), [CWE-639](https://cwe.mitre.org/data/definitions/639.html)

---

### [BAJA] A05 — Error de excepción expuesto en producción (ImportController)

- **Ubicación:** `app/Http/Controllers/Admin/ImportController.php:51`
- **Descripción:** `'error', 'Error crítico en la importación: ' . $e->getMessage()` — el mensaje de excepción de PHP se expone directamente al usuario.
- **Impacto:** Stack traces o mensajes internos pueden revelar rutas de archivos, configuración de BD o estructura interna.
- **Remediación:**
```php
} catch (\Exception $e) {
    Log::error('CSV Import failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
    return redirect()->back()->with('error', 'Error en la importación. Contacte con el administrador.');
}
```
- **Referencias:** [CWE-209](https://cwe.mitre.org/data/definitions/209.html)

---

### ✅ CONFORME — Puntos que pasan la auditoría

| Área | Estado | Detalle |
|:-----|:-------|:--------|
| Contraseñas hasheadas | ✅ | `bcrypt` via `password => 'hashed'` cast |
| Cookie JWT | ✅ | `HttpOnly=true`, `Secure=true`, `SameSite=Strict` |
| Timing-safe comparison | ✅ | `hash_equals()` en JwtService |
| CSRF en formularios | ✅ | `@csrf` + Laravel middleware activo |
| Consultas SQL | ✅ | 100% Eloquent, sin raw queries concatenadas |
| Archivos en storage privado | ✅ | `Storage::disk('private')` fuera de public/ |
| Rutas ocultas (dotfiles) | ✅ | `location ~ /\.(?!well-known).*  { deny all; }` |
| Mass assignment User | ✅ | `$fillable` explícito, sin `$guarded = []` |
| Input escaping en Blade | ✅ | `{{ }}` en todas las vistas (no `{!! !!}` sin sanitizar) |
| Rate limiting en login | ✅ | `throttle:5,1` |
| Política Solo Log (IA) | ✅ | `file_path = NULL` confirmado en auditorías IA |
| Acceso a dotfiles server | ✅ | Nginx bloquea `.env`, `.git`, etc. |

---

## 3. Checklist de Cabeceras HTTP

| Cabecera | Beta | Prod |
|:---------|:-----|:-----|
| `X-Frame-Options` | ✅ SAMEORIGIN | ✅ SAMEORIGIN |
| `X-Content-Type-Options` | ✅ nosniff | ✅ nosniff |
| `X-XSS-Protection` | ✅ 1; mode=block | ✅ 1; mode=block |
| `Referrer-Policy` | ✅ strict-origin-when-cross-origin | ✅ strict-origin-when-cross-origin |
| `Strict-Transport-Security` | ❌ **AUSENTE** | ✅ max-age=31536000 |
| `Content-Security-Policy` | ❌ **AUSENTE** | ❌ **AUSENTE** |
| `Permissions-Policy` | ❌ **AUSENTE** | ❌ **AUSENTE** |

**Nota HSTS en Beta:** Al ser beta un entorno interno de pruebas accesible por Cloudflare, la ausencia de HSTS es aceptable. En prod está presente ✅.

---

## 4. Plan de Acción Priorizado

| Prioridad | Hallazgo | Severidad | Tiempo est. |
|:----------|:---------|:----------|:------------|
| 🔴 1 | RBAC en rutas admin (cualquier auth puede importar CSV) | Media-Alta | 30 min |
| 🔴 2 | RBAC en ContactController (viewer puede editar contactos) | Media | 20 min |
| 🔴 3 | Validación MIME en MoldexController (subida .mac) | Media | 15 min |
| 🟡 4 | Validación MIME en ImportController (subida .csv) | Media | 10 min |
| 🟡 5 | Ocultar mensaje de excepción en ImportController | Baja | 10 min |
| 🟡 6 | JWT blacklist en Redis al hacer logout | Baja | 2h |
| 🟢 7 | CSP header en nginx (beta + prod) | Baja | 1h |
| 🟢 8 | Permissions-Policy header en nginx | Baja | 15 min |
| 🟢 9 | Verificación ownership en descarga COD | Baja | 30 min |

**Total estimado: ~5 horas**

---

## 5. Resumen Ejecutivo

La aplicación tiene una **base de seguridad sólida**: JWT en cookie HttpOnly con SameSite=Strict, bcrypt, Eloquent en todas las queries, archivos fuera de public/, y protección contra dotfiles en Nginx.

El **riesgo principal** es la falta sistemática de RBAC en rutas de escritura: los controllers de contactos, inventario y admin no aplican `CheckPermission`. Esto significa que cualquier usuario autenticado (incluso `viewer`) tiene acceso a operaciones que deberían requerir `admin` o `technician`.

Los hallazgos de validación de archivos son correcciones rápidas (< 15 min cada una) y deben aplicarse antes de que usuarios externos suban archivos.

No se han encontrado vulnerabilidades críticas (SQL injection, XSS, path traversal, RCE).
