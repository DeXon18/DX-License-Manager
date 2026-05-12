> Historial completo de cambios desde el inicio del proyecto.
> **Regla:** Nunca eliminar entradas. Las nuevas entradas van siempre al principio.


## [2026-05-12] — Fase 12: Repositorio de Licencias y Normalización ✅

### Added
- **Normalización de Almacenamiento**: Implementado `StorageNormalizationService` para estandarizar carpetas de clientes a **MAYÚSCULAS CON ESPACIOS**, eliminando puntos y comas (ej. `S.L.` -> `SL`).
- **Migración Automática**: Nuevo comando Artisan `system:migrate-storage-names` para renombrar retroactivamente todas las carpetas existentes en el servidor.
- **Repositorio Semanal**: Implementado motor de archivado en `LicenseRepositoryService` que agrupa licencias en archivos ZIP estructurados.
- **ZIP Personalizado**: Nomenclatura estándar `REPOSITORIO_SEMANAL_S[XX]_[YYYY].zip`.
- **Automatización**: Programación del sistema (Scheduler) para generar y enviar el repositorio los **lunes a las 07:00 AM**.
- **Notificaciones ATS**: Mailable `WeeklyLicenseReport` con resumen de Clientes/Sold-Tos y adjunto ZIP enviado a `Soporte@ats-global.com`.
- **Panel Administrativo**: Nueva interfaz en `/admin/repository` para visualización del historial, descarga de archivos y generación manual de repositorios.
- **Generación Pro**: Añadida opción de "Generar y Enviar" manual y trazabilidad de origen (`auto` vs `manual`) con iconos en la tabla.
- **Refactoring**: Actualizados los controladores de herramientas (NX, Star-CCM, Heeds, Moldex) para integrarse con la nueva lógica de normalización y corregida la inyección de dependencias en `NXSuiteController`.
- **Estabilidad**: Corregida lógica de creación de ZIPs con rutas absolutas y permisos forzados en el servidor.
- **UX**: Eliminación de textos redundantes en el panel administrativo para un diseño más limpio.

### Added
- **Inteligencia Artificial**: Integración de Gemini (Google AI) para el análisis de adaptadores de red en el generador de COD.
- **Asistente de Composite**: Nuevo servicio `CompositeParserService` que identifica el hardware óptimo (Ethernet físico > Wi-Fi) descartando adaptadores virtuales y VPNs.
- **UI Premium**: Modal interactivo con soporte para **Drag & Drop** de archivos `.txt` y pegado de texto.
- **Auto-rellenado**: Función de volcado automático de Hostname, Composite y MAC detectados directamente al formulario.
- **Seguridad**: Procesamiento asíncrono y protección de API Key en entorno Beta.
- **Correcciones**: Solucionado error 404 en la API de Gemini mediante la actualización al modelo **`gemini-3.1-flash-lite`** y versión `v1beta`.
- **UI/UX**: Rediseño integral de la zona de carga con estética premium, bordes punteados estilizados (**blue dashed**) y animaciones de pulso.
- **Iconografía Gemini**: Implementación del icono oficial **Sparkle** de Gemini en toda la plataforma (Herramientas y Dashboard).
- **Dashboard del Sistema**: Actualización integral de la matriz de servicios con iconos premium y colores de marca para **Gemini (Flash 3.1)**, **DeepSeek**, **OpenRouter**, **n8n**, **Telegram**, **MariaDB** y **Redis**.
- **Estética NOC Pro**: Unificación de dimensiones (34x34), centrado absoluto y sombras elevadas para una interfaz de monitorización de alta gama.
- **Dashboard NOC Pro**: Unificación visual de iconos (34x34px) y gradientes de marca para servicios de IA e infraestructura.
- **Módulo de Recursos (Fase 8.5/9.2)**: Implementación de sistema dinámico de gestión de enlaces y documentación con páginas independientes para Siemens y Moldex3D. Incluye panel de gestión reactivo (Alpine.js) para Staff/Admin.
- **Fix (UI)**: Corregida la especificidad de CSS en el Hub de Herramientas que provocaba que los nombres de utilidades Siemens se visualizaran con el color de Moldex.

