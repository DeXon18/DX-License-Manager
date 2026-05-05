---
trigger: always_on
---

# 📄 Integridad Documental — Regla Absoluta

## La Regla

**El contenido de los archivos de gestión NUNCA se elimina.**

Se puede resumir, reorganizar, mover, actualizar o marcar como completado.
**Borrar está prohibido.**

Esta regla aplica a todos los archivos de `management/` sin excepción:

- `management/CHANGELOG.md`
- `management/ROADMAP.md`
- `management/BACKLOG.md`
- `management/HANDOFF.md`

---

## Por Qué Existe Esta Regla

El historial de un proyecto es su memoria técnica. Un CHANGELOG desde la v1.0 con todas sus entradas vale más que el código — explica por qué las cosas son como son, qué se intentó antes, qué falló, qué decisiones se tomaron y por qué.

Eliminar entradas antiguas "por orden" o "por limpieza" es destruir contexto irreemplazable.

---

## Qué Está Permitido

| Acción | ¿Permitido? |
|:---|:---|
| Añadir una entrada nueva | ✅ Sí |
| Marcar una tarea como completada | ✅ Sí |
| Mover una tarea de "Pendiente" a "Completado" | ✅ Sí |
| Resumir una entrada larga en menos líneas | ✅ Sí (conservando el significado) |
| Corregir errores tipográficos | ✅ Sí |
| Reorganizar secciones | ✅ Sí (sin eliminar contenido) |
| Archivar entradas antiguas en una sección `## Archivo` al final | ✅ Sí |
| **Eliminar entradas antiguas del CHANGELOG** | ❌ **Nunca** |
| **Borrar tareas del BACKLOG aunque estén completadas** | ❌ **Nunca** |
| **Reescribir el ROADMAP eliminando fases ya hechas** | ❌ **Nunca** |
| **Vaciar el HANDOFF para "empezar limpio"** | ❌ **Nunca** |

---

## Comportamiento Correcto por Archivo

### CHANGELOG.md
Las entradas se añaden **al principio** (más reciente primero). Las entradas anteriores permanecen intactas debajo. Nunca se reemplaza el archivo entero, solo se añade arriba.

```markdown
## [2026-05-10] — Nueva feature X    ← añadir aquí arriba
...contenido nuevo...

## [2026-04-26] — Docker stack prod  ← esto permanece intacto
...contenido anterior...

## [2026-04-20] — Estructura base    ← esto permanece intacto
...
```

### ROADMAP.md
Las fases completadas se marcan con ✅ pero **no se eliminan**. El ROADMAP debe mostrar siempre el recorrido completo del proyecto, incluyendo lo que ya se hizo.

```markdown
## Fase 0 — Estructura base ✅ COMPLETADA   ← permanece, marcada
## Fase 1 — Docker + CI/CD ✅ COMPLETADA    ← permanece, marcada
## Fase 2 — Laravel base 🔜 EN CURSO        ← actualizar así
```

### BACKLOG.md
Las tareas completadas se mueven a la sección `## Completado` pero **no se borran**. Si no existe esa sección, crearla.

```markdown
## En Progreso
- Tarea X

## Completado
- ~~Tarea Y~~ — completada 2026-04-26   ← mover aquí, no borrar
- ~~Tarea Z~~ — completada 2026-04-20
```

### HANDOFF.md
El HANDOFF se **sobreescribe** en cada sesión (es una foto del momento actual, no un historial). Esta es la única excepción a la regla — pero solo porque su propósito es exactamente ese: estado actual, no historial. El historial de sesiones vive en el CHANGELOG.

---

## Si el Agente Siente Tentación de Borrar

Antes de eliminar cualquier línea de estos archivos, preguntarse:

> "¿Esta información podría ser útil en el futuro para entender por qué algo es como es?"

Si la respuesta es posiblemente sí → **no borrar**. Mover a una sección `## Archivo` al final del documento si estorba visualmente.

---

## Cómo Detectar una Violación

Si el CHANGELOG de repente empieza en una versión reciente sin entradas anteriores → el agente borró el historial. Recuperar con:

```bash
git log --oneline management/CHANGELOG.md
git show [commit-hash]:management/CHANGELOG.md
```
