# AGENTS.md — DX License Manager

Protocolo de operación para el agente en Antigravity.
Este documento tiene prioridad sobre cualquier instrucción implícita o asumida.

---

## 0. Idioma

**Siempre responder en castellano.** Sin excepciones — aunque el código, los archivos, los errores o las preguntas estén en inglés, la respuesta siempre es en castellano. Los comentarios de código, mensajes de commit y documentación técnica sí van en inglés (estándar del sector), pero todo lo que el agente comunica al desarrollador va en castellano.

---

## 0.1 Commits — Regla Absoluta

**Cada `/log` y cada `/sync` termina con un commit. Sin excepciones.**

Un trabajo sin commit no existe. Antes de cada commit, verificar qué se commitea:

```bash
git status          # qué archivos están modificados
git diff --cached   # qué cambios exactos van al commit
```

Solo si el diff es correcto, proceder:

```bash
# Tras /log
git add [archivos tocados] management/CHANGELOG.md
git commit -m "feat/fix/chore(...): descripción"

# Tras /sync
git add management/CHANGELOG.md management/ROADMAP.md management/BACKLOG.md
git commit -m "docs(sync): [bloque] completado"
```

**Nunca usar `git add .` sin revisar `git status` primero.**

---

## 0.2 Parar ante Problemas — Regla Absoluta

**Cuando algo falla, el agente PARA. No continúa. No aplica workarounds silenciosos.**

1. Mostrar el error exacto al desarrollador
2. Analizar causa siguiendo `debug-reasoning.md`
3. Proponer solución y esperar confirmación
4. Solo continuar cuando el problema esté resuelto y verificado

**Nunca marcar una tarea como completada si hubo errores.**

---

## 0.3 DESIGN.md — Obligatorio para cualquier UI

**Antes de crear cualquier vista, componente o elemento visual → leer `DESIGN.md`.**

Sin excepción. El sistema de diseño del proyecto está definido ahí: tipografía, colores, espaciado, componentes. No improvisar estilos.

---

## 0.4 Descomposición Obligatoria — Antes de Ejecutar, Dividir

**Antes de ejecutar cualquier tarea, presentar el plan dividido en pasos pequeños y esperar confirmación.**

```
📋 Plan para: [tarea]
Paso 1: [un archivo o un comando]
Paso 2: [un archivo o un comando]
¿Empezamos con el Paso 1?
```

Cada paso debe poder completarse, verificarse y commitearse de forma independiente.
Una tarea que no cabe en un commit es una tarea demasiado grande — dividirla.

---

## 0.5 Una Cosa a la Vez — Trabajo Secuencial

**Un paso. Un archivo. Un comando. Verificar. Commitear. Siguiente.**

Prohibido crear múltiples archivos a la vez, encadenar comandos sin revisar output, o pasar al siguiente paso sin verificar el anterior.

---

## 0.5.1 Detección de Cambio de Tarea — Regla Absoluta

**Antes de ejecutar cualquier petición nueva, el agente se pregunta:**

```
¿Lo que me están pidiendo pertenece a la rama en la que estoy?
```

Si la respuesta es NO o NO ESTOY SEGURO → ejecutar `/switch` inmediatamente. No escribir código. No ejecutar comandos. Cerrar la rama actual primero.

**Señales de cambio de tarea:**

- La petición involucra un módulo distinto al de la rama activa
- El nombre de la rama no describe lo que se está pidiendo
- El desarrollador dice "ahora vamos a...", "cambia de tema", "necesito otra cosa"
- La nueva tarea lógicamente iría en una rama con nombre diferente

Ver procedimiento completo en `.agent/workflows/switch-task.md`.

---

## 0.6 Acciones Destructivas — Confirmación Obligatoria

Estos comandos requieren "sí" explícito del desarrollador antes de ejecutarse:

`migrate:fresh` · `migrate:rollback` · `git push --force` · `git reset --hard` · `git rebase` · `git merge` · `rm -rf` · `docker compose down -v` · `docker system prune`

Formato obligatorio antes de ejecutar cualquiera:

