# HANDOFF — DX License Manager
> Última actualización: 2026-06-01 13:30  
> Sesión en: indeterminado  
> Rama activa: dev

---

## Estado General

**Fase actual:** Post-Fase 33 — Estabilización v3.0.1 (Tour & Background Workers)  
**Stack beta:** ✅ running  
**Stack prod:** ✅ running  

---

## Qué se hizo en esta sesión

- **Patch de UI e Integridad (v3.0.1):** Se corrigió un fallo persistente en el Tour de Bienvenida (Driver.js). Se eliminó el `confirm()` invasivo al cerrarlo y se evitó que el tour arrancara en pantallas genéricas sin pasos propios definidos.
- **Migración Crítica en Producción:** Se detectó que la columna `has_seen_tour` no existía en Producción. Se realizó un backup manual (`backup-db.sh prod`) y se forzó la migración pendiente, resolviendo el bug silencioso que impedía guardar las preferencias del tour.
- **Centralización de Versión (v3.0.0 a v3.0.1):** Se purgó la lectura estática del `.env` y el `VERSION.json`. Todo el sistema (Laravel y README) lee la versión maestra dinámicamente mediante Regex desde la cabecera del `CHANGELOG.md`.
- **Despliegues Finales:** Merge de estabilizaciones a `main`, generación de los tags de Git correspondientes (`v3.0.0` y `v3.0.1`) y despliegue completado hacia Producción (`portal.dxpro.es`).

---

## Qué falta por hacer (próxima sesión)

### Tarea inmediata (empezar aquí)
*A la espera de nuevas prioridades por parte de Oskar.* Sugerencia: Revisar y refinar nuevas "Ideas Pendientes" en el BACKLOG (Soporte UX, Reportes Dinámicos, etc.).

### Tareas siguientes
1. Esperando revisión del desarrollador.

---

## Contexto técnico importante

- **Tour y Persistencia:** El endpoint `profile.tour-seen` requiere la columna `has_seen_tour` en BD. Los fallos del tour solían ser silenciados porque el frontend usaba `console.log()` en las promesas y no alertaba al usuario del error 500 originado por la BD desincronizada.
- **Centralización CHANGELOG:** La expresión regular usada en `config/dx.php` para la versión es `/^\>\s*\*\*Version:\*\*\s*(v[\d\.]+)/m`. Si alguna vez se altera ese patrón en la línea 3 del CHANGELOG, el sistema revertirá al fallback `v3.0.0`.
- **Cron / Queue Containers:** Beta y Prod ahora corren 3 contenedores PHP: fpm, scheduler (cron) y worker (colas Redis). Cuidado al mirar logs, hay que especificar a cuál se desea atacar.

---

## Bloqueos o problemas sin resolver

Ninguno. La infraestructura y la interfaz se encuentran completamente saludables y estables en todos los entornos.

---

## Estado de archivos clave

| Archivo | Estado |
|:---|:---|
| `infra/.env.prod` | ✅ configurado |
| `infra/.env.beta` | ✅ configurado |
| `backend/.env` | ✅ configurado (sin APP_VERSION) |
| `backend/vendor/` | ✅ instalado |

---

## Comandos útiles para la próxima sesión

```bash
# Arrancar beta si está down
docker compose --project-directory . -f infra/docker-compose.beta.yml up -d

# Entrar al contenedor PHP principal
docker exec -it dx-php-beta sh

# Ver logs del scheduler (tareas programadas)
docker compose --project-directory . -f infra/docker-compose.beta.yml logs -f dx-php-scheduler-beta
```
