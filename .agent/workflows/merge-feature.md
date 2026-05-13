---
description: 
---

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
- Los logs del contenedor están limpios (sin errores)

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

### 2. Crear el Pull Request

**Detectar si MCP GitHub está disponible antes de continuar.**

---

**✅ CON MCP GitHub disponible:**

```
Crear PR via MCP GitHub:
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

**⚠️ SIN MCP GitHub — manual:**

```bash
# 1. Asegurarse de que la rama está pusheada
git push origin feature/[nombre]
```

Mostrar al desarrollador:

```
⚠️ MCP GitHub no disponible — PR manual requerido

Abre este enlace para crear el PR:
https://github.com/DeXon18/DX-License-Manager/compare/dev...feature/[nombre]

Configurar así:
  - base:  dev
  - head:  feature/[nombre]
  - title: feat([scope]): [descripción breve]

Avísame cuando el PR esté creado con su número → continúo desde aquí.
```

⛔ No continuar al Paso 3 hasta que Oskar confirme el número de PR.

---

### 3. Verificar CI

**✅ CON MCP GitHub disponible:**

Consultar el estado del PR via MCP y esperar a que el workflow `CI / test` complete.

```
✅ CI en verde — [tiempo]s
```

**⚠️ SIN MCP GitHub:**

```
⏳ CI corriendo — verifica el estado en:
https://github.com/DeXon18/DX-License-Manager/pull/[número]

Avísame cuando CI esté en verde → continúo con el merge.
```

⛔ Si CI falla → **PARAR**. No mergear. Mostrar el error al desarrollador y resolver primero.

```
❌ CI falló — no se puede mergear
Error: [descripción del fallo]
Acción requerida: resolver antes de continuar
```

---

### 4. Hacer el Merge

Una vez CI en verde:

**✅ CON MCP GitHub disponible:**

```
Merge PR #[número] via MCP GitHub:
  - Método: merge commit (no squash, no rebase — preservar historial)
  - Commit message: "Merge pull request #[número]: feat([scope]): [descripción]"
```

Confirmar al desarrollador:

```
✅ PR #[número] mergeado a dev
GitHub Actions Deploy Beta arrancando...
```

---

**⚠️ SIN MCP GitHub — manual:**

```
⚠️ MCP GitHub no disponible — merge manual requerido

Abre el PR y haz merge desde GitHub:
https://github.com/DeXon18/DX-License-Manager/pull/[número]

Usar: "Create a merge commit" (no squash, no rebase)

Avísame cuando hayas hecho el merge → continúo con la limpieza de ramas.
```

⛔ No continuar al Paso 5 hasta que Oskar confirme que el merge está hecho.

---

### 5. Borrar la Rama — Local y Remota

⚠️ **Este paso es obligatorio. No saltar. No marcar como hecho sin ejecutar los comandos.**

**5.1 — Eliminar rama remota:**

```bash
git push origin --delete feature/[nombre]
```

Verificar que se eliminó:

```bash
git branch -r | grep feature/[nombre]
# Si no aparece nada → eliminada correctamente
# Si sigue apareciendo → repetir el comando
```

**5.2 — Sincronizar y eliminar rama local:**

```bash
git checkout dev
git pull origin dev
git branch -d feature/[nombre]
```

Si `-d` falla porque Git dice que no está mergeada del todo:

```bash
git branch -D feature/[nombre]
```

**5.3 — Limpiar referencias remotas obsoletas:**

```bash
git remote prune origin
```

**5.4 — Verificar estado final de ramas:**

```bash
# Ramas locales — feature/[nombre] NO debe aparecer
git branch

# Ramas remotas — feature/[nombre] NO debe aparecer
git branch -r
```

Mostrar al desarrollador el output de ambos comandos como evidencia.

```
🗑️ Rama feature/[nombre] eliminada
   Local:  ✅ no aparece en git branch
   Remota: ✅ no aparece en git branch -r
```

⛔ **Si la rama sigue apareciendo en cualquiera de los dos listados → NO continuar al paso 6. Resolver primero.**

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
Rama local:   ✅ eliminada (verificado con git branch)
Rama remota:  ✅ eliminada (verificado con git branch -r)
CI:        ✅ verde en [tiempo]s
Deploy:    ✅ beta.dxpro.es actualizado
BACKLOG:   ✅ tarea movida a Completado
CHANGELOG: ✅ entrada añadida
HANDOFF:   ✅ actualizado

Siguiente tarea: [primera tarea pendiente del BACKLOG]
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```

---

## Limpieza de ramas huérfanas

Si hay ramas antiguas que quedaron sin borrar de sesiones anteriores:

```bash
# Ver todas las ramas locales
git branch

# Ver todas las ramas remotas
git branch -r

# Borrar rama local huérfana
git branch -D feature/[nombre-viejo]

# Borrar rama remota huérfana
git push origin --delete feature/[nombre-viejo]

# Limpiar referencias remotas obsoletas
git remote prune origin
```

---

## Cuándo NO ejecutar este workflow

- Si la feature no está verificada funcionalmente
- Si CI está fallando
- Si hay conflictos con dev no resueltos
- Si el desarrollador no ha dado el visto bueno a la feature
- Si los logs del contenedor tienen errores

---

## Merge a main (producción)

Este workflow es solo para `feature → dev`. El merge de `dev → main` es un proceso separado que se hace cuando dev está estable y validado en beta. Se ejecuta con `/deploy-prod` (workflow pendiente de crear).