# HANDOFF — DX License Manager
> Última actualización: 2026-06-10 10:48  
> Sesión en: PC de Oskar (Windows)
> Rama activa: dev

---

## Estado General

**Fase actual:** Post-Despliegue — Producción (v3.2.6) desplegada con éxito.  
**Stack beta:** ✅ running  
**Stack prod:** ✅ running  

---

## Qué se hizo en esta sesión

- **Despliegue a Producción (v3.2.6)**: Se realizó un backup de seguridad completo de la BD de Producción.
- Se crearon los tags de seguridad `v3.2.6-pre-deploy-dev` en dev y `v3.2.6-pre-deploy-main` en main.
- Se resolvió conflicto de merge en `CHANGELOG.md` al hacer merge de dev a main.
- Push exitoso a main que disparó el Action `deploy-prod.yml`.
- Verificación exitosa de los contenedores de producción.
- Retorno a la rama `dev` para continuar el desarrollo.

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
