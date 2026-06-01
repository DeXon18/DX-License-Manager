# HANDOFF — DX License Manager
> Última actualización: 2026-06-01 11:51  
> Sesión en: Windows local (Z: drive mapeado)  
> Rama activa: `dev`

---

## Estado General

**Fase actual:** Desacoplamiento de Entornos y Telemetría NOC ✅ COMPLETADA  
**Stack beta:** Operativo (volúmenes de telemetría y docker.sock restaurados)  
**Stack prod:** Operativo  

---

## Qué se hizo en esta sesión

1. **Environment Agnosticism** — Refactorizados los controladores (`BackupController`, `SystemActionController`) y scripts (`backup-db.sh`) para usar la variable inyectada `$DB_HOST` y no depender del string hardcodeado "beta".
2. **Chatbot Service** — Eliminada la URL hardcodeada de `beta.dxpro.es` en la cabecera `HTTP-Referer` de OpenRouter (ahora usa `config('app.url')`).
3. **Telemetría NOC Dashboard** — Mapeados los volúmenes `storage_beta:ro` y `storage_prod:ro` en ambos `docker-compose` para que el contenedor PHP pueda reportar tamaños reales de los directorios sin importar el entorno.
4. **Fix Docker Daemon Socket** — Se descubrió y documentó (en `AGENTS.md`) que `docker compose up -d` en un entorno LXC pierde el permiso de lectura para el usuario web. Se ejecutó `chmod 666 /var/run/docker.sock` en el host, restaurando el módulo de monitorización de contenedores en el NOC Dashboard.
5. **Merge** — La rama `feature/env-decoupling` fue validada, commiteada, pusheada y mergeada a `dev` vía GitHub por el usuario. La rama local y remota ha sido borrada, y el repositorio limpiado con `git remote prune origin`.

---

## Qué falta por hacer (próxima sesión)

### Tarea inmediata (empezar aquí)
Consultar con Oskar la próxima prioridad en el BACKLOG o validar si la rama `dev` está lista para ser subida a Producción (`main`) y cerrar release.

---

## Contexto técnico importante

- Al recrear contenedores que usen el `DockerMonitorService`, recordar que el socket `/var/run/docker.sock` puede requerir un nuevo `chmod 666` en Proxmox si el usuario `www-data` pierde el acceso. Documentado en lecciones aprendidas de `AGENTS.md`.
- La aplicación es ahora completamente agnóstica respecto a su entorno (beta vs prod). La lógica se controla 100% mediante las variables inyectadas en los archivos `.env.beta` y `.env.prod`.
