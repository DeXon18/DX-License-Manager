# HANDOFF — DX License Manager
> Última actualización: 2026-06-01 12:51  
> Sesión en: PC Oskar
> Rama activa: dev

---

## Estado General

**Fase actual:** Post-Fase 33 — Seguridad Auditada y Versionado 3.0.1 Centralizado  
**Stack beta:** ✅ running (Healthy)  
**Stack prod:** ✅ running (Healthy)  

---

## Qué se hizo en esta sesión

- **Patch de UI (v3.0.1):** Se corrigió un fallo molesto en el Tour de Bienvenida (Driver.js). Ahora el tour solo se autoinicia en las páginas que tienen configurados pasos específicos, y el botón "Cerrar" guarda la preferencia del usuario instantáneamente de forma silenciosa sin el popup de confirmación invasivo.
- **Centralización de Versión (v3.0.0):** Se ha extraído la definición de la versión del sistema fuera de los archivos `.env` y código duro, centralizándolo en el propio `CHANGELOG.md`. Ahora `config/dx.php` lee mediante expresiones regulares la etiqueta de versión directamente desde el inicio del historial, haciendo del CHANGELOG la Única Fuente de Verdad.
- **Infraestructura (Emails & Cron):** Se añadieron contenedores dedicados de `queue` y `scheduler` en los archivos Docker Compose para permitir el procesamiento en segundo plano de tareas programadas (ej. reportes semanales) y envíos de emails asíncronos en Beta y Producción.
- **Seguridad & Limpieza:** Se detectó la fuga de un Token de Telegram Bot en el historial de Git (en un commit antiguo del HANDOFF). Se usó `git-filter-repo` para purgar completamente el historial y se forzó push a las ramas. El token fue rotado exitosamente en BotFather y actualizado en los `.env`.
- **UI & Ribbon Beta:** Se eliminó el antiguo badge estático de la barra lateral en favor de un Ribbon CSS flotante en la esquina superior derecha exclusivo para entornos no-producción (`.dx-v2-beta-ribbon`).
- **Documentación:** Se reestructuró y pulió el `README.md` con vocabulario enterprise (aclarando la diferencia entre el Gemini nativo para composite/chatbot y el DeepSeek orquestado por n8n para FlexLM), además de referenciar explícitamente el directorio de auditorías `docs/`.
- **Infraestructura:** Se resolvieron los errores recurrentes `502 Bad Gateway` en Nginx causados por el desajuste de IPs tras los reinicios dinámicos del contenedor PHP-FPM originados por los cambios en `.env`.

---

## Qué falta por hacer (próxima sesión)

### Tarea inmediata (empezar aquí)
Evaluar la creación de un Pull Request o hacer un merge directo de `dev` hacia `main` para que Producción refleje formalmente la versión `2.8.0` en su historial principal.

### Tareas siguientes
1. Iniciar el desarrollo de la siguiente gran funcionalidad del backlog (Ej. Exportación/gestión del Planificador de Renovaciones).
2. Monitorizar que el nuevo token de Telegram no arroja errores `401 Unauthorized` en el Webhook.

---

## Contexto técnico importante

- Nginx en los stacks Beta y Prod sigue perdiendo sincronización con la IP de `php-fpm` si este último contenedor es recreado o reiniciado (ej. por tocar el `.env`). Ante cualquier `502 Bad Gateway`, la solución inmediata sigue siendo reiniciar el contenedor Nginx (`docker compose restart nginx-beta/prod`).
- El socket de Docker (`/var/run/docker.sock`) requiere reasignación de permisos `chmod 666` cada vez que se levantan los contenedores para asegurar que el NOC Dashboard de Laravel pueda leer la telemetría local de Docker en el servidor LXC.
- El repositorio local Windows está ya sincronizado con el árbol purgado sin el token filtrado.

---

## Bloqueos o problemas sin resolver

Ninguno. La infraestructura está 100% operativa y sana.

---

## Estado de archivos clave

| Archivo | Estado |
|:---|:---|
| `infra/.env.prod` | ✅ configurado (Telegram token actualizado) |
| `infra/.env.beta` | ✅ configurado (Telegram token actualizado y v2.8.0) |
| `backend/.env` | ✅ configurado |
| `backend/vendor/` | ✅ instalado |

---

## Comandos útiles para la próxima sesión

```bash
# Reiniciar Nginx si hay un 502 Bad Gateway
docker compose --project-directory . -f infra/docker-compose.beta.yml restart nginx-beta

# Ver el estado de los servicios
docker compose --project-directory . -f infra/docker-compose.beta.yml ps
```