```
⚠️ Acción destructiva detectada
Comando: [comando exacto]
Consecuencia: [qué se perderá]
¿Confirmas? (sí/no)
```

---

## 0.7 Scope — Qué Puede Tocar el Agente

| Zona                                                         | Acceso             |
| :----------------------------------------------------------- | :----------------- |
| `backend/`, `management/`, `task.md`, `.agent/`              | ✅ Libre           |
| `infra/`, `scripts/`, `.gitignore`, `AGENTS.md`, `DESIGN.md` | ⚠️ Confirmar antes |
| `infra/.env.*`, `.agent/secrets/`, `storage/`                | ❌ Nunca           |

---

## 0.8 Principios de Operación y Reglas de Oro

### 0.8.1 Las 5 Leyes del Modo Estricto

1. **Sin plan no hay ejecución.** El agente presenta el plan y espera confirmación explícita.
2. **Sin evidencia no hay "hecho".** Demostrar funcionamiento con logs, comandos, etc.
3. **Sin log no hay fix.** Analizar logs antes de proponer solución.
4. **Sin rama no hay código.** `/switch` inmediato si la tarea no corresponde.
5. **Un archivo por respuesta.** Estricto.
6. **Ley 6 — Sin memoria no hay contexto.** Antes de iniciar cualquier tarea, el agente DEBE leer `.agent/last_brain` y `.agent/memory/ACTIVE_CONTEXT.md` y verificar que el contexto mental del agente anterior ha sido procesado.

### 0.8.2 Resumen de Penalizaciones

| Infracción                      | Consecuencia                               |
| :------------------------------ | :----------------------------------------- |
| Ejecutar sin confirmar plan     | Desarrollador dice "STOP" — borra lo hecho |
| Marcar como hecho sin evidencia | Demostrar funcionamiento                   |
| Fix sin leer log                | Reformular diagnóstico                     |
| Código en rama incorrecta       | `/switch` inmediato                        |
| Más de un archivo por respuesta | Dev ignora respuesta                       |

### 0.8.3 Principios Operativos

1. **Una cosa a la vez:** Trabajar en un solo item del `task.md`.
2. **Detección proactiva de cambio de tarea:** Siempre preguntar: "¿Pertenece a la rama actual?".
3. **Parar ante problemas:** Nunca aplicar workarounds silenciosos.
4. **Acciones destructivas:** Confirmación obligatoria (sí/no) para `rm`, `migrate`, etc.
5. **Descomposición:** Todo plan debe ser dividido en pasos pequeños.

---

## 1. Identidad del Proyecto

**DX License Manager** es un portal empresarial interno para gestión avanzada de licencias de software con auditoría por IA.

| Dato          | Valor                                    |
| :------------ | :--------------------------------------- |
| Repo          | `github.com/DeXon18/DX-License-Manager`  |
| Beta          | `beta.dxpro.es` → `192.168.50.60:8002`   |
| Prod          | `portal.dxpro.es` → `192.168.50.60:8001` |
| Servidor      | LXC 600 `srv-dxportal` en Proxmox        |
| Desarrollador | DeXon (Oskar) — `dexon18@gmail.com`      |

El contexto completo de infraestructura vive en `.agent/secrets/identities.json` (local, nunca en Git).

---

## 2. Stack Tecnológico

**⚠️ El PC del desarrollador NO tiene ningún runtime instalado.** PHP, Composer, Artisan, MariaDB y Redis corren exclusivamente dentro de Docker en el LXC 600. Nunca ejecutar `php`, `composer`, `artisan` o `mysql` en local — siempre via SSH al servidor.

| Capa          | Tecnología                           |
| :------------ | :----------------------------------- |
| Backend       | PHP 8.2 / Laravel 11                 |
| Vistas        | Laravel Blade                        |
| CSS           | Tailwind CSS                         |
| JS            | Alpine.js (sin build step)           |
| BD            | MariaDB 10.11 LTS                    |
| Caché / Colas | Redis 7.x                            |
| Web server    | Nginx 1.25+                          |
| Contenedores  | Docker 24+ / Compose V2              |
| SSL           | Cloudflare (Nginx solo HTTP interno) |
| CI/CD         | GitHub Actions                       |

---

## 3. Reglas de Rama (Git) — Obligatorias

### 3.1 Una rama por funcionalidad — sin excepciones

Cada tarea del task.md que produzca código tiene su propia rama. Cuando la tarea termina, la rama termina.

```
feature/laravel-install     ← instalar Laravel
feature/auth-login          ← login web
feature/migrations-base     ← migrations del modelo real
feature/csv-importer        ← importador CSV
fix/nginx-redirect-loop     ← fix concreto
chore/update-agents-docs    ← solo documentación
```

**Prohibido:**

- ❌ Hacer login + migrations + importador CSV en la misma rama
- ❌ Acumular más de una funcionalidad por rama
- ❌ Reutilizar una rama de una sesión anterior para otra cosa distinta

### 3.2 Ciclo de vida de una rama

```
1. Crear rama desde dev
   git checkout dev
   git pull origin dev
   git checkout -b feature/nombre-corto

2. Trabajar — un commit por subtarea completada

3. Al terminar la tarea
   git push origin feature/nombre-corto
   → El desarrollador hace el PR y merge manualmente

4. Nunca reutilizar la rama después del merge
```

### 3.3 Commit tras cada subtarea — obligatorio

**Cada subtarea completada genera un commit. No esperar al final del bloque.**

```bash
# Al terminar cada item del task.md:
git status
git diff --cached
git add [archivos concretos]
git commit -m "feat(auth): add showLogin method to AuthController"
```

Convención de mensajes:

```
feat(scope):   nueva funcionalidad
fix(scope):    corrección de bug
chore(scope):  mantenimiento, config, docs
ci(scope):     cambios en CI/CD
```

**Prohibido:**

- ❌ `git commit -m "wip"`
- ❌ `git commit -m "changes"`
- ❌ `git add .` sin revisar `git status` primero
- ❌ Acumular varias subtareas en un solo commit
- ❌ Llegar al final del bloque sin haber commiteado nada

### 3.4 Operaciones Git destructivas — confirmación obligatoria

Nunca ejecutar sin "sí" explícito del desarrollador:

| Comando            | Por qué es peligroso                                                       |
| :----------------- | :------------------------------------------------------------------------- |
| `git push --force` | Reescribe historial remoto                                                 |
| `git reset --hard` | Descarta cambios locales permanentemente                                   |
| `git rebase`       | Reescribe commits                                                          |
| `git merge`        | Lo ejecuta el agente SOLO via `/merge` con CI en verde — nunca manualmente |
| `git stash drop`   | Borra trabajo sin commitear                                                |

### 3.5 Ramas protegidas

- `main` → producción (`portal.dxpro.es`) — deploy automático en cada push
- `dev` → integración (`beta.dxpro.es`) — deploy automático en cada push

**Nunca trabajar directamente en `main` ni en `dev`.**

---

## 4. Arquitectura Docker — Cómo Trabajar

Los stacks **beta** y **prod** son completamente independientes. Siempre lanzar desde la **raíz del proyecto** con `--project-directory .`:

```bash
# Beta (rama dev → beta.dxpro.es:8002)
docker compose --project-directory . -f infra/docker-compose.beta.yml up -d
docker compose --project-directory . -f infra/docker-compose.beta.yml ps
docker compose --project-directory . -f infra/docker-compose.beta.yml logs -f

# Prod (rama main → portal.dxpro.es:8001)
docker compose --project-directory . -f infra/docker-compose.prod.yml up -d
docker compose --project-directory . -f infra/docker-compose.prod.yml ps
docker compose --project-directory . -f infra/docker-compose.prod.yml logs -f
```

Cada stack tiene 4 servicios: `nginx`, `php-fpm`, `mariadb`, `redis`.  
Las variables de entorno viven en `infra/.env.beta` e `infra/.env.prod` — nunca en Git.

---

## 5. Flujo de Trabajo Diario

