# AGENTS.md — DX License Manager

Protocolo de operación para el agente en Antigravity.
**Este documento tiene prioridad absoluta sobre cualquier instrucción implícita o asumida.**
Contexto de proyecto → `.agent/INDEX.md` · Estado activo → `.agent/memory/ACTIVE_CONTEXT.md`

---

## ⛔ REGLA CERO — LEER ANTES DE CUALQUIER COSA

**⚠️ REGLA CRÍTICA DE ARQUITECTURA DE CARPETAS Y MERGES:**
Existen DOS carpetas físicas independientes. Trabajar en la incorrecta corrompe Producción en tiempo real.
- `Z:\DX-License-Manager-DEV` (/opt/web-projects/DX-License-Manager-DEV) → **ENTORNO DE DESARROLLO/BETA**. Aquí debes trabajar siempre, anclado a la rama `dev`.
- `Z:\DX-License-Manager` (/opt/web-projects/DX-License-Manager) → **PRODUCCIÓN**. Intocable. Anclado a `main`.
**NUNCA** uses tus herramientas de edición o terminal en la carpeta de Producción. Todo el desarrollo se hace en `-DEV`.
**MERGE A MAIN:** A la rama `main` SOLO se pasa cuando Oskar lo ordene explícitamente, y el proceso de merge se hará de forma manual (excepto que él indique lo contrario).

**NUNCA ejecutar después de presentar un plan. NUNCA. Sin excepciones.**

```
1. Recibir tarea
2. Leer INDEX.md → identificar skill · Leer ACTIVE_CONTEXT.md → recuperar estado
3. Presentar plan + checklist
4. ── DETENERSE EN SILENCIO ──
5. Esperar que Oskar escriba: "adelante" / "ok" / "sí" / "procede" / "empieza"
6. Solo entonces: ejecutar Paso 1 únicamente — nada más
```

**Lo que NO es confirmación válida — nunca ejecutar por:**
- Creación de un artefacto o archivo
- Mensaje del sistema o del IDE
- Silencio o ausencia de respuesta
- El propio agente diciendo "Aprobación recibida"
- Cualquier señal que no sea texto explícito de Oskar

**Lo que SÍ es confirmación válida:**
- Oskar escribe: "adelante", "ok", "sí", "procede", "empieza", "dale", "go"

**Después del plan → NO preguntar "¿Empiezo?". Presentar y CALLAR.**
El desarrollador inicia. El agente espera.

⛔ "Aprobación recibida. Empiezo ejecución." → FRASE PROHIBIDA. Nunca escribirla.

Al iniciar sesión, declarar: **"Modo estricto activo. No ejecuto sin confirmación explícita."**

---

## 0. Idioma

**Siempre responder en castellano.** Sin excepciones — aunque el código, los errores o las preguntas estén en inglés.
Excepción: comentarios de código, mensajes de commit y documentación técnica → en inglés (estándar del sector).

---

## 0.1 Commits — Regla Absoluta

**Cada `/log` y cada `/sync` termina con un commit. Sin excepciones.**

**Antes de cualquier commit — verificar logs obligatoriamente:**

```bash
# 1. Revisar logs del contenedor PHP — cero errores antes de commitear
docker compose --project-directory . -f infra/docker-compose.beta.yml logs --tail=50 dx-php-beta

# 2. Si hay errores en logs → NO commitear → resolver primero
# 3. Solo si logs están limpios → proceder al commit
git status
git diff --cached
git add [archivos concretos]
git commit -m "feat/fix/chore(scope): descripción"
```

⛔ **Logs con errores = commit bloqueado.** Resolver el error, verificar logs limpios, entonces commitear.

- **Nunca** `git add .` sin revisar `git status` primero
- **Nunca** `wip` ni `changes` como mensaje de commit
- **Nunca** acumular varias subtareas en un solo commit
- **Nunca** hacer merge a `main` sin autorización explícita del desarrollador — ni via PR, ni via comando, ni via workflow

---

## 0.2 Parar ante Problemas — Regla Absoluta

**Cuando algo falla, el agente PARA. No continúa. No aplica workarounds silenciosos.**

1. Mostrar el error exacto
2. Analizar causa siguiendo `debug-reasoning.md`
3. Proponer solución y esperar confirmación
4. Solo continuar cuando el problema esté resuelto y verificado

**Nunca marcar una tarea como completada si hubo errores.**

---

## 0.3 DESIGN.md — Obligatorio para Cualquier UI

**Antes de crear cualquier vista, componente o elemento visual → leer `DESIGN.md`.**
No improvisar estilos. Referencia visual obligatoria: vistas Blade ya existentes en `backend/resources/views/` — mantener coherencia con lo construido, no inventar patrones nuevos.

---

## 0.4 Descomposición Obligatoria — Antes de Ejecutar, Dividir

**Presentar siempre el plan antes de ejecutar:**

```
📋 Plan para: [tarea]
Paso 1: [un archivo o un comando]
Paso 2: [un archivo o un comando]
¿Empezamos con el Paso 1?
```

---

## 0.4.1 Modo Plan — Tareas No Triviales (3+ pasos)

Para cualquier tarea con 3 o más pasos, antes de presentar el plan:

1. Identificar archivos afectados
2. Detectar dependencias y riesgos
3. Escribir specs concretos (qué hace cada paso, qué NO hace)
4. Estimar si algún paso requiere backup previo

Si algo sale mal durante la ejecución → PARAR y re-planear desde ese punto.
No improvisar sobre un plan roto.

---

## 0.4.2 Reglas de Planificación — Modo Estricto

- No alterar el orden sin permiso
- No saltar pasos
- No reordenar, no combinar, no optimizar sin preguntar
- Si ves algo que se podría hacer mejor → solo sugerir, no ejecutar

Cada paso debe poder completarse, verificarse y commitearse de forma independiente.
Una tarea que no cabe en un commit es demasiado grande — dividirla.

---

## 0.4.3 Checklist Obligatorio — El Agente No Puede Mentir

**Antes de empezar**, el agente genera el checklist completo de la tarea:

```
✅ CHECKLIST — [nombre de la tarea]
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
[ ] Paso 1: [descripción concreta]
[ ] Paso 2: [descripción concreta]
[ ] Paso 3: [descripción concreta]
[ ] Verificación final: [qué demuestra que funciona]
[ ] Commit realizado
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```

**Tras completar cada paso**, el agente muestra el checklist actualizado:

```
✅ CHECKLIST — [nombre de la tarea]
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
[x] Paso 1: [descripción] ← output: [resultado real obtenido]
[ ] Paso 2: [descripción]
[ ] Paso 3: [descripción]
[ ] Verificación final
[ ] Commit realizado
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```

**Reglas del checklist:**
- Un paso solo se marca [x] si hay evidencia real (output de comando, archivo creado, test pasado)
- Nunca marcar [x] por inferencia — solo por resultado verificado
- Si un paso falla → marcar [!] y PARAR
- Antes del commit de cierre → revisar logs obligatoriamente (ver §0.1)
- El checklist final debe estar 100% completo Y logs limpios antes de hacer el commit de cierre

---

## 0.5 Una Cosa a la Vez — Trabajo Secuencial

**Un paso. Un archivo. Un comando. Verificar. Commitear. Siguiente.**

Prohibido: crear múltiples archivos a la vez · encadenar comandos sin revisar output · avanzar sin verificar el paso anterior.

---

## 0.5.1 Detección de Cambio de Tarea — Regla Absoluta

**Antes de ejecutar cualquier petición nueva:**

```
¿Lo que me piden pertenece a la rama en la que estoy?
```

Si NO o NO ESTOY SEGURO → ejecutar `/switch` inmediatamente. No escribir código. Cerrar la rama actual primero.

**Señales de cambio de tarea:** módulo distinto · nombre de rama no describe lo pedido · "ahora vamos a..." / "necesito otra cosa" · lógicamente iría en otra rama.

---

## 0.5.2 Escritura de Archivos — Regla Crítica

Usar según lo disponible en el IDE activo:

| Situación | Ruta a usar |
| :--- | :--- |
| MCP filesystem configurado | `\\192.168.50.10\webs\DX-License-Manager\` |
| Sin MCP — unidad mapeada | `Z:\DX-License-Manager\` o `Y:\DX-License-Manager\` |

**NUNCA** crear archivos fuera del proyecto activo ni mezclar rutas entre proyectos.

---

## 0.6 Acciones Destructivas — Confirmación Obligatoria

Requieren `"sí"` explícito antes de ejecutarse:

`migrate:fresh` · `migrate:rollback` · `git push --force` · `git reset --hard` · `git rebase` · `git merge` · `rm -rf` · `docker compose down -v` · `docker system prune` · `systemctl stop cloudflared` · `systemctl disable cloudflared`

```
⚠️ Acción destructiva detectada
Comando: [comando exacto]
Consecuencia: [qué se perderá]
¿Confirmas? (sí/no)
```

---

## 0.7 Scope — Qué Puede Tocar el Agente

| Zona | Acceso |
| :--- | :----- |
| `backend/`, `management/`, `task.md`, `.agent/` | ✅ Libre |
| `infra/`, `scripts/`, `.gitignore`, `AGENTS.md`, `DESIGN.md` | ⚠️ Confirmar antes |
| `infra/.env.*`, `.agent/secrets/`, `storage/` | 👁️ Solo Lectura |
| `.agent/secrets/`, `infra/.env.beta`, `infra/.env.prod`, `storage/` | ⛔ No tocar |

---

## 0.8 Seguridad de Datos — Regla de Oro

**PROHIBIDO realizar cambios estructurales o ejecutar tests en el servidor sin backup previo.**

```bash
# Obligatorio antes de migrate, db:seed o tests de integración
./scripts/backup-db.sh beta
# Verificar que el backup existe en storage/backups/db/ antes de continuar
```

Tests en servidor: **siempre** forzar SQLite en memoria:
```bash
docker exec -e DB_CONNECTION=sqlite -e DB_DATABASE=:memory: dx-php-beta php artisan test
```

**`migrate:fresh` en Beta → PROHIBIDO.** Solo migraciones incrementales.

---

## 0.9 Reglas Git — Obligatorias

**El agente trabaja EXCLUSIVAMENTE en ramas que mergean a `dev`. Nunca tocar `main`.**

### 🛡️ BLINDAJE DE PRODUCCIÓN (MAIN) — REGLA CRÍTICA
La rama `main` y la base de datos de producción (`mariadb-prod`) están **estrictamente blindadas**. 
Cualquier cambio en `main`, por minúsculo que sea (un espacio, un estilo CSS, o una variable de entorno), afecta de forma inmediata y en tiempo real a los usuarios de Producción.
- **PROHIBIDO** hacer `git push` o `git merge` directo a `main` sin que el desarrollador lo pida explícitamente.
- **PROHIBIDO** ejecutar migraciones o tests en el entorno de producción (`dx-php-prod`) sin haber realizado antes un volcado completo de la base de datos de producción (`./scripts/backup-db.sh prod`).

### Flujo de Trabajo Estandarizado
- **Una rama por funcionalidad.** Cuando la tarea termina, la rama termina.
- **Formato de rama:** `feature/nombre-corto` · `fix/descripcion` · `chore/descripcion`
- **Nunca** login + migrations + otra feature en la misma rama
- **Nunca** reutilizar una rama después del merge
- **Nunca** crear PR hacia `main` — el merge dev → main lo decide Oskar, no el agente

**Al terminar cada fase del ROADMAP:**
1. PR a `dev` — crear PR pero esperar autorización explícita para merge
2. Git Tag descriptivo: `v[N.0]-[nombre-fase]-ok`
3. Commit de cierre: `"Fase X Terminada — Punto de Restauración"`

---

## 0.10 Seguridad — Reglas Fijas

- Descargas de licencias: IDs de BD, nunca rutas físicas → `/download?id=[UUID]`
- Archivos `.lic`: nunca en texto plano a la IA — solo metadatos
- Tras modificar controladores o middleware de auth → cargar `laravel-security-audit` + `php-security-auditor`
- JWT: access token 15 min + refresh token 24h con rotación automática
- RBAC: `admin` escribe · `technician` lee · `viewer` solo visualiza

### 0.10.1 Buenas Prácticas — Derivadas de Auditorías (Fases 1-3)

**Docs de referencia:**
- `backend/docs/260509_auditoria-seguridad.md` — Fase 1 (RBAC, MIME, JWT)
- `backend/docs/260509_auditoria-seguridad-fase2.md` — Fase 2 (fallback auth, HMAC)
- `backend/docs/260601_auditoria-seguridad-fase3.md` — Fase 3 (Bot, Chatbot, throttle)

**Al crear cualquier endpoint nuevo — checklist obligatorio:**

```
[ ] ¿Tiene middleware auth.jwt o token de bot?
[ ] ¿Tiene middleware permission:rol si es de escritura?
[ ] ¿Tiene throttle si puede generar coste (IA, email, Telegram)?
[ ] ¿El catch no expone $e->getMessage() en respuesta JSON/HTML?
[ ] ¿La subida de archivos valida extensión Y mime type?
[ ] ¿Los tokens/secretos se reciben solo por Header, nunca por query param?
[ ] ¿Los webhooks externos verifican HMAC con hash_equals()?
```

**Patrones prohibidos — nunca escribir esto:**

```php
// ❌ Expone stack trace al cliente
return response()->json(['error' => $e->getMessage()], 500);

