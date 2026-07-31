# HANDOFF — DX License Manager
> Última actualización: 2026-07-31 10:45  
> Sesión en: Windows (Agent)  
> Rama activa: dev

---

## Estado General

**Fase actual:** Soporte Fecha Inicio Licencias (v3.9.1)  
**Stack beta:** ✅ running  
**Stack prod:** ✅ running  

---

## Qué se hizo en esta sesión

- **Soporte de Fecha de Inicio en Licencias y Ajustes UI (`v3.9.1`):**
  - **Licencias:** Parseo de la fecha de inicio (`START=...`) de los archivos procesados por n8n (webhook) y guardado en `start_date`.
  - **UI / Inventario:** Añadida columna "Inicio" y cálculo del estado "PENDIENTE DE ACTIVACIÓN".
  - **Diseño:** Tabla de inventario balanceada, usando nowrap para la descripción y permitiendo ajuste automático sin hacks CSS.

- **Selector de Año y Filtros en Planificador de Renovaciones (`v3.8.0`):**
  - Selector dinámico de año y barra de filtros por estado en `/planificador`.

---

## Qué falta por hacer (próxima sesión)

### Tarea inmediata (empezar aquí)
Revisar con Oskar nuevas solicitudes o módulos adicionales requeridos.

### Tareas siguientes
1. Evaluar tareas del BACKLOG / ROADMAP.

---

## Contexto técnico importante

- Las licencias NCmatic se asocian por `serial_number` y se vinculan a los clientes con contratos NCmatic.
- La vista `/clientes` mantiene la cuadrícula de 4 tarjetas KPI (`dx-v2-sys-dash-stats-grid`).

---

## Bloqueos o problemas sin resolver

Ninguno. Todos los stacks operando con normalidad.

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
# Limpiar caché de vistas en Beta
docker exec dx-php-beta php artisan view:clear

# Ver logs de PHP Beta
docker compose --project-directory . -f infra/docker-compose.beta.yml logs --tail=50 dx-php-beta
```