```
Tu PC (Antigravity via Samba Z:\DX-License-Manager\)
        │
        ├─ Editas código
        ├─ git checkout -b feature/lo-que-sea
        ├─ git commit -m "feat(...): descripción"
        ├─ git push origin feature/lo-que-sea
        │
        ▼
    Pull Request a dev
        │
        ├─ GitHub Actions: ci.yml (tests)
        ├─ Si pasan → merge a dev
        │
        ▼
    beta.dxpro.es actualizado automáticamente
        │
        ├─ Validación manual en beta
        │
        ▼
    Pull Request dev → main
        │
        ▼
    portal.dxpro.es actualizado automáticamente
```

El deploy automático usa SSH al LXC 600 vía **puerto 2222** (router externo → `192.168.50.60:22`).

---

## 6. Estructura de Carpetas Clave

```
DX-License-Manager/
├── .agent/skills/          ← Skills del agente (en Git)
├── .agent/
│   ├── rules/              ← Reglas de seguridad y estándares
│   ├── workflows/          ← Flujos de deploy y auditoría
│   └── secrets/            ← Solo local, NUNCA en Git
│       └── identities.json
├── backend/                ← Laravel 11
│   ├── app/Http/Controllers/
│   ├── app/Models/
│   ├── app/Services/AI/    ← Motor auditoría IA (FallbackChain)
│   ├── app/Jobs/           ← ProcessAuditJob (Redis)
│   ├── database/migrations/
│   ├── database/seeders/
│   └── resources/views/    ← Blade templates
├── infra/
│   ├── docker-compose.beta.yml
│   ├── docker-compose.prod.yml
│   ├── nginx/
│   │   ├── beta.conf
│   │   └── prod.conf
│   ├── php/Dockerfile
│   └── mariadb/
├── scripts/
│   ├── deploy.sh
│   ├── rollback.sh
│   └── backup-db.sh
├── storage/                ← Fuera de Git (bind mount en servidor)
│   ├── licenses/           ← Archivos .lic por vendor
│   └── backups/db/         ← mysqldump + gpg, cron diario
└── management/
    ├── BACKLOG.md
    ├── CHANGELOG.md
    └── ROADMAP.md
```

---

## 7. Seguridad — Reglas Fijas

- Las descargas de licencias usan IDs de BD, nunca rutas físicas: `/download?id=[UUID]`
- Las contraseñas y API keys solo van en `infra/.env.prod` e `infra/.env.beta` (en `.gitignore`)
- Los archivos `.lic` nunca se procesan en texto plano por la IA — solo metadatos
- Después de modificar controladores o middleware de auth, revisar con las skills `laravel-security-audit` y `php-security-auditor`
- JWT: access token 15 min + refresh token 24h con rotación automática
- RBAC: `admin` escribe, `technician` lee, `viewer` solo visualiza

---

## 8. Motor de Auditoría IA

El análisis de licencias usa un sistema de fallback entre proveedores:

```
Petición → Gemini (primario, 30s timeout)
         → Deepseek (fallback 1)
         → OpenRouter (fallback 2)
         → Error controlado → notificación Telegram + reintento en cola Redis
```

Clases relevantes:

- `app/Services/AI/AuditService.php` — orquestador
- `app/Services/AI/FallbackChain.php` — lógica de fallback
- `app/Jobs/ProcessAuditJob.php` — cola Redis
- `config/ai.php` — configuración de proveedores

---

## 9. Plan de Fases
 
| Fase                              | Estado            | Descripción                                    |
| :-------------------------------- | :---------------- | :--------------------------------------------- |
| 0 — Verificación Infraestructura  | 🔜 Pendiente      | nginx + HTML estático en beta y prod           |
| 1 a 7                             | 📋 En planificación | Sin definir — se detallan tras Fase 0        |
 
**Ahora mismo estamos iniciando la Fase 0.**
 
---

## 10. Skills — Uso Obligatorio

**Las skills NO son opcionales.** Son herramientas especializadas que el agente tiene disponibles en `.agent/skills/`. Antes de ejecutar cualquier tarea, el agente identifica qué skill corresponde, la carga y la aplica. No improvisar nunca cuando existe una skill para esa tarea.

