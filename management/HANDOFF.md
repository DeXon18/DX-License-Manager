# HANDOFF — DX License Manager
> Última actualización: 2026-10-02 10:20  
> Sesión en: indeterminado  
> Rama activa: main

---

## Estado General

**Fase actual:** Despliegue a Producción v3.9.4 (Duración de Sesión 8h) ✅  
**Stack beta:** ✅ running  
**Stack prod:** ✅ running  

---

## Qué se hizo en esta sesión

- Despliegue a Producción de v3.9.3 (Fecha Mínima de Expiración en licencias).
- Despliegue seguro a Producción (`main`) de la **Ampliación de Sesión JWT a 8 Horas (480 minutos)**:
  - `AuthController.php`: token y cookie inicial extendidos a 480 min.
  - `JwtAuth.php`: renovación por rotación extendida a 480 min y Redis active TTL a 28800s.
  - `JwtService.php`: expiración por defecto elevada a 480 min.
  - Verificada la suite `AuthTest` pasando al 100%.
  - Backup preventivo en Producción verificado (`prod_manual_...sql`).
  - CHANGELOG.md incrementado a `v3.9.4`.

---

## Qué falta por hacer (próxima sesión)

### Tarea inmediata (empezar aquí)
Revisar el BACKLOG con el desarrollador para definir la siguiente prioridad.

### Tareas siguientes
1. Continuar con roadmap y backlog de mejoras.
2. Desarrollos futuros en inventario y visor de procesamiento asíncrono.

---

## Contexto técnico importante

- Al actualizar variables de entorno en `infra/.env.beta` o `infra/.env.prod`, es obligatorio reiniciar/recrear los contenedores PHP para que recarguen las variables del host, y posteriormente reiniciar Nginx para que resuelva la nueva IP de upstream de Docker.
- El proyecto utiliza Spatie Permission (`spatie/laravel-permission`), por lo que los roles se gestionan mediante `assignRole('nombre_rol')` en lugar de la columna legacy `role_id`.
- Al desplegar código, si las vistas fallan con "Permission denied" en `file_put_contents`, SIEMPRE debe limpiarse la caché de las vistas (`php artisan view:clear`) y asegurar que los permisos de `/storage/framework/views` son `777`.

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
