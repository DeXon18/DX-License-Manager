# CHANGELOG — DX License Manager

> Historial completo de cambios desde el inicio del proyecto.
> **Regla:** Nunca eliminar entradas. Las nuevas entradas van siempre al principio.

---

## [2026-05-05] — Stack Laravel y Assets (Fase 1 y 2)

### Added
- Instalación de Laravel 11 en `backend/`.
- Configuración de Docker para PHP 8.4-FPM, MariaDB y Redis.
- Implementación de `AppServiceProvider` para forzar HTTPS en assets.
- Mapeo de Nginx `/assets/` hacia volumen `infra/assets/`.
- Sistema de diseño `dx-styles.css` con variables CSS y clases de utilidad.
- Layout principal Blade con Sidebar, Header y Footer.

### Fixed
- Error de Mixed Content al cargar assets vía Cloudflare.
- Mapeo de assets de Nginx que servía versiones obsoletas de la carpeta public.
- Inconsistencia de versiones de PHP entre dependencias de Composer y contenedor.

### Pending
- **Bug UI**: Dashboard se visualiza sin estilos en producción/beta. Posible caché de Cloudflare o conflicto de clases.

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