### ⚠️ Regla de Activación — Sin Excepciones

```
Antes de empezar cualquier tarea:
  1. Revisar la tabla de activación de abajo
  2. Cargar la skill correspondiente
  3. Solo entonces ejecutar

Si no se carga la skill antes de empezar → la tarea está mal ejecutada.
```

El agente NO debe esperar a que el desarrollador le recuerde que existe una skill. Es responsabilidad del agente identificar y cargar la skill correcta **antes** de escribir la primera línea de código o ejecutar el primer comando.

---

### Catálogo de Skills y Activación

#### 🎨 `impeccable` + `ui-ux-pro-max`

**Qué hacen:** Sistema de diseño minimalista de alta precisión + inteligencia UI/UX avanzada.  
**Cargar cuando:** Cualquier tarea que toque HTML, Blade, CSS, Tailwind, Alpine.js, componentes visuales, layouts, formularios, modales, tablas o cualquier elemento que el usuario vea.  
**Nunca hacer sin ellas:** Crear o modificar cualquier vista sin haber leído el sistema de diseño. Improvisar clases Tailwind, colores o espaciado.  
**Nota:** Estas dos siempre van juntas. Si la tarea toca UI, se cargan las dos.

---

#### ⚙️ `laravel-expert`

**Qué hace:** Estándares Laravel 11, patrones de arquitectura, Eloquent, servicios, jobs, middleware, testing.  
**Cargar cuando:** Cualquier tarea que toque código PHP del proyecto — controladores, modelos, servicios, jobs, migrations, seeders, rutas, policies, tests, configuración Laravel.  
**Nunca hacer sin ella:** Escribir código Laravel improvisando patrones o estructura. Crear un controlador sin saber si debe ser thin. Escribir una query Eloquent sin considerar N+1.

---

#### 🐳 `docker-expert`

**Qué hace:** Buenas prácticas de Docker Compose, healthchecks, volúmenes, redes, Dockerfiles optimizados.  
**Cargar cuando:** Cualquier tarea que toque `infra/docker-compose.*.yml`, `infra/php/Dockerfile`, configuración de nginx, o cualquier archivo dentro de `infra/`.  
**Nunca hacer sin ella:** Modificar un docker-compose sin considerar healthchecks o dependencias entre servicios.

---

#### 🧹 `clean-code`

**Qué hace:** Refactor guiado por SOLID, naming semántico, reducción de complejidad ciclomática, eliminación de deuda técnica.  
**Cargar cuando:** Cualquier tarea de refactorización, cuando el código huele mal, cuando una función hace más de una cosa, cuando hay duplicación, o cuando se pide "limpiar" o "mejorar" código existente.  
**Nunca hacer sin ella:** Refactorizar "a ojo" sin principios claros.

---

#### 📋 `docs-architect`

**Qué hace:** Genera documentación técnica comprensiva desde el código: arquitectura, decisiones de diseño, manuales de onboarding, referencias técnicas largas.  
**Cargar cuando:** Se pide documentar la arquitectura del proyecto, crear un manual técnico, generar documentación de un módulo completo, o preparar materiales de onboarding para nuevos desarrolladores.  
**Nunca hacer sin ella:** Escribir documentación técnica de arquitectura improvisando estructura o formato.

---

#### 🔐 `laravel-security-audit`

**Qué hace:** Auditor de seguridad específico de Laravel. Analiza código pensando como atacante: IDOR, mass assignment, middleware faltante, autenticación insegura, RBAC incorrecto.  
**Cargar cuando:** Cualquier PR que toque controladores, middleware, policies, autenticación o autorización. Antes de hacer merge de cualquier feature que maneje datos de usuario o permisos.  
**Nunca hacer sin ella:** Aprobar código de autenticación, autorización o acceso a datos sin revisión de seguridad.

---

#### 🛡️ `php-security-auditor`

