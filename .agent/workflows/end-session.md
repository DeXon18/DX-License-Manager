# Workflow: Fin de Sesión
> Ruta: `.agents/workflows/end-session.md`  
> Trigger: Al terminar el trabajo del día, o al ejecutar `/end`

---

## Objetivo

Dejar el proyecto en un estado limpio y autoexplicado para que cualquier agente
(en cualquiera de los 4 PCs) pueda retomar el trabajo sin preguntas y sin contexto previo.

El `HANDOFF.md` es la memoria entre sesiones. Debe quedar tan claro que si mañana
empiezas desde otro PC con el agente frío, sepa exactamente qué hacer en los próximos 30 segundos.

---

## Pasos

### 1. Ejecutar sync primero

Antes de cerrar, ejecutar el workflow `/sync` para que la documentación esté al día.
No cerrar sesión sin hacer esto.

### 2. Verificar estado Git limpio

```bash
git status
git stash list
```

- Si hay cambios sin commitear → commitearlos o descartarlos explícitamente
- Si hay stashes → aplicarlos, commitearlos o borrarlos
- No dejar trabajo a medias sin documentar

```bash
# Si todo está limpio:
git status
# → "nothing to commit, working tree clean"

# Si hay algo en progreso que no puede commitearse aún:
git stash push -m "WIP: descripción de lo que está en progreso"
```

### 3. Anotar el estado de los stacks

```bash
docker compose --project-directory . -f infra/docker-compose.beta.yml ps
docker compose --project-directory . -f infra/docker-compose.prod.yml ps
```

Registrar si están up o down para dejarlo en el HANDOFF.

### 4. Reescribir HANDOFF.md

Este archivo se **sobreescribe completamente** en cada sesión. No es un historial — es una foto del momento actual.

```markdown
# HANDOFF — DX Management Portal
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

[Lista concreta de lo que se completó — nombres de archivos, comandos ejecutados, decisiones tomadas]

---

## Qué falta por hacer (próxima sesión)

### Tarea inmediata (empezar aquí)
[Una sola tarea, la más prioritaria, con instrucciones concretas de cómo empezar]

### Tareas siguientes
1. [tarea 2]
2. [tarea 3]

---

## Contexto técnico importante

[Cualquier cosa que el agente de la próxima sesión necesite saber y que no esté en otro archivo:
decisiones tomadas, problemas encontrados, workarounds aplicados, comandos especiales]

---

## Bloqueos o problemas sin resolver

[Si hay algo bloqueado, describir exactamente el problema y qué se intentó]
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

### 5. Commit final de cierre

```bash
git add management/HANDOFF.md management/CHANGELOG.md management/ROADMAP.md management/BACKLOG.md
git commit -m "docs(handoff): cierre de sesión YYYY-MM-DD"
git push origin [rama-activa]
```

El push asegura que el HANDOFF esté disponible desde cualquiera de los 4 PCs al día siguiente.

### 6. Confirmar cierre al desarrollador

```
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
🔴 SESIÓN CERRADA — DX Management Portal
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

✅ Sync completado
✅ Git limpio — rama: [nombre]
✅ HANDOFF.md actualizado y pusheado
✅ Próxima tarea documentada: [primera tarea del HANDOFF]

Stack beta:  [estado]
Stack prod:  [estado]

Puedes cerrar Antigravity.
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```