// ✅ Correcto
Log::error('...', ['trace' => $e->getTraceAsString()]);
return response()->json(['message' => 'Error interno. Reintenta.'], 500);

// ❌ Token en query param (queda en logs de Nginx)
$token = $request->input('token');

// ✅ Correcto — solo headers
$token = $request->header('X-Bot-Token') ?: $request->bearerToken();

// ❌ Subida sin restricción
'license_file' => 'required|file'

// ✅ Correcto — extensión + mime
'license_file' => 'required|file|max:10240|mimetypes:text/plain,application/octet-stream'
// + validación de extensión explícita con in_array()

// ❌ Endpoint con coste sin throttle
Route::post('/chatbot/query', [ChatbotController::class, 'query']);

// ✅ Correcto
Route::post('/chatbot/query', ...)->middleware('throttle:30,1');

// ❌ Webhook externo sin verificación
public function callback(Request $request) { $this->service->handle($request->all()); }

// ✅ Correcto — HMAC con hash_equals
$secret = config('ai.webhook_secret');
if (!hash_equals(hash_hmac('sha256', $request->getContent(), $secret), $request->header('X-Signature'))) {
    abort(401);
}
```

**Periodicidad de auditorías:**
- Ejecutar auditoría de seguridad tras añadir 3+ controllers nuevos o modificar auth/middleware.
- Usar skill `laravel-security-audit` + `php-security-auditor`.
- Guardar resultado en `backend/docs/YYMMDD_auditoria-seguridad-faseN.md`.
- Añadir entrada en CHANGELOG.md con referencia al doc.

## 0.11 Las 5 Leyes del Modo Estricto

1. **Sin plan no hay ejecución.** Presentar plan · esperar confirmación explícita.
2. **Sin evidencia no hay "hecho".** Demostrar funcionamiento con logs o comandos.
3. **Sin log no hay fix.** Analizar logs antes de proponer solución.
4. **Sin rama no hay código.** `/switch` inmediato si la tarea no corresponde.
5. **Un archivo por respuesta.** Sin excepciones.
6. **Sin memoria no hay contexto.** Leer `.agent/last_brain` y `.agent/memory/ACTIVE_CONTEXT.md` antes de cualquier tarea.

| Infracción | Consecuencia |
| :--------- | :----------- |
| Ejecutar sin confirmar plan | Desarrollador dice STOP — borra lo hecho |
| Marcar como hecho sin evidencia | Demostrar funcionamiento |
| Fix sin leer log | Reformular diagnóstico |
| Código en rama incorrecta | `/switch` inmediato |
| Más de un archivo por respuesta | Dev ignora respuesta |

---

## 📓 Lecciones Aprendidas

**El agente actualiza esta sección tras CADA corrección.** No esperar a que el desarrollador lo pida.

Formato obligatorio:
```
- [YYYY-MM-DD] ERROR: [qué salió mal] → REGLA: [cómo evitarlo en el futuro]
```

Al iniciar sesión con `/start`: leer esta sección completa antes de empezar.

- [2026-05-15] ERROR: La base de datos Beta se vació accidentalmente durante la ejecución de tests de integración debido a una mala configuración del entorno de test en el contenedor. → REGLA: Verificar SIEMPRE el aislamiento del entorno de test y realizar backup obligatorio de la DB (`./scripts/backup-db.sh beta`) antes de cualquier ejecución de tests en el servidor.
- [2026-05-22] ERROR: Intento fallido de ejecutar comandos `docker exec` desde el entorno local de Windows. → REGLA: GRABAR A FUEGO: NUNCA, y digo NUNCA, hay Docker en el entorno local (Windows). El servidor web se accede por SSH o se interactúa a través de los directorios mapeados en red (`Z:`). Los comandos de Docker documentados son exclusivamente para correr en el host Proxmox, no en local.
- [2026-05-28] ERROR: Base de datos Beta borrada accidentalmente por olvidar las variables `DB_CONNECTION=sqlite DB_DATABASE=:memory:` en SSH al hacer tests, y posterior error TLS/SSL al restaurar backup local. → REGLA: Usar siempre `DB_CONNECTION=sqlite` en tests. Al restaurar MariaDB en un contenedor interno mediante comandos de backup/script, añadir **obligatoriamente** el flag `--skip-ssl` al comando `mariadb` para saltarse el requisito TLS que los contenedores locales de Docker no soportan.
- [2026-06-01] ERROR: Nginx no actualizaba la cabecera CSP tras hacer git pull y nginx -s reload. → REGLA: Al modificar archivos mapeados uno a uno por volumen en Docker (como beta.conf o prod.conf), git pull cambia su inode en el host y rompe el bind mount de Docker. NUNCA usar `nginx -s reload` si el archivo ha sido modificado externamente. SIEMPRE reiniciar el contenedor completo con `docker compose restart nginx-beta` (o prod).
- [2026-06-01] ERROR: Pérdida de conexión con el Daemon de Docker (`/var/run/docker.sock`) desde el dashboard del sistema tras ejecutar `docker compose up -d` para recrear el contenedor de PHP. → REGLA: Al recrear contenedores en un entorno LXC que acceden al socket de Docker, los permisos del socket en el host pueden restablecerse. Si el usuario del contenedor (`www-data`) pierde acceso, restaurar ejecutando `chmod 666 /var/run/docker.sock` en el host.
- [2026-06-02] ERROR: Riesgo crítico de corrupción en Producción al editar código en caliente en el entorno Beta, ya que ambos entornos compartían el mismo volumen de código (`./backend:/var/www/html`). → REGLA: **Arquitectura de Carpetas Separadas Obligatoria**. El entorno de Desarrollo/Beta DEBE ejecutarse en una carpeta física aislada (`/opt/web-projects/DX-License-Manager-DEV`) anclada a la rama `dev`, mientras que Producción (`/opt/web-projects/DX-License-Manager`) permanece aislada en `main`. NUNCA usar la misma carpeta física para contenedores de distintos entornos.