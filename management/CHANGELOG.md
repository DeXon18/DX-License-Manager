> Historial completo de cambios desde el inicio del proyecto.
> **Regla:** Nunca eliminar entradas. Las nuevas entradas van siempre al principio.

---

## [2026-05-07] — Gestión de Memoria y Reglas de Control (Sesión Actual)
- **Skills**: Integrada la habilidad `claude-mem` para persistencia semántica entre sesiones.
- **Git/GitHub**: Implementada regla innegociable de Puntos de Control (Tags) tras cada fase terminada.
- **Git/GitHub**: Otorgado permiso explícito al agente para crear Pull Requests.
- **Cleanup**: Realizada limpieza masiva de ramas locales y remotas ya integradas (`feature/clients-base`, `feature/csv-importer-base`, etc.).
- **Rollback**: Deshecha la migración `ai_audit_results` y borrado el archivo para mantener `dev` limpio ante el replanteamiento de la Fase 8.
- **Roadmap**: Bloqueada la Fase 8 (Siemens) por problema grave detectado. Pendiente de investigación por Oskar.

## [2026-05-06] — Sincronización y Lecciones (Fase 8.1)
- **Sincronización**: Restaurada la rama `dev` tras un fallo arquitectónico en el inicio de la Fase 8.1.
- **Lección Aprendida (UI)**: Uso estricto de `dx-styles.css` sin introducir Tailwind CSS no autorizado.
- **Lección Aprendida (Rutas)**: Respetar la convención de rutas en castellano (`/herramientas`) y no sobreescribir lógica validada en fases anteriores.

## [2026-05-06] — Fase 7: Hub de Herramientas ✅
- **UI/UX**: Implementado Hub de utilidades dinámico agrupado por Vendor (Siemens / Moldex3D).
- **Backend**: Creado `ToolController` y modelo `FeatureFlag` para gestión de accesos.
- **Identidad**: Sincronización total de llaves, etiquetas y daemons con `identities.json`.
- **Navegación**: Sidebar y Header vinculados al Hub centralizado.
- **Feature Flags**: Control visual de herramientas no activas ("Próximamente").

---

## [2026-05-06] — Fase 6.3: Gestión de Contactos ✅
- Implementación de CRUD de contactos vinculados a clientes.
- Sistema de pestañas en perfil de cliente con persistencia vía `localStorage`.
- Interfaz de contactos compacta con botones de acción horizontales y modales Alpine.js.
- Creación de `DemoContactSeeder` para pruebas rápidas.
- Corrección de acceso SSH y limpieza de `known_hosts` para despliegue.

## [2026-05-06] — Refinamiento UI Clientes
- [x] Leyenda de estados integrada en la Card de contratos (Fase 6.1).
- [x] Estilo alineado con `DESIGN.md` (jerarquía técnica y card-footer).
- [x] Refinamiento estético de la leyenda de estados en el ContraHeader.

## [2026-05-06] — Fase 6.1: Perfeccionamiento de Gestión de Clientes ✅
- **UX**: Implementado atajo global `Ctrl + Espacio` para búsqueda inteligente y persistencia de foco.
- **UI**: Rediseño del listado de clientes con espaciado optimizado y buscador inteligente (Contratos, Clientes, Estados).
- **Contratos**: Mapeo granular de estados basado en `identities.json` con iconos FontAwesome y leyenda técnica integrada.
- **Robustez**: Implementada limpieza de datos (`trim`) en estados de contrato para evitar fallos de mapeo en importación y visualización.
- **Mejora Leyenda**: Refinado el diseño de la leyenda de estados de los ContraHeader para mayor integración estética.

## [2026-05-06] — Fase 5: Portal Principal (Dashboard) ✅

### Added
- **Dashboard Dinámico**: Implementación de métricas automáticas basadas en el estado real de los contratos (Activos, Urgentes, Próximos, Seguimiento).
- **Top 10 Vencimientos**: Tabla interactiva con badges de estado y cálculo de días restantes en tiempo real.
- **Cache Busting**: Sistema de versionado dinámico para `dx-styles.css` mediante `?v={{ time() }}` en el layout.
- **UX**: Sesión JWT extendida a 1 hora (60 min) para flujos de trabajo prolongados.

### Fixed
- **Persistencia de Tema**: Corregido fallo que reseteaba el modo oscuro al recargar o navegar. Ahora usa `localStorage` de forma consistente.
- **Flash de Tema**: Eliminado el parpadeo blanco al cargar la página en modo oscuro mediante script de inicialización síncrono.
- **Layout Simétrico**: Header y Footer ajustados con contenedores internos (`.header-inner`) para evitar dispersión en monitores panorámicos.
- **Labeling**: Generalizada la etiqueta de contratos a "Ecosistema Multi-Vendor" para mayor precisión.


## [2026-05-06] — Fase 4: Importación CSV & Modelado de Datos ✅

### Added
- **Motor de Importación**: Implementación de `CsvImportService` con detección inteligente de separador (`,`/`;`) y cabeceras. Ahora soporta 9 columnas incluyendo **Sub-Producto**.
- **Normalización de Datos**: Formateo automático de nombres de clientes (*Title Case*) y gestión de estado *Baja* para contratos ausentes.
- **Modelo de Datos**: Tablas `vendors`, `clients`, `contracts` e `import_logs` con migraciones incrementales. Añadido campo `sub_product` a la tabla de contratos.
- **UI Administrativa**: Vista `/admin/import` modernizada siguiendo `DESIGN.md`. Protocolo de mapeo balanceado visualmente (5/4).
- **Infraestructura**: Centralización de archivos `.env` mediante volúmenes de Docker y symlinks relativos para estabilidad del entorno.

### Fixed
- **Error de Ingesta**: Solucionado fallo que procesaba 0 registros debido a discrepancia en separadores de CSV.
- **Layout Dashboard**: Refactor de vistas administrativas para usar clases nativas de `dx-styles.css` y evitar solapamientos visuales.
- **UI Balance**: Ajustado el Protocolo de Mapeo de Datos para evitar asimetría tras añadir el campo C9.


## [2026-05-05] — Autenticación JWT y Verificación (Fase 3 ✅)

### Added
- **Servicio JWT**: Implementación de `JwtService` para generación y validación de tokens HS256.
- **AuthController**: Gestión de login/logout con cookies `HttpOnly` seguras.
- **Middleware RBAC**: `JwtAuth` y `CheckPermission` para control de acceso jerárquico (`admin`, `technician`, `staff`, `viewer`).
- **Vista de Login Premium**: Implementación de diseño *Full Background* con *Glassmorphism*.
- **Persistencia de Tema**: Integración de `localStorage` con Alpine.js para mantener el modo oscuro/claro.
- **Fondo Corporativo**: Nueva imagen y layout optimizado para pantallas ultra-panorámicas (Centrado 50/50).
- **Seguridad**: Implementado **Rate Limiting** (throttle:5,1) en la ruta de login.
- **Tests Automatizados**: Creado `AuthTest.php` con verificación de login, redirecciones y bloqueo de usuarios inactivos (PASS).

### Fixed
- **CSS Conflicts**: Eliminación de selectores heredados que causaban franjas blancas en el layout de login.
- **Ultra-Wide Layout**: Solucionado el problema de dispersión de elementos en monitores panorámicos mediante contenedor centralizado.
- **PHPUnit Config**: Activado SQLite en memoria para ejecución de tests segura.

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
- **Cleanup**: Eliminadas ramas locales y remotas ya integradas (`feature/fix-layout-css`, `feature/css-assets`, `feature/laravel-install`, etc.).

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
