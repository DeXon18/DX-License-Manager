# HANDOFF — DX License Manager
> Última actualización: 2026-05-16 15:50  
> Sesión en: Antigravity Beta  
> Rama activa: dev

---

## Estado General

**Fase actual:** Fase de Mantenimiento y Estabilización ✅  
**Stack beta:** ✅ running  
**Stack prod:** ✅ running  

---

## Qué se hizo en esta sesión

1.  **Fix COD Preview (#015)**: 
    *   Reparado nesting HTML en `cod.blade.php` que bloqueaba modales.
    *   Refactor de rutas de almacenamiento a **MAYÚSCULAS** (Nombre Real Cliente).
    *   Limpieza de infraestructura: Eliminada carpeta residual `backend/storage/private`.
2.  **UX Optimization (Herramientas)**:
    *   Reubicado asistente IA de Composite a sección contextual "Nueva Máquina".
    *   Eliminados botones redundantes para limpiar el layout.
3.  **Higiene Documental**:
    *   Actualizados `BACKLOG.md`, `CHANGELOG.md` y `ERRORS.md`.
    *   Merge de las ramas `fix/cod-preview-fail` y `fix/cod-delete-file-fail` a `dev`.

---

## Qué falta por hacer (próxima sesión)

### Tarea inmediata (empezar aquí)
Revisar la incidencia **#008 (Unificación de estilos CSS)** en `ERRORS.md`. El objetivo es mover los estilos locales inyectados en `clients/show.blade.php` y `tools/cod.blade.php` al archivo central `public/css/dx-styles.css` para limpiar las vistas.


### Tareas siguientes
1. Iniciar **Fase 15 (Integraciones IA)**: Configuración y test de conexión con Gemini 3.1 Flash-Lite y DeepSeek.
2. Refinar la lógica de n8n para soporte de ramificación por vendor.

---

## Contexto técnico importante

*   La rotación de tokens JWT ahora solo ocurre si el token tiene más de 5 minutos de vida. Esto soluciona los errores de "Sesión expirada" durante cargas rápidas de componentes Alpine.js.
*   El diseño de las licencias unificadas usa opacidades muy bajas (0.04) para cumplir con la estética "NOC Pro" solicitada por Oskar.

---

## Bloqueos o problemas sin resolver

Ninguno.

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
# Ver logs del contenedor PHP (Beta)
docker compose --project-directory . -f infra/docker-compose.beta.yml logs -f dx-php-beta

# Forzar limpieza de caché tras cambios en CSS
docker exec dx-php-beta php artisan view:clear && docker exec dx-php-beta php artisan cache:clear
```
