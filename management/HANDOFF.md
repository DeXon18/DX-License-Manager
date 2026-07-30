# HANDOFF — DX License Manager
> Última actualización: 2026-07-30 17:30  
> Sesión en: Windows (Agent)  
> Rama activa: dev

---

## Estado General

**Fase actual:** Módulo NCmatic & Mejoras UX (v3.9.0)  
**Stack beta:** ✅ running  
**Stack prod:** ✅ running  

---

## Qué se hizo en esta sesión

- **Módulo de Licencias NCmatic por Número de Serie (`v3.9.0`):**
  - **Base de Datos & Modelo:** Creada migración `ncmatic_licenses` y modelo `NcmaticLicense` con relación `belongsTo(Client)` y `hasMany(NcmaticLicense)` en `Client`.
  - **Gestor NCmatic:** Creado `NcMaticController` y la vista `/herramientas/ncmatic` con buscador en tiempo real por número de serie, cliente o notas, selector de clientes por contrato NCmatic y modal interactivo para alta y edición.
  - **Simplificación de Puestos:** Eliminado el campo de puestos/asientos (fijado en 1 por número de serie individual).
  - **Ficha de Cliente (`/clientes/{id}`):** Integrada la pestaña **NCmatic** para ver y gestionar licencias asignadas al cliente.
  - **Directorio Principal (`/clientes`):** Integrado el filtro **NCmatic** en la barra de control segmentado, badge verde esmeralda para el recuento de seriales y mantenida la cuadrícula armónica de 4 tarjetas KPI.
  - **Cumplimiento `DESIGN.md`:** Todo el diseño refactorizado a clases nativas del sistema (`dx-v2-form-input`, `dx-v2-form-select`, `modal-overlay`, `modal-content`) sin inline styles.

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
