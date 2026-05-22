# HANDOFF — DX License Manager
> Última actualización: 2026-05-22 12:20
> Sesión en: local
> Rama activa: dev

---

## Estado General

**Fase actual:** Fase 29 — Rediseño y Módulo Costes IA  
**Stack beta:** ✅ running  
**Stack prod:** ✅ running  

---

## Qué se hizo en esta sesión

- Se añadió una 4ta tarjeta en el Bento Grid de Costes de IA ('Total Peticiones') para corregir asimetría visual.
- Se reparó el bug 500 (`$totalCostThisMonth` no definida).
- Se mapearon las acciones a nombres legibles ('Herramienta de Licencias (Normalización)', 'Procesador COD', etc).
- Se añadió la métrica de 'tk/req' dividiendo tokens entre requests, en una línea separada.
- Se eliminó la clase `.dx-v2-sys-dash-main-layout` del layout del módulo Costes IA porque su `display: grid` reservaba 340px para un sidebar fantasma, dejando un enorme margen negro a la derecha.
- Se documentó en `AGENTS.md` la lección de NUNCA usar `docker exec` desde PowerShell local en Windows, y vaciamos el cache de vistas borrando `storage/framework/views/*`.

---

## Qué falta por hacer (próxima sesión)

### Tarea inmediata (empezar aquí)
Revisar `BACKLOG.md` con el usuario para determinar la siguiente prioridad (por ejemplo, el módulo FAQ).

### Tareas siguientes
1. Crear el sistema de FAQ (como acordado anteriormente).
2. Refinamiento final de UI.

---

## Contexto técnico importante

- Los comandos de Docker NUNCA se deben ejecutar desde el agente local. Si hay que vaciar la caché, usar Artisan vía web (si aplica) o vaciar los archivos por Samba (red compartida `Z:`).
- `beta.dxpro.es` está sincronizado con la rama `dev`.

---

## Bloqueos o problemas sin resolver

Ninguno

---

## Estado de archivos clave

| Archivo | Estado |
|:---|:---|
| `infra/.env.prod` | ✅ configurado |
| `infra/.env.beta` | ✅ configurado |
| `backend/.env` | ✅ configurado |
| `backend/vendor/` | ✅ instalado |

---

## Comandos útiles para la próxima sesión

```bash
# Arrancar beta si está down (Desde Proxmox Host, NO local)
docker compose --project-directory . -f infra/docker-compose.beta.yml up -d

# Ver logs (Desde Proxmox Host)
docker compose --project-directory . -f infra/docker-compose.beta.yml logs -f
```
