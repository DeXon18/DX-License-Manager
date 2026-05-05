# Workflow: Merge de Feature

> Ruta: `.agent/workflows/merge-feature.md`
> Trigger: Al terminar una feature completa y verificada — ejecutar `/merge`

---

## Objetivo

Cerrar una rama de feature de forma limpia, trazable y documentada. El agente ejecuta todo el proceso — PR, CI, merge, limpieza y documentación — dejando registro en BACKLOG, CHANGELOG y HANDOFF.

---

## Prerequisitos

Antes de ejecutar `/merge`, verificar que:

- La feature está completa y verificada (tests pasando, funcionalidad comprobada)
- Hay al menos un commit en la rama
- El CHANGELOG tiene la entrada correspondiente a lo hecho

Si alguno de estos puntos falla → no ejecutar `/merge`. Resolver primero.

---

## Pasos

### 1. Registrar la tarea en BACKLOG

Antes de tocar Git, documentar lo que se está cerrando.

Mover la tarea de `## 🔵 En Progreso` a `## ✅ Completado` en `management/BACKLOG.md`:

```markdown
### [nombre de la feature]

**Completada:** YYYY-MM-DD
**Rama:** feature/nombre-corto
**Resumen:** [qué se implementó en 2-3 líneas]
**PR:** #[número] — merged a dev
```

Commitear el BACKLOG actualizado en la rama de feature:

```bash
git add management/BACKLOG.md
git commit -m "docs(backlog): feature/[nombre] completada"
git push origin feature/[nombre]
```

---

### 2. Crear el Pull Request via MCP GitHub

```
Crear PR:
  - title: "feat([scope]): [descripción breve]"
  - base: dev
  - head: feature/[nombre]
  - body: resumen de lo implementado + lista de archivos principales tocados
```

Mostrar al desarrollador:

```
🔀 PR creado: #[número]
Título: [título]
Base: dev ← feature/[nombre]
URL: https://github.com/DeXon18/DX-License-Manager/pull/[número]

Esperando que CI pase en verde...
```

---

### 3. Verificar CI

Esperar a que el workflow `CI / test` complete.

```
✅ CI en verde — [tiempo]s
```

Si CI falla → **PARAR**. No mergear. Mostrar el error al desarrollador y resolver primero.

```
❌ CI falló — no se puede mergear
Error: [descripción del fallo]
Acción requerida: resolver antes de continuar
```

---

### 4. Hacer el Merge

Una vez CI en verde:

```
Via MCP GitHub:
  - Merge PR #[número]
  - Método: merge commit (no squash, no rebase — preservar historial)
  - Commit message: "Merge pull request #[número]: feat([scope]): [descripción]"
```

Confirmar al desarrollador:

```
✅ PR #[número] mergeado a dev
GitHub Actions Deploy Beta arrancando...
```

---

### 5. Borrar la Rama

```bash
# Borrar rama remota via MCP GitHub
# Borrar rama local
git checkout dev
git pull origin dev
git branch -d feature/[nombre]
```

Confirmar:

```
🗑️ Rama feature/[nombre] eliminada (local y remota)
```

---

### 6. Actualizar CHANGELOG

Añadir entrada en `management/CHANGELOG.md` si no está ya:

```markdown
## [YYYY-MM-DD HH:MM] — [Nombre de la feature]

### Added / Changed / Fixed

- [item concreto]
- [item concreto]
```

---

### 7. Actualizar HANDOFF

Actualizar `management/HANDOFF.md`:

- Marcar la feature como completada
- Actualizar "Tarea inmediata" con la siguiente del BACKLOG
- Actualizar estado de ramas

---

### 8. Commit de documentación en dev

```bash
git add management/CHANGELOG.md management/HANDOFF.md management/BACKLOG.md
git commit -m "docs(sync): merge feature/[nombre] — documentación actualizada"
git push origin dev
```

---

### 9. Verificar Deploy

Esperar confirmación de que GitHub Actions completó el deploy a beta.

```bash
# Verificar que nginx sigue respondiendo tras el deploy
curl -I http://localhost:8002
```

---

### 10. Confirmación Final

```
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
✅ MERGE COMPLETADO — feature/[nombre]
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
PR:        #[número] mergeado a dev
Rama:      feature/[nombre] eliminada
CI:        ✅ verde en [tiempo]s
Deploy:    ✅ beta.dxpro.es actualizado
BACKLOG:   ✅ tarea movida a Completado
CHANGELOG: ✅ entrada añadida
HANDOFF:   ✅ actualizado

Siguiente tarea: [primera tarea pendiente del BACKLOG]
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```

---

## Cuándo NO ejecutar este workflow

- Si la feature no está verificada funcionalmente
- Si CI está fallando
- Si hay conflictos con dev no resueltos
- Si el desarrollador no ha dado el visto bueno a la feature

---

## Merge a main (producción)

Este workflow es solo para `feature → dev`. El merge de `dev → main` es un proceso separado que se hace cuando dev está estable y validado en beta. Se ejecuta con `/deploy-prod` (workflow pendiente de crear).
