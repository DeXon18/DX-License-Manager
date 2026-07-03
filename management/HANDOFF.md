# HANDOFF — DX License Manager
> Última actualización: 2026-07-03 07:53  
> Sesión en: local (Windows)  
> Rama activa: dev

---

## Estado General

**Fase actual:** Fase 29 — AI Routing Hub ✅ Completada. En espera de nuevas tareas.  
**Stack beta:** ⚠️ indeterminado (SSH no disponible en esta sesión)  
**Stack prod:** ⚠️ indeterminado (SSH no disponible en esta sesión)  

---

## Qué se hizo en esta sesión

- **Limpieza crítica de git:** `.agent/` estaba completamente rastreado en `dev` y `main` (327 archivos).
- Actualizado `.gitignore` → excluye `.agent/` completo (antes solo excluía `secrets/` y `brain/`).
- Ejecutado `git rm -r --cached .agent/` en ambas ramas.
- Commiteado y pusheado en `dev` y `main` exitosamente.
- `.agent/` es ahora **local-only** en todos los entornos.

---

## Qué falta por hacer (próxima sesión)

### Tarea inmediata (empezar aquí)
Verificar si hay deploy pendiente del COD Generator (renombrado de placeholders a "LM Host (MAC)"). El `last_brain` anterior indicaba que los cambios estaban en `dev` y `main` pero faltaba ejecutar `./scripts/deploy-prod.sh`. Confirmar con Oskar si sigue pendiente.

### Tareas siguientes
1. Definir con Oskar las tareas de la próxima fase del ROADMAP.

---

## Contexto técnico importante

**CAMBIO ARQUITECTURAL IMPORTANTE — 2026-07-03:**  
`.agent/` ya NO está en git. Consecuencias:
- `last_brain` y `brain_history/` son **solo locales** — no se sincronizan entre PCs via git.
- En otro PC, `.agent/` deberá reinstalarse manualmente o copiarse.
- `management/HANDOFF.md` es la única fuente de verdad inter-sesión en git.
- El `.gitignore` correcto ya está pusheado en `dev` y `main`.

---

## Bloqueos o problemas sin resolver

- SSH MCP (`ssh-local`) no disponible en esta sesión — estado real de los stacks Docker desconocido.
- Verificar manualmente que `nginx-beta`, `dx-php-beta`, `mariadb-beta` y `redis-beta` siguen up.

---

## Estado de archivos clave

| Archivo | Estado |
|:---|:---|
| `infra/.env.prod` | ✅ configurado (no en git) |
| `infra/.env.beta` | ✅ configurado (no en git) |
| `.gitignore` | ✅ actualizado — `.agent/` excluido completo |
| `management/HANDOFF.md` | ✅ este archivo |

---

## Comandos útiles para la próxima sesión

```bash
# Verificar stacks
docker compose --project-directory /opt/web-projects/DX-License-Manager-DEV -f /opt/web-projects/DX-License-Manager-DEV/infra/docker-compose.beta.yml ps

# Arrancar beta si está down
docker compose --project-directory /opt/web-projects/DX-License-Manager-DEV -f /opt/web-projects/DX-License-Manager-DEV/infra/docker-compose.beta.yml up -d

# Deploy a producción (si procede)
./scripts/deploy-prod.sh
```
