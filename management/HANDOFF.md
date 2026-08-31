# HANDOFF — DX License Manager
> Última actualización: 2026-08-31 09:40  
> Sesión en: indeterminado  
> Rama activa: dev

---

## Estado General

**Fase actual:** Fase 19 / Estabilización v3.9.2 (Actualización de URLs & n8n Callback)  
**Stack beta:** ✅ running  
**Stack prod:** ✅ running  

---

## Qué se hizo en esta sesión

- Actualización de dominios y URLs principales:
  - Desarrollo/Beta: `https://soporteays-dev.dxpro.es` (APP_URL y AUDIT_CALLBACK_URL).
  - Producción: `https://soporteays.dxpro.es` (APP_URL y AUDIT_CALLBACK_URL).
- Configuración de Nginx: añadido `soporteays-dev.dxpro.es` a `server_name` en `infra/nginx/beta.conf` y resuelta la incidencia 502 al refrescar la resolución FastCGI.
- Sincronización del callback de n8n (`AUDIT_CALLBACK_URL`) en ambos entornos para garantizar el retorno de datos tras auditorías de licencias.
- Restauración de la base de datos MariaDB Beta desde el último backup `beta_system_2026-07-29_01-00-01.sql`.
- Actualización de `AdminUserSeeder` y `AuthTest` adaptándolos a Spatie RBAC (gestión de roles por `name`), restableciendo la contraseña del administrador a `Venganza69`.
- Verificación exhaustiva de endpoints HTTP en Beta y Producción (ambos respondiendo HTTP 200 en login).

---

## Qué falta por hacer (próxima sesión)

### Tarea inmediata (empezar aquí)
Revisar el BACKLOG con Oskar para iniciar el siguiente módulo o requerimiento funcional.

### Tareas siguientes
1. Continuar con tareas pendientes del Roadmap.
2. Desarrollos futuros en inventario y visor de procesamiento asíncrono.

---

## Contexto técnico importante

- Al actualizar variables de entorno en `infra/.env.beta` o `infra/.env.prod`, es obligatorio reiniciar/recrear los contenedores PHP para que recarguen las variables del host, y posteriormente reiniciar Nginx para que resuelva la nueva IP de upstream de Docker.
- El proyecto utiliza Spatie Permission (`spatie/laravel-permission`), por lo que los roles se gestionan mediante `assignRole('nombre_rol')` en lugar de la columna legacy `role_id`.

---

## Bloqueos o problemas sin resolver

Ninguno

---

## Estado de archivos clave

| Archivo | Estado |
|:---|:---|
| `infra/.env.prod` | ✅ configurado (`https://soporteays.dxpro.es`) |
| `infra/.env.beta` | ✅ configurado (`https://soporteays-dev.dxpro.es`) |
| `backend/.env` | ✅ configurado |
| `backend/vendor/` | ✅ instalado |

---

## Comandos útiles para la próxima sesión

```bash
# Arrancar beta si está down
docker compose --project-name dx-license-manager-dev --project-directory /opt/web-projects/Development -f /opt/web-projects/Development/infra/docker-compose.beta.yml up -d

# Entrar al contenedor PHP
docker exec -it dx-php-beta sh

# Ver logs en tiempo real
docker compose --project-name dx-license-manager-dev --project-directory /opt/web-projects/Development -f /opt/web-projects/Development/infra/docker-compose.beta.yml logs -f nginx-beta
```
