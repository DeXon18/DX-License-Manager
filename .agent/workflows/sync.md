---
description: 
---

# Workflow: Sincronización
> Ruta: `.agent/workflows/sync.md`  
> Triggers: `/sync` (completo) · `/log` (micro-sync) · automático al terminar cada subtarea del task.md

---

## El problema que resuelve esto

Hacer `/sync` al final de una sesión larga genera respuestas enormes que desbordan
el límite de tokens del modelo. La solución es documentar de forma incremental —
una entrada pequeña cada vez que se completa algo, no todo junto al final.

---

## Dos modos de uso

### Modo `/log` — Micro-sync (usar frecuentemente)

Ejecutar después de completar **cada subtarea o bloque** del `task.md`.
Solo actualiza el CHANGELOG con una entrada mínima. Rápido, sin riesgo de overflow.

**Qué hace:**
1. Añadir UNA entrada al CHANGELOG con lo que se acaba de completar (máx 5 líneas)
2. Marcar el item en `task.md` si aplica
3. **No tocar** ROADMAP, BACKLOG ni HANDOFF
4. Commit mínimo:
```bash
git add management/CHANGELOG.md
git commit -m "docs(log): [descripción breve de lo hecho]"
```

**Formato de entrada `/log`:**
```markdown
## [YYYY-MM-DD HH:MM] — [Qué se completó]

### Added
- item

### Fixed
- item si aplica
```

**Etiquetas permitidas — siempre estas, siempre en inglés:**
`### Added` · `### Changed` · `### Fixed` · `### Removed` · `### Security` · `### Pending`

Nunca inventar etiquetas nuevas.

---

### Modo `/sync` — Sync completo (usar al terminar un bloque grande o antes de `/end`)

Solo ejecutar cuando se termina un **bloque completo** del task.md, no subtareas individuales.
Actualiza todos los archivos pero de forma **acotada** — solo lo del bloque actual, no toda la sesión.

**Regla anti-overflow:** Cada actualización debe ser proporcional al trabajo hecho.
Si se completó 1 bloque → 1 entrada de CHANGELOG, 1-2 cambios en BACKLOG.
Nunca intentar resumir toda la sesión de golpe.

**Pasos:**

**1. CHANGELOG** — Una sola entrada para el bloque completado:
```markdown
## [YYYY-MM-DD] — [Nombre del bloque]

### Añadido
- item concreto (archivo, comando, decisión)

### Cambiado / Arreglado
- si aplica
```

**2. ROADMAP** — Solo si una fase completa se cerró:
- Marcar subtarea como `✅` si se verificó que funciona
- Si no se verificó → no marcar

**3. BACKLOG** — Solo mover las tarjetas del bloque completado:
- `En Progreso` → `Completado`
- No reorganizar ni tocar el resto

**4. Commit:**
```bash
git add management/CHANGELOG.md management/ROADMAP.md management/BACKLOG.md
git commit -m "docs(sync): [nombre del bloque] completado"
```

**5. Confirmación:**
```
✅ SYNC — [Bloque X]
- CHANGELOG: entrada añadida
- ROADMAP: [cambio o "sin cambios"]
- BACKLOG: [tarjeta movida o "sin cambios"]
```

---

## Flujo recomendado en una sesión

```
Empezar subtarea 1.1
  → completar
  → /log  (30 segundos, entrada mínima en CHANGELOG)

Empezar subtarea 1.2
  → completar
  → /log

...

Terminar Bloque 1 completo
  → /sync  (actualiza ROADMAP + BACKLOG + entrada de bloque en CHANGELOG)

Empezar Bloque 2...
  → /log por cada subtarea
  → /sync al terminar el bloque

Al final de la sesión
  → /end  (solo actualiza HANDOFF — el resto ya está documentado)
```

---

## Señales de que vas a tener overflow

- Llevas más de 2 horas sin hacer `/log`
- El task.md tiene más de 5 items marcados sin documentar
- Estás a punto de hacer `/sync` con 3+ bloques acumulados

Si esto ocurre → no hacer `/sync` completo. Hacer varios `/log` seguidos,
uno por bloque, en lugar de uno grande.