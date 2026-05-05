# CHANGELOG — DX License Manager

> Historial completo de cambios desde el inicio del proyecto.
> **Regla:** Nunca eliminar entradas. Las nuevas entradas van siempre al principio.

---

## [2026-05-05] — Resolución de Assets y Refactor de Layout (Fase 1 y 2 ✅)

### Fixed
- **Desbloqueo de Assets**: Eliminado alias de Nginx para assets externos. Ahora se sirven nativamente desde `backend/public/assets/`.
- **Refactor de Diseño**: Eliminadas clases Tailwind de las vistas Blade y migrado al sistema de CSS Semántico oficial (`dx-styles.css`).
- **Fuentes Locales**: Eliminada dependencia de Google Fonts externos. Ahora se sirven archivos `.woff2` locales.
- **Permisos de Escritura**: Corregidos permisos `777` en `storage/` y `bootstrap/cache/` del servidor.
- **Docker Orchestration**: Añadido `depends_on` en Nginx para garantizar que el upstream PHP esté listo.

### Added
- Documentado aprendizaje en `.agent/lessons.md`.
- Layout principal Blade completamente responsivo y alineado con los prototipos HTML.
- Instalación de Laravel 11 en `backend/`.
- Configuración de Docker para PHP 8.4-FPM, MariaDB y Redis.
- Implementación de `AppServiceProvider` para forzar HTTPS en assets.

## [2026-05-05] — Inicialización de Infraestructura (Fase 0)
...

### Added
- Inicialización del repositorio Git local y conexión con el remoto en GitHub.
- Configuración de ramas `main` y `dev`.
- Creación de workflows de GitHub Actions:
  - `ci.yml`: Verificación básica de estructura.
  - `deploy-beta.yml`: Despliegue automático a stack beta vía SSH.
  - `deploy-prod.yml`: Despliegue automático a stack prod vía SSH.
- Estructura base de carpetas y archivos de gestión (`management/`, `infra/`, etc.) subida al repositorio.
