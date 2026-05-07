# 📂 AI Agent Library Index — DX License Manager

Este archivo es el mapa de navegación del agente. **Leerlo es obligatorio antes de cualquier tarea.**
Consulta este índice para saber qué skills cargar, qué reglas aplicar y dónde están los recursos.

## 🧠 Memoria Activa — Leer Primero

Antes de consultar cualquier skill o regla, leer estos dos archivos en orden:

1. **`.agent/last_brain`** — párrafo técnico denso del estado mental del agente anterior.
2. **`.agent/memory/ACTIVE_CONTEXT.md`** — estado del proyecto, decisiones técnicas activas, handover de la sesión anterior.

Sin leer estos dos archivos, el agente trabaja sin contexto y repetirá errores ya resueltos.

---

## 🚦 Routing — Qué Cargar Según la Tarea

| Si la tarea involucra...                     | Cargar                                              |
| :------------------------------------------- | :-------------------------------------------------- |
| Cualquier vista, Blade, CSS, Alpine.js       | `impeccable` + `ui-ux-pro-max` (carga lazy — ver abajo) |
| Código PHP, Laravel, Eloquent, rutas, jobs   | `laravel-expert`                                    |
| Docker, Compose, Dockerfile, infra/          | `docker-expert`                                     |
| Refactor, limpieza, SOLID, naming            | `clean-code`                                        |
| Documentación técnica de arquitectura        | `docs-architect`                                    |
| Code review o PR antes de merge              | `laravel-security-audit`                            |
| Auditoría OWASP, vulnerabilidades, hardening | `php-security-auditor`                              |
| Vault Obsidian, notas, knowledge graph       | `karpathy` + `obsidian-bases` + `obsidian-markdown` ⏸️ Fase 3 |
| No sé qué skill aplica                       | `find-skills`                                       |

---

## ⚡ Productividad — Skills por Trigger

Estas skills no se cargan al inicio de sesión. Se activan únicamente por palabra clave.

| Trigger                                                    | Skill            |
| :--------------------------------------------------------- | :--------------- |
| `/caveman`, "less tokens", "be brief", "caveman mode"      | `caveman`        |
| "grill me", antes de arrancar una fase nueva               | `grill-me`       |
| "nueva skill", patrón repetido sin skill existente         | `write-a-skill`  |

- **`caveman`** → `.agent/skills/caveman/` — compresión extrema de tokens, activa hasta "normal mode"
- **`grill-me`** → `.agent/skills/grill-me/` — entrevista relentless para validar planes y arquitectura
- **`write-a-skill`** → `.agent/skills/write-a-skill/` — crear nuevas skills con estructura correcta

---

## 🧠 Core Intelligence — Skills Universales

- **`clean-code`** → `.agent/skills/clean-code/`
- **`docs-architect`** → `.agent/skills/docs-architect/`

---

## 🛠️ Especialistas de Stack

- **`laravel-expert`** → `.agent/skills/laravel-expert/`
- **`laravel-security-audit`** → `.agent/skills/laravel-security-audit/`
- **`php-security-auditor`** → `.agent/skills/php-security-auditor/`
- **`docker-expert`** → `.agent/skills/docker-expert/`
- **`claude-mem`** → `.agent/skills/claude-mem/` — memoria persistente y resúmenes

---

## 🎨 Diseño y Frontend — Carga Lazy

`impeccable` y `ui-ux-pro-max` son skills extensas. **No cargar el archivo completo.**

Protocolo de carga lazy:
1. Leer el índice interno de la skill (`INDEX.md` o sección de contenidos)
2. Identificar la sección relevante para la tarea concreta
3. Cargar solo esa sección

- **`impeccable`** → `.agent/skills/impeccable/` — leer índice primero
- **`ui-ux-pro-max`** → `.agent/skills/ui-ux-pro-max/` — leer índice primero

> ⚠️ Referencia visual obligatoria: `infra/html/` — replicar en Blade sin inventar estilos. Ver tabla de archivos en `DESIGN.md`.