## [2026-05-12] — Restauración Configuración n8n (Fix) ✅
### Fixed
- **Configuración**: Restauradas variables críticas de n8n en `infra/.env.beta` que fueron eliminadas accidentalmente.
- **Conectividad**: Verificada salud del motor n8n desde el servidor Beta (Online).
- **Callback**: Restaurada `AUDIT_CALLBACK_URL` para permitir el retorno de datos desde la IA.

## [2026-05-12] — Gestión de Backups (Fase 14) ✅

### Added
- **Modularización**: Traslado de lógica de backups de `SystemActionController` a `BackupController`.
- **Restauración**: Implementación de sistema de restauración de DB mediante SQL pipe con validación de rutas.
- **Seguridad UI**: Modal de doble confirmación con keyword "RESTAURAR" para acciones destructivas.
- **Automatización**: Configuración de Cron Job diario (03:00 AM) en servidor LXC 600.
- **Git Cleanup**: Merge de funcionalidades previas y borrado de 12 ramas obsoletas.

## [2026-05-12] — Gestión de Usuarios y Acceso (Fase 11) ✅

### Added
- **User Toggle AJAX**: Implementada acción inmediata para activar/desactivar usuarios desde el listado sin recarga de página.
- **RBAC Assignments**: Sistema de asignación de roles verificado y funcional en creación/edición.
- **Delete Protection**: Blindaje contra auto-eliminación y auto-desactivación del administrador en sesión.

## [2026-05-11] — Restauración Infraestructura y SMTP ✅

### Fixed
- **Base de Datos**: Restauradas credenciales de MariaDB Beta (`dxportal_beta`).
- **SMTP Production**: Configurado Mailtrap en modo Producción con autenticación via API Token.
- **Docker Sync**: Resuelto problema de sincronización de archivos `.env` en contenedores Docker (inode cache).
- **Notificaciones**: Verificado envío de emails reales desde el backend.

## [2026-05-11] — Gestión de Usuarios y RBAC (Fase 11 y 11.1) ✅

### Added
- **User Management CRUD**: Sistema completo de administración de usuarios con filtrado por roles y búsqueda.
- **RBAC Engine**: Implementación de roles dinámicos (admin, technician, staff, viewer) con middleware de permisos granular.
- **My Profile Section**: Nueva sección de autogestión para que los usuarios actualicen sus datos y cambien su contraseña.
- **Automated Notifications**: Sistema de bienvenida por email con envío automático de credenciales iniciales.
- **Native Design Migration**: Migración total a `dx-styles.css` eliminando dependencias externas (Bootstrap) en las vistas administrativas.

### Fixed
- **Database Cleanup**: Eliminación de registros de prueba (faker) y normalización de usuarios base del sistema.
- **Security Hardening**: Bloqueo de auto-desactivación y auto-eliminación para sesiones administrativas activas.

## [2026-05-11] — Estabilización de NOC Pro e Infraestructura ✅

### Added
- **Git Localization**: Localización completa de fechas relativas al castellano ("hace X segundos") mediante integración de Carbon y timestamps de Git.
- **Representative UI Colors**: Implementación de código de colores semánticos en el Dashboard (Azul para Caché, Ámbar para Backups, Naranja para Alertas, Verde para Despliegues OK).
- **UX Quick Actions**: Alineación ergonómica a la izquierda y micro-interacciones de desplazamiento lateral en el panel de acciones administrativas.

### Fixed
- **Git Multi-User Permissions**: Configuración de `safe.directory` a nivel de sistema (`--system`) en el contenedor para permitir que el servidor web (`www-data`) extraiga metadatos del repositorio.
- **App Localization Loop**: Sincronización de `APP_LOCALE=es` en configuración global y entornos Docker para garantizar consistencia en todas las respuestas del sistema.
- **NOC Alignment**: Corregida alineación del NOC para lectura natural (de derecha a izquierda) y normalización visual de fuentes mono-espaciadas.