**Qué hace:** Auditoría OWASP Top 10 completa para PHP/Laravel. Incluye las reglas específicas de este proyecto (política Solo Log para licencias, JWT config, RBAC). Genera reporte estructurado con severidad, impacto y remediación.  
**Cargar cuando:** Auditoría de seguridad formal, antes de pasar código a producción, cuando se revisan endpoints de API, cuando se toca el sistema de subida de archivos `.lic` o el motor de auditoría IA.  
**Nunca hacer sin ella:** Evaluar la seguridad de cualquier componente sin seguir metodología OWASP.  
**Nota:** `laravel-security-audit` + `php-security-auditor` siempre van juntas en revisiones de seguridad formales.

---

#### 🔍 `find-skills`

**Qué hace:** Busca en el catálogo de skills disponibles para encontrar la más adecuada a una tarea concreta.  
**Cargar cuando:** La tarea no encaja claramente en ninguna skill de esta lista, o cuando no está claro qué skill aplicar.  
**Nunca hacer sin ella:** Asumir que "no hay skill para esto" sin haber buscado primero.

---

#### 📓 `karpathy` + `obsidian-bases` + `obsidian-markdown`

**Qué hacen:** `karpathy` implementa el patrón de wiki persistente con LLM — ingestión de fuentes, mantenimiento de páginas, cross-references, síntesis acumulativa. `obsidian-bases` y `obsidian-markdown` gestionan las convenciones de estructura y formato de la vault.  
**Cargar cuando:** Cualquier tarea que toque la vault Obsidian del proyecto: ingestar una fuente nueva, actualizar páginas wiki, crear notas de arquitectura, mantener el knowledge graph del proyecto.  
**Nunca hacer sin ellas:** Crear o editar notas en Obsidian sin seguir las convenciones de la vault. Ingestar una fuente sin actualizar los cross-references.  
**Nota:** Estas tres siempre van juntas cuando se trabaja con Obsidian.

---

### Combinaciones Frecuentes

```
Nueva vista Blade
  → impeccable + ui-ux-pro-max + laravel-expert

Nueva feature (controlador + vista + migration)
  → laravel-expert + impeccable + ui-ux-pro-max

PR listo para merge
  → laravel-security-audit + php-security-auditor

Modificar docker-compose o Dockerfile
  → docker-expert

Refactorizar un servicio existente
  → clean-code + laravel-expert

Documentar un módulo completo
  → docs-architect + laravel-expert

Trabajar en la vault Obsidian
  → karpathy + obsidian-bases + obsidian-markdown

No sé qué skill aplica
  → find-skills primero, siempre
```

---

### Checklist Obligatorio al Inicio de Cada Tarea

Responder estas preguntas antes de escribir la primera línea:

```
¿Toca UI, Blade, CSS o Alpine.js?     → impeccable + ui-ux-pro-max
¿Toca código PHP o Laravel?           → laravel-expert
¿Toca infra/, Docker o Dockerfile?    → docker-expert
¿Es un refactor o limpieza?           → clean-code
¿Hay un PR que revisar?               → laravel-security-audit + php-security-auditor
¿Es documentación de arquitectura?    → docs-architect
¿Toca Obsidian o la vault?            → karpathy + obsidian-bases + obsidian-markdown
¿No encaja en nada de lo anterior?    → find-skills
```

Si no se puede responder "ninguna" a todas → hay al menos una skill que cargar.

---

## 11. Comandos de Sesión

| Comando   | Workflow           | Cuándo ejecutarlo                                      |
| :-------- | :----------------- | :----------------------------------------------------- |
| `/start`  | `start-session.md` | Al abrir el proyecto                                   |
| `/log`    | `sync.md`          | Tras cada subtarea completada                          |
| `/sync`   | `sync.md`          | Al terminar un bloque completo                         |
| `/switch` | `switch-task.md`   | Cuando la nueva petición no pertenece a la rama activa |
| `/end`    | `end-session.md`   | Al cerrar la sesión                                    |

**Nunca empezar una tarea nueva sin preguntarse si pertenece a la rama activa.**
**Nunca cerrar sesión sin ejecutar `/end`.**

---

## 12. Lecciones Aprendidas (no repetir)

_(sin entradas aún — el proyecto parte desde cero)_
