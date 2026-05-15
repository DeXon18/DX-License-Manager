---
trigger: always_on
---

# 🛡️ Reglas de Auditoría de Seguridad — DX Management Portal

## 1. Gestión de Ramas y Despliegue

**NUNCA** hacer merge ni push directamente a `main` o `dev` sin que el desarrollador lo pida explícitamente.
Todo el desarrollo ocurre en ramas `feature/`, `fix/` o `chore/` hasta aprobación final.

Antes de cualquier cambio, verificar rama activa:
```bash
git branch --show-current
```

## 2. Blindaje de Descargas — ID-Abstraction

Al implementar cualquier funcionalidad de recuperación de archivos de licencia:

1. **Prohibido pasar rutas en URL.** Nunca `?path=storage/licenses/...`
2. **Solo IDs de base de datos.** Usar `/licenses/download?id=[UUID]` validando existencia en BD antes de servir.
3. Los archivos `.lic` se sirven desde `storage/licenses/{vendor}/{cliente}/` nunca desde `public/`.

## 3. Privacidad en Auditoría IA

Al trabajar en `AuditService.php`, `ProcessAuditJob.php` o cualquier proveedor IA:

1. Las licencias auditadas por IA **NUNCA** se guardan físicamente. `file_path` en `ai_audit_results` debe ser `NULL`.
2. Todo proceso queda registrado en `audit_log` vinculando `user_id`.
3. No enviar el contenido completo del archivo `.lic` al proveedor IA — solo metadatos extraídos.

## 4. Prevención de Path Traversal

Al modificar rutas de archivos o parámetros que concatenen input externo:

1. Sanitizar siempre con `basename()` o validar contra lista blanca.
2. Verificar que no sea posible inyectar `../` en ningún parámetro.
3. En Laravel: usar `Storage::disk()->exists()` antes de cualquier operación de archivo.

## 5. Autenticación y Sesiones JWT

Cada vez que se toque `JwtService.php`, `JwtAuth.php` o la configuración de auth:

1. Access token: máximo 15 minutos.
2. Refresh token: 24 horas con **rotación obligatoria** en cada uso.
3. Blacklist de tokens revocados en Redis.
4. Cookies con `HttpOnly`, `Secure` (en prod) y `SameSite=Strict`.

## 6. RBAC — Control de Acceso por Rol

En cada controlador o ruta nueva, verificar que el middleware `CheckPermission` esté aplicado:

| Rol | Puede |
|:---|:---|
| `admin` | Leer, escribir, eliminar, gestionar usuarios |
| `technician` | Leer, subir archivos, auditar |
| `viewer` | Solo visualizar |

Nunca asumir que porque el usuario está autenticado tiene permisos — verificar el rol explícitamente.

## 7. Variables de Entorno y Secretos

1. **Nunca hardcodear** API keys, tokens, URLs de webhook ni contraseñas.
2. Usar siempre `config('ai.gemini_key')` o `env('GEMINI_API_KEY')` — nunca el valor directo.
3. Los archivos `infra/.env.prod` e `infra/.env.beta` están en `.gitignore` — verificar que sigan ahí.
4. Antes de cualquier commit: `git diff --cached` para confirmar que no hay secrets expuestos.

## 8. Seguridad en Integraciones Externas (IA / n8n)

1. Toda llamada a APIs externas valida certificados SSL. No deshabilitar `CURLOPT_SSL_VERIFYPEER` en prod.
2. Timeout máximo por proveedor IA: 30 segundos.
3. El fallback chain (Gemini → Deepseek → OpenRouter) debe registrar en log qué proveedor se usó.
4. URLs de webhook de n8n solo en `.env`, nunca en código.

## 9. Auditoría de Cambios Estructurales

Cuando se modifiquen rutas, autoload o estructura de carpetas en `backend/`:

1. Ejecutar `composer dump-autoload` y verificar que no hay errores.
2. Comprobar que ningún archivo sensible (`.env`, logs, configs) queda accesible bajo `public/`.
3. Revisar las rutas en `routes/web.php` y `routes/api.php` — ninguna ruta debe quedar sin middleware de auth salvo `/login`.

## 10. UI/UX — Consistencia Visual

Para nuevas vistas Blade:

1. Usar exclusivamente las clases de Tailwind definidas en el proyecto — no CSS inline.
2. Alpine.js para interactividad ligera — no añadir librerías JS sin aprobación explícita.
3. Mantener la filosofía `impeccable`: minimalismo, precisión, sin decoración innecesaria.