## [2026-05-11] — Modularización Administrativa (Phase 10.4) ✅

### Added
- **Database Vault Module**: Migración de la gestión de backups a un controlador y vista independientes (`BackupController`).
- **Centro de Auditoría**: Nuevo módulo dedicado para logs de sistema con filtros avanzados (IP, Usuario, Acción, Nivel).
- **Header Standardization**: Unificación visual de las cabeceras de administración siguiendo el estilo del módulo de Importación.
- **Backup Download/Delete**: Implementada lógica de borrado y descarga de copias de seguridad mediante IDs de archivo seguros.
- **Relocation of Stats**: Las estadísticas técnicas se han movido a las cabeceras de las tarjetas internas para limpiar el encabezado principal.

### Fixed
- **Backup Execution Environment**: Instalado `mariadb-client` en el contenedor PHP y actualizado el script para usar `mariadb-dump` con `--ssl=0`, solucionando errores de conexión y comandos faltantes.
- **Git Metrics in Dashboard**: Corregido error `N/A` mediante el montaje del directorio `.git` y la configuración de `safe.directory` en el contenedor, permitiendo la visualización del hash y fecha de despliegue.
- **Permission Denied in Backups**: Actualizado script `backup-db.sh` para forzar permisos 777 en archivos nuevos, permitiendo su borrado desde la interfaz web.
- **Path Synchronization**: Corregidas las rutas de almacenamiento de backups para sincronizar el volumen de Docker con el `storage_path()` de Laravel.
- **Styling Consistency**: Corregido estilo de botones "Limpiar" y alineación de botones de acción en tablas.

## [2026-05-11] — Dashboard del Sistema (NOC Pro) ✅

### Added
- **NOC Pro Control Center**: Evolución del dashboard a centro de operaciones profesional.
- **Quick Actions Panel**: Implementado panel interactivo con Alpine.js para acciones administrativas (Limpiar caché, Backups, Mantenimiento, Reinicio de Workers).
- **Telemetría de Red**: Visualización en tiempo real de tráfico ETH0 (RX/TX bytes) directo desde kernel.
- **Métricas DB Profundas**: Monitorización de hilos conectados y consultas lentas (slow queries) en MariaDB.
- **Git Intelligence**: Integración de Hash de commit y fecha relativa del último despliegue en la cabecera.
- **System Live Feed**: Registro visual de los últimos 10 eventos de auditoría administrativa.
- **Maintenance Pulse**: Indicador visual dinámico en cabecera cuando el modo mantenimiento está activo.
- **Seguridad**: Registro automático de toda acción administrativa en `audit_log` con trazabilidad de usuario.
- **Selective Maintenance Mode (Admin Friendly)**: Implementado sistema de mantenimiento que permite a los administradores seguir operando mientras el público visualiza una página 503 personalizada.
- **Top Warning Banner**: Aviso persistente en el layout para administradores cuando el mantenimiento está activo.
- **Custom 503 Page**: Nueva vista de mantenimiento con diseño premium alineado con la identidad del proyecto.
- **Helper formatBytes**: Añadida utilidad para formateo dinámico de unidades de datos.
- **Operator Control Center**: Implementado nuevo Dashboard de alta densidad con métricas técnicas y de seguridad (NOC style).
- **Refinamiento Visual NOC**: Integración de fuente `Outfit` para valores master, centrado de KPIs y estilo "Ghost Icons" rotados para look premium.
- **Métricas de Infraestructura**: Monitorización de `Load Avg` (1m, 5m, 15m), RAM (vía `cgroups` para LXC) y almacenamiento.
- **Matriz de Servicios**: Monitorización de DB, Redis, n8n y proveedores de IA (Gemini, DeepSeek, OpenRouter).
- **Seguridad**: Monitorización de sesiones activas, `JWT Blacklist` y errores críticos (24h).
- **Visualización de Datos**: Integración de Chart.js para gráficas de tendencia (Auditorías 7 días) y distribución de Daemons.
- **KPIs de Negocio**: Visualización en tiempo real de contratos, licencias activas, caducidades próximas y estado de auditorías IA.
- **Factories de Datos**: Creadas factories para `Client`, `Contract` y `AiAuditResult` para soporte de tests de integración.
- **Services Matrix Categorizada**: Organización de servicios en grupos lógicos (Infraestructura, Procesadores, Inteligencia AI).
- **Iconografía y Localización**: Añadidos iconos SVG personalizados y etiquetas en castellano natural para mayor claridad operativa.
- **Seguimiento en Tiempo Real**: Implementado sistema de presencia basado en Redis para contar usuarios activos (JWT) con ventana de 15 minutos.
- **Métricas de Actividad AI**: Implementado contador de auditorías diarias para Gemini como proxy de consumo.

### Fixed
- **Métricas de RAM en LXC**: Corregida detección de límites de memoria usando `cgroup v1/v2` para reportar el límite del contenedor en lugar de la RAM del nodo Proxmox.
- **CPU Load Formatting**: Estructurada la salida de `sys_getloadavg` para evitar solapamientos visuales y permitir acceso directo a intervalos.
- **Protocolo de Seguridad:** Implementada la sección 0.9 en `AGENTS.md` obligando a realizar backups antes de cambios estructurales o tests en el servidor.
- **Base de Datos:** Corregido error 500 en Dashboard debido a nombre de columna incorrecto (`daemon` vs `vendor_daemon`) en la tabla de inventario.
- **Robustez Infra**: Corregido comando `uptime` para compatibilidad con BusyBox/Alpine en el contenedor PHP y timeouts de API de Telegram (5s).

---

## [2026-05-10] — Phase 10: Gestión de Usuarios (Auth)

### Added
- **CRUD Contactos**: Sistema de gestión de destinatarios vinculados a clientes con modales Alpine.js.
- **Persistencia**: Sistema de pestañas en perfil de cliente que mantiene el estado tras recargar.
- **Demo Data**: `DemoContactSeeder` para poblar el sistema con datos de prueba.

---

## [2026-05-09] — Paridad de Temas y Refinamiento de UX Final

### Added
- **Paridad de Temas (Light/Dark)**: Refactorización total de `moldex3d.blade.php` y `clients/show.blade.php` para eliminar colores HEX hardcodeados, asegurando que todos los componentes visuales (Property List, Bento Grid, Inventory Cards, History Toggle) se adapten automáticamente al tema del sistema mediante variables CSS (`--primary`, `--muted`, `--surface`, etc.).
- **Robustez en Herramientas**: Simplificación de la lógica de enlaces en el Hub de Herramientas para garantizar el acceso correcto a la auditoría de Moldex3D.
- **Identidad de Marca**: Migración de colores estáticos de Moldex3D a la variable `--moldex` para consistencia cross-module.

### Fixed
- **Contraste de UI**: Corregida la visibilidad de textos secundarios y fondos de tarjetas en modo claro.
- **Visibilidad Crítica**: Reparadas las etiquetas de "Expiración" y "Versión" (v2026) que eran invisibles en modo claro por estar hardcodeadas en blanco o colores de bajo contraste.
- **Spinner & Dropzone**: Adaptación visual de los estados de carga y arrastre de archivos al sistema de diseño global.
- **Layout de Cliente**: Corregida etiqueta `<template>` sin cerrar en `clients/show.blade.php` que bloqueaba el renderizado del footer global.

---

## [2026-05-09] — Diferenciación de Vendors en UI (Siemens vs Moldex3D)

### Added
- **Modelo de Datos**: Implementado accessor `vendor` en `LicenseInventoryDaemon` para identificación estructural de proveedores.
- **UI Adaptativa**: 
  - Rediseño de labels en inventario: "Daemon" para Siemens y "Plataforma" para Moldex3D.
  - **Logo Moldex3D**: Implementación de logo estilizado con colores de marca (Rojo/Naranja) para mayor identidad visual.
  - **Limpieza de UI**: Eliminación de badges redundantes para Moldex3D y diferenciación de etiquetas de cuenta (**Sold-To Account** para Siemens vs **Customer ID** para Moldex3D).
  - **Versión Prominente**: Mejora de la visibilidad de la versión (v2025) y unificación de etiquetas de red (**Servidor / Hostname** + **Machine ID** para Moldex3D).
  - Resaltado de hardware: Label específico "Machine ID" para licencias de Moldex3D.
- **Sistema de Estilos**: 
  - Nuevas clases CSS `.moldex-logo` y `.accent` para representación tipográfica de marca.
  - Dinamismo de colores de vendor en tarjetas de inventario.

### Fixed
- **Rutas**: Restaurado el acceso a la herramienta de Moldex3D (`/herramientas/moldex3d`) mediante la integración de las ramas de desarrollo pendientes. Corregido error 404.
- **Robustez**: Eliminada la dependencia de `str_contains` en las vistas, delegando la lógica de identificación al modelo.

---

## [2026-05-09] — Fase 9: Auditoría Moldex3D y Persistencia ✅

### Added
- **Parser Moldex3D**: Implementado parser determinista (regex) para archivos `.mac`.
- **Persistencia en Inventario**: Nuevo `MoldexSyncService` que vincula automáticamente las licencias con clientes existentes en la base de datos.
- **Registro de Productos**: Sincronización de módulos, cantidades y fechas de expiración en `license_inventory_products`.
- **UI/UX Premium**: 
  - Vista "Property List" estilo dark/técnico para resultados de auditoría.
  - Dropzone rediseñado con alineación óptica corregida.
  - Indicadores visuales de estado de sincronización en tiempo real.
- **Nomenclatura**: Estandarización de archivos basada en `AÑO_ID_CLIENTE__TIPO_FECHA.mac`.
- **Seguridad**: Almacenamiento privado estructurado y proceso local 100% determinista.

### Fixed
- **Alineación Dropzone**: Corregida desviación de iconos mediante `inline-flex` y contenedor de bloque.

---

## [2026-05-08] — Generador Siemens COD (Completo)

### Added
- **Generador de Certificados de Cese (COD)**: Motor de generación de PDF de alta fidelidad bilingüe.
- **Gestión de Firmas**: Implementada subida y descarga segura de CODs firmados por el cliente.
- **Borrado Inteligente**: Nueva opción de borrado completo (Registro BD + Archivos físicos Original/Firmado).
- **Vista Previa Interactiva**: Modal con visor de PDF integrado y limpieza de barras de herramientas.
- **Seguridad ID-Abstraction**: Flujo de descarga blindado mediante UUIDs para certificados.
- **Validación Estricta**: Sanitización de inputs (Hostnames sin tildes, MACs sin guiones, Solicitantes sin números).
- **Asistente de Hardware**: (Idea registrada en Backlog) para futuras versiones.

### Fixed
- **Enlaces Históricos**: Corregido error 404 en el historial de cliente mediante migración a sistema de UUIDs.
- **Alineación de Iconos**: Ajuste de UI mediante `display: contents` para visualización perfecta en horizontal.
- **Mapeo de Almacenamiento**: Corregida visibilidad en el host (Windows) alineando el disco `private` con los volúmenes de Docker.
- **Localización**: Mapeo de tipos de certificado a nombres profesionales en castellano.

- **Optimización Visual**: Iconos de acción compactados en horizontal (26px) para mejorar la densidad de información.
- **Alineación UI**: Unificación de layouts horizontales para iconos y títulos en todo el módulo.
- **Optimización PDF**: Compresión de márgenes y fuentes para asegurar una sola página A4.
- **Bug Fix**: Corregido error de variable indefinida en la persistencia del certificado.
- **Infraestructura**: Creación de directorio de fuentes y reseteo de logs.

---
_Firmado por: **Antigravity (DX Agent)** 🦾_

## [2026-05-08] — Fase 8.4: Generación de Certificados de Cese (COD) ✅

### Added
- **COD Generator**: Implementado generador bilingüe (ES/EN) de certificados COD oficial de Siemens.
- **Dompdf**: Instalación y configuración de `barryvdh/laravel-dompdf` para generación de documentos de alta fidelidad.
- **Fuentes Corporativas**: Integración de fuentes Calibri TTF para cumplimiento de estándares visuales de Siemens.
- **Seguridad**: Nuevo disco de almacenamiento `private` para CODs, garantizando que los archivos no sean accesibles públicamente.
- **UI/UX**: 
  - Nueva herramienta "Generador COD" en el Hub con soporte para múltiples MACs y previsualización dinámica.
  - Integración del historial de certificados en la ficha de cliente (`Certificados` tab).
- **Base de Datos**: Nueva tabla `cod_certificates` para trazabilidad completa y gestión de estados (Pendiente/Firmado).

## [2026-05-08] — Fase 8.3: Motor HEEDS y Normalización Cross-Module ✅

### Added
- **HEEDS**: Implementado motor completo de auditoría y transformación para licencias HEEDS (`rctech` -> `saltd`).
- **Parser**: Nuevo `HeedsService` con extracción avanzada de metadatos desde el bloque de cabecera de Siemens (Sold-To, Cliente, Versión).
- **UI**: Vista dedicada `tools/heeds.blade.php` con bento técnico y soporte para auditoría IA.
- **Normalización**: Implementada la Bandeja de Normalización Central para gestión de identidades y duplicados.
- **Motor**: Nuevo `ClientNormalizationService` con soporte para **Fuzzy Matching** (85%) y gestión de Alias.
- **Integración**: Sistema de normalización cruzada que captura avisos tanto de CSV como de Auditoría de Licencias (AI).
- **STAR-CCM+**: Implementado `StarCcmService` para parsing y transformación de licencias `cdlmd` a `saltd`.
- **UI**: Nuevo dashboard técnico en `tools/star-ccm.blade.php` y bandeja de normalización premium.
- **Base de Datos**: Tablas `client_aliases`, `normalization_decisions` y columna `warnings` en logs y auditorías.

### Refined (UI/UX - Phase 8.3 Final)
- **Engine Selector**: Integrado selector rápido de motores (NX Suite, STAR-CCM+, HEEDS) en la barra lateral de todas las herramientas.
- **Unificación Estética**: Centralizado el diseño de tarjetas (radius 4px) y cuadrículas (gap 24px, sidebar 300px) en `dx-styles.css`, eliminando más de 200 líneas de estilos locales redundantes.
- **Layout Stability**: Implementado `overflow-y: scroll` global para prevenir saltos de píxeles al cambiar entre páginas con y sin scroll.
- **Sidebar Fix**: Corregido error de anidamiento de etiquetas `<a>` en el layout principal que causaba desplazamientos en el bloque de contenido.

### Changed
- **Arquitectura**: Refactorizado `NormalizationController` para centralizar la lógica de limpieza de datos.
- **Configuración**: Centralizadas las URLs de Webhooks y Callbacks de IA en variables de entorno (eliminando hardcoded URLs).
- **Fase STAR-CCM+**: Nomenclatura estricta de archivos `.lic` y almacenamiento jerárquico por cliente/fecha.

### Fixed
- **Unificación**: Lógica de migración total que mueve contratos, licencias, inventario y contactos al unificar clientes.
- **Regex**: Corregidos los patrones de extracción de nombres en los logs de importación.

### Status
- **Pausa Técnica**: Se detienen temporalmente las tareas de n8n v2.2 y fix del modal para priorizar la estabilidad de la UI de clientes y el motor de normalización. ⏸️

---

## [2026-05-07] — Optimización de Auditoría IA (v2.2) ⏳ (Pendiente Verificar)

### Added
- **n8n Workflow v2.2**: Implementado nuevo prompt de IA con soporte explícito para:
  - **Hardware Keys (Dongles)**: Detección de `UG_HWKEY_ID` e IDs numéricos cortos.
  - **Modo Standalone**: Gestión de licencias sin servidor central.
  - **IDs Numéricos**: Soporte para Host IDs no hexadecimales (ej: 24141).
- **Backend Sincronización**: Actualizado `InventorySyncService` para reconocer automáticamente IDs numéricos cortos como licencias tipo `dongle`.

### Fixed
- **Precisión de Inventario**: Mejorada la detección de tipo de licencia basada en el formato del Host ID del producto.

## [2026-05-07] — Refinamiento del Inventario Activo (Fase 8.1 Finalizada) ✅

### Added
- **UI de Inventario Robusta**: Rediseño completo de la interfaz de inventario utilizando CSS puro de alta densidad técnica.
  - Layout horizontal optimizado para lectura rápida de daemons y productos.
  - Soporte nativo para visualización de múltiples **Sold-To** bajo un mismo cliente (Ecosistema Siemens).
  - Identificación visual clara de licencias **Node-Locked** (MAC) y **Hardware Keys** (Dongles).
- **Consistencia Visual**: Restauración de estilos globales (menú de pestañas, leyenda de estados, modales de auditoría) para asegurar la integridad de toda la vista de cliente.
- **Tipografía Corporativa**: Integración de Google Fonts (Inter e IBM Plex Mono) para mejorar la legibilidad de datos técnicos.

### Fixed
- **Layout Bento**: Eliminadas dependencias de Tailwind que causaban fallos de renderizado en monitores panorámicos.
- **Estabilidad CSS**: Aislados los estilos de inventario en bloques robustos, evitando colisiones con el diseño global del portal.

## [2026-05-07] — Motor de Auditoría Siemens (Fase 8.1 Parte 2) ✅

### Added
- **Base de Datos**: Implementadas tablas `ai_audit_results` y `client_mappings`.
- **Servicios de Backend**:
  - `LicenseParserService`: Parser de limpieza para archivos FlexLM (unificación de líneas y filtrado de firmas).
  - `AuditService`: Orquestador de comunicación con n8n y lógica de auto-vinculación de clientes.
- **Integración IA**: 
  - Conexión operativa con el webhook de n8n para procesamiento asíncrono.
  - Implementado `AuditCallbackController` para recepción de resultados estructurados.
  - Integración en el flujo de subida de `NXSuiteController`.
- **UI de Auditoría (Beta)**:
  - Nueva pestaña "Licencias" en el perfil de cliente con historial de auditorías.
  - Visualización de productos detectados mediante chips dinámicos.
  - **Pendiente**: Refinar la apertura del modal de detalle (investigar fallo Alpine.js tras teleport).

## [2026-05-07] — Mecanismo Siemens NX (Fase 8.1 Parte 1) ✅

### Added
- **Nomenclatura Estricta**: Nueva lógica de generación de nombres para Siemens NX.
  - Formato: `SOLDTO_HOSTNAME_CLIENTE_VERSION_Valida_DDMMYYYY.lic`.
  - Normalización: Hostname y Cliente siempre en **MAYÚSCULAS** y sin caracteres especiales (puntos/espacios).
- **Almacenamiento Jerárquico**: Las licencias se organizan por `siemens/{cliente}/{mes-año}/`.
- **Gestión de Duplicados**: Implementado sufijo numérico automático (`_1`, `_2`) para evitar sobrescrituras.
- **UI de NX Suite**: Rediseño visual semántico, utilizando tarjetas diferenciadas con colores de vendor (Rojo Legacy / Teal SALT) y estructura de paneles laterales al estilo `admin/import`.

### Fixed
- **Error 413 (Payload Too Large)**: Resuelto. Se corrigió la ruta de `env_file` en `docker-compose.beta.yml` a `./infra/.env.beta` lo que permitió montar correctamente el archivo `local.ini` (100MB) en PHP-FPM.
- **Permisos de Almacenamiento**: Corregido bloqueo de I/O en la carpeta `storage` y `bootstrap/cache` mediante ajuste de permisos 777.

## [2026-05-07] — Gestión de Memoria y Reglas de Control

### Added
- **Skills**: Integrada la habilidad `claude-mem` para persistencia semántica entre sesiones.
- **Git/GitHub**: Implementada regla innegociable de Puntos de Control (Tags) tras cada fase terminada.
- **Cleanup**: Realizada limpieza masiva de ramas locales y remotas ya integradas.

## [2026-05-07] — Rediseño de Inventario y Gestión Multi-Vendor

### Added
- [PLAN] Iniciado rediseño completo de la gestión de licencias hacia un modelo de "Inventario Activo".
- Definición de nuevas tablas `license_inventory_daemons` y `license_inventory_products` para soportar multi-Sold-To y Node-Locked (MACs).
- Soporte para licencias de tipo Dongle USB (HW-KEY).

### Fixed
- Estandarización de etiquetas (### Added, ### Fixed, ### Changed) en el historial de sesiones.

## [2026-05-06] — Sincronización y Lecciones (Fase 8.1)

### Changed
- **Sincronización**: Restaurada la rama `dev` tras un fallo arquitectónico en el inicio de la Fase 8.1.
- **Lección Aprendida (UI)**: Uso estricto de `dx-styles.css` sin introducir Tailwind CSS no autorizado.
- **Lección Aprendida (Rutas)**: Respetar la convención de rutas en castellano (`/herramientas`) y no sobreescribir lógica validada en fases anteriores.

## [2026-05-06] — Fase 7: Hub de Herramientas ✅

### Added
- **Hub de Utilidades**: Implementación de vista dinámica `/herramientas` agrupada por Vendor.
- **Feature Flags**: Modelo `FeatureFlag` y seeder sincronizado con `identities.json` para control de accesos.
- **Navegación**: Vinculación de Sidebar y Header con el nuevo Hub centralizado.

### Changed
- **Sincronización**: Llaves y etiquetas técnicas actualizadas para coincidir estrictamente con el archivo de identidades.
- **Copy**: Actualización de frases descriptivas para Siemens y Moldex3D según estándares técnicos.

## [2026-05-06] — Fase 6.3: Gestión de Contactos ✅

### Added
- **CRUD Contactos**: Sistema de gestión de destinatarios vinculados a clientes con modales Alpine.js.
- **Persistencia**: Sistema de pestañas en perfil de cliente que mantiene el estado tras recargar.
- **Demo Data**: `DemoContactSeeder` para poblar el sistema con datos de prueba.

### Fixed
- **Infraestructura**: Limpieza de `known_hosts` y corrección de acceso SSH para despliegues automatizados.

## [2026-05-06] — Refinamiento UI Clientes

### Changed
- **Leyenda de Estados**: Integrada en la Card de contratos (Fase 6.1).
- **Estilo**: Alineado con `DESIGN.md` (jerarquía técnica y card-footer).
- **Mejora**: Refinamiento estético de la leyenda de estados en el ContraHeader.

## [2026-05-06] — Fase 6.1: Perfeccionamiento de Gestión de Clientes ✅

### Added
- **Búsqueda Pro**: Atajo global `Ctrl + Espacio` y buscador inteligente optimizado para grandes volúmenes de datos.
- **Leyenda Técnica**: Guía visual de estados integrada en el ContraHeader para referencia rápida.

### Changed
- **UX/UI**: Rediseño simétrico del listado de clientes y normalización de datos (`trim`) para evitar desajustes en el mapeo de estados.

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
