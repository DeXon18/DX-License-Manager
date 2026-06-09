# HANDOFF — DX License Manager
> Última actualización: 2026-06-09 11:00  
> Sesión en: PC de Oskar (Windows)
> Rama activa: dev

---

## Estado General

**Fase actual:** Post-Despliegue — Feature "Superseded"  
**Stack beta:** ✅ running  
**Stack prod:** ✅ running  

---

## Qué se hizo en esta sesión

- Se ha eliminado la opción obsoleta "Recibir reportes semanales de caducidad" en el perfil de cliente (`backend/resources/views/clients/show.blade.php`), ya que los envíos semanales se redirigen al equipo de soporte y no a clientes individuales.
- Eliminado el código muerto relacionado (`WeeklyLicenseAlert.php` y su vista).
- Actualizado CHANGELOG con el registro del refactor.
- Todo ha sido commiteado y verificado en logs de `php-fpm-beta`.

---

## Qué falta por hacer (próxima sesión)

### Tarea inmediata (empezar aquí)
Revisar ROADMAP o BACKLOG para seleccionar la siguiente gran tarea a desarrollar. Se mencionó en la sesión anterior comprobar la necesidad de agregar `dx:mark-superseded` al kernel scheduler.

### Tareas siguientes
1. Integración de `dx:mark-superseded` si se confirma necesario.
2. Continuar roadmap.

---

## Contexto técnico importante

El comando de `SendWeeklyLicenseAlertsJob` envía reportes globales a `soporte@ats-global.com`. Las opciones por contacto en UI eran código muerto, de ahí la limpieza para simplificar la interfaz.

---

## Bloqueos o problemas sin resolver

Existen carpetas temporales (`X__Carpeta Temporal/`) en local, se ignoraron y no afectan el repositorio. Ningún bloqueo.

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
# Arrancar beta si está down
docker compose --project-directory /opt/web-projects/DX-License-Manager-DEV -f /opt/web-projects/DX-License-Manager-DEV/infra/docker-compose.beta.yml up -d

# Entrar al contenedor PHP
docker exec -it dx-php-beta sh

# Ver logs en tiempo real
docker compose --project-directory /opt/web-projects/DX-License-Manager-DEV -f /opt/web-projects/DX-License-Manager-DEV/infra/docker-compose.beta.yml logs -f nginx-beta
```