---

## 📓 Conocimiento y Documentación — ⏸️ Fase 3

Estas skills están aparcadas hasta Fase 3. No cargar antes.

- **`karpathy`** → `.agent/skills/karpathy/`
- **`obsidian-bases`** → `.agent/skills/obsidian-bases/`
- **`obsidian-markdown`** → `.agent/skills/obsidian-markdown/`

---

## 🔍 Meta

- **`find-skills`** → `.agent/skills/find-skills/`

---

## 🛡️ Reglas — Cumplimiento Obligatorio

| Archivo                      | Qué regula                                                        |
| :--------------------------- | :---------------------------------------------------------------- |
| `security-check.md`          | OWASP, JWT, RBAC, ID-Abstraction, política Solo Log               |
| `code-review-reasoning.md`   | Metodología de code review antes de merge                         |
| `debug-reasoning.md`         | Diagnóstico sistemático de bugs                                   |
| `documentation-integrity.md` | El historial de gestión nunca se borra                            |
| `negative-contrast.md`       | Validación por contraste negativo en tareas críticas              |

---

## 🗂️ Workflows — Comandos de Sesión

| Comando   | Archivo                             | Cuándo                   |
| :-------- | :---------------------------------- | :----------------------- |
| `/start`  | `.agent/workflows/start-session.md` | Al abrir el proyecto     |
| `/log`    | `.agent/workflows/sync.md`          | Tras cada subtarea       |
| `/sync`   | `.agent/workflows/sync.md`          | Al terminar un bloque    |
| `/switch` | `.agent/workflows/switch-task.md`   | Al cambiar de tarea/rama |
| `/merge`  | `.agent/workflows/merge-feature.md` | Al terminar una feature  |
| `/end`    | `.agent/workflows/end-session.md`   | Al cerrar la sesión      |

---

## 📁 Estructura de Carpetas Clave

```
.agent/
├── last_brain              ← estado mental del agente anterior
├── INDEX.md                ← estás aquí
├── IDENTITY.md             ← comportamiento y tono del agente
├── CHECKLIST.md            ← checklist de entrega
├── lessons.md              ← lecciones aprendidas
├── rules/                  ← reglas siempre activas
├── workflows/              ← flujos de sesión
├── memory/
│   ├── ACTIVE_CONTEXT.md   ← estado activo de la sesión
│   └── PROJECT_MAP.md      ← flujos de datos del sistema
├── skills/
│   ├── caveman/            ← productividad — trigger /caveman
│   ├── grill-me/           ← productividad — trigger "grill me"
│   ├── write-a-skill/      ← productividad — trigger "nueva skill"
│   ├── clean-code/
│   ├── docker-expert/
│   ├── docs-architect/
│   ├── find-skills/
│   ├── impeccable/         ← submodule, carga lazy
│   ├── karpathy/           ← ⏸️ Fase 3
│   ├── laravel-expert/
│   ├── laravel-security-audit/
│   ├── obsidian-bases/     ← ⏸️ Fase 3
│   ├── obsidian-markdown/  ← ⏸️ Fase 3
│   ├── php-security-auditor/
│   └── ui-ux-pro-max/      ← submodule, carga lazy
└── secrets/                ← LOCAL, NUNCA en Git
    ├── _vars.json
    ├── identities.json
    └── mcp.[equipo].json
```

---

## 🔒 Zonas Prohibidas

| Zona                                  | Restricción                                          |
| :------------------------------------ | :--------------------------------------------------- |
| `.agent/secrets/`                     | **NUNCA** leer ni modificar — solo el desarrollador  |
| `infra/.env.beta` / `infra/.env.prod` | **NUNCA** leer — contienen contraseñas reales        |
| `storage/`                            | **NUNCA** tocar — archivos físicos de licencias      |
| `obsidian/`                           | Solo lectura — nunca modificar sin instrucción       |

