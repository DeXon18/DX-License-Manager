---
description: 
---

# Workflow: Fin de Sesión
> Ruta: `.agent/workflows/end-session.md`  
> Trigger: Al terminar el trabajo del día, o al ejecutar `/end`

---

## Objetivo

Dejar el proyecto en un estado limpio y autoexplicado para que cualquier agente
(en cualquiera de los 4 PCs) pueda retomar el trabajo sin preguntas y sin contexto previo.

El `HANDOFF.md` es la fuente de verdad entre sesiones. El `last_brain` es el contexto técnico
denso para arranque rápido. Ambos deben quedar actualizados antes de cerrar.

---

## Pasos — Seguir en orden estricto

### 1. Ejecutar sync primero

Antes de cerrar, ejecutar el workflow `/sync` para que la documentación esté al día.
No cerrar sesión sin hacer esto.

---

### 2. Verificar estado Git limpio

```bash
git status
git stash list
```

- Si hay cambios sin commitear → commitearlos o descartarlos explícitamente
- Si hay stashes → aplicarlos, commitearlos o borrarlos
- No dejar trabajo a medias sin documentar

```bash
# Si hay algo en progreso que no puede commitearse aún:
git stash push -m "WIP: descripción de lo que está en progreso"
```

⚠️ **No continuar al paso siguiente si `git status` no está limpio.**

---

### 3. Anotar el estado de los stacks

```bash
docker compose --project-directory . -f infra/docker-compose.beta.yml ps
docker compose --project-directory . -f infra/docker-compose.prod.yml ps
```

Registrar si están up o down para el HANDOFF y el resumen de cierre.

---

### 4. Archivar last_brain y escribir el nuevo

> `last_brain` es el contexto técnico denso de la sesión actual — un párrafo compacto
> que permite al agente de la próxima sesión recuperar el estado mental en segundos.

**Paso 4.1 — Archivar la versión anterior antes de sobreescribir:**

```bash
# Crear directorio si no existe
mkdir -p .agent/memory/brain_history

# Copiar last_brain actual al historial con fecha
cp .agent/last_brain .agent/memory/brain_history/brain_$(date +%Y-%m-%d).md
```

**Paso 4.2 — Escribir el nuevo last_brain:**

Redactar un párrafo técnico denso con todo lo que el agente de la próxima sesión
necesita saber para orientarse en 10 segundos:

```
Rama activa: [nombre]. Fase [X] — [nombre fase]. 
Último trabajo: [qué se hizo concretamente — archivos tocados, comandos ejecutados].
Estado: [qué funciona, qué no, qué está a medias].
Próximo paso inmediato: [una acción concreta].
Bloqueos: [si hay alguno / "ninguno"].
Stack beta: [running/down]. Stack prod: [running/down].
```

Guardar en `.agent/last_brain` (sobreescribir).

**Paso 4.3 — Limpiar historial antiguo (mantener solo los últimos 5):**

```bash
# Eliminar entradas más antiguas si hay más de 5
ls -t .agent/memory/brain_history/brain_*.md | tail -n +6 | xargs rm -f
```

---

### 5. Reescribir HANDOFF.md

Este archivo se **sobreescribe completamente** en cada sesión. No es un historial — es una foto del momento actual.

```markdown
# HANDOFF — DX License Manager
> Última actualización: YYYY-MM-DD HH:MM  
> Sesión en: [nombre del PC o "indeterminado"]  
> Rama activa: [nombre]

---

## Estado General

**Fase actual:** Fase X — [nombre]  
**Stack beta:** ✅ running / ❌ down  
**Stack prod:** ✅ running / ❌ down  

---

## Qué se hizo en esta sesión

[Lista concreta de lo completado — nombres de archivos, comandos ejecutados, decisiones tomadas]

---

## Qué falta por hacer (próxima sesión)

### Tarea inmediata (empezar aquí)
[Una sola tarea, la más prioritaria, con instrucciones concretas de cómo empezar]

### Tareas siguientes
1. [tarea 2]
2. [tarea 3]

---

## Contexto técnico importante

[Decisiones tomadas, problemas encontrados, workarounds aplicados, comandos especiales]

---

## Bloqueos o problemas sin resolver

[Si hay algo bloqueado: describir el problema y qué se intentó]
[Si no hay nada bloqueado: "Ninguno"]

---

## Estado de archivos clave

| Archivo | Estado |
|:---|:---|
| `infra/.env.prod` | ✅ configurado / ⚠️ pendiente |
| `infra/.env.beta` | ✅ configurado / ⚠️ pendiente |
| `backend/.env` | ✅ configurado / ⚠️ pendiente / ❌ no existe aún |
| `backend/vendor/` | ✅ instalado / ❌ pendiente `composer install` |

---

## Comandos útiles para la próxima sesión

```bash
# Arrancar beta si está down
docker compose --project-directory . -f infra/docker-compose.beta.yml up -d

# Entrar al contenedor PHP
docker exec -it dx-php-beta sh

# Ver logs en tiempo real
docker compose --project-directory . -f infra/docker-compose.beta.yml logs -f nginx-beta
```
```

---

### 6. Commit final de cierre

```bash
git add management/HANDOFF.md management/CHANGELOG.md management/ROADMAP.md management/BACKLOG.md .agent/last_brain .agent/memory/brain_history/
git commit -m "docs(handoff): cierre de sesión $(date +%Y-%m-%d)"
git push origin [rama-activa]
```

El push garantiza que HANDOFF y last_brain estén disponibles desde cualquiera de los 4 PCs.

---

### 7. Confirmar cierre al desarrollador

```
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
🔴 SESIÓN CERRADA — DX License Manager
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

✅ Sync completado
✅ Git limpio — rama: [nombre]
✅ last_brain archivado → brain_history/brain_[fecha].md
✅ last_brain actualizado — próxima sesión arranca con contexto
✅ HANDOFF.md actualizado y pusheado
✅ Próxima tarea documentada: [primera tarea del HANDOFF]

Stack beta:  [estado]
Stack prod:  [estado]

Puedes cerrar Antigravity.
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```