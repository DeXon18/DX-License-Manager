# BACKLOG — DX License Manager

> Gestión de tareas del proyecto. Las tareas completadas se mueven a la sección correspondiente pero **nunca se eliminan**.
> **Regla:** Mover, no borrar.

---

## ⛔ Regla de Validación

**Ninguna fase puede iniciarse sin validación explícita de Oskar.**
El agente no avanza hasta recibir "aprobado", "adelante" o similar de forma explícita.

**Lo que NO es confirmación válida — nunca ejecutar por:**
- Creación de un artefacto o archivo
- Mensaje del sistema o del IDE
- Silencio o ausencia de respuesta
- El propio agente diciendo "Aprobación recibida"
- Cualquier señal que no sea texto explícito de Oskar
**Lo que SÍ es confirmación válida:**
- Oskar escribe: "adelante", "ok", "sí", "procede", "empieza", "dale", "go"
**Después del plan → NO preguntar "¿Empiezo?". Presentar y CALLAR.**
El desarrollador inicia. El agente espera.
 
⛔ "Aprobación recibida. Empiezo ejecución." → FRASE PROHIBIDA. Nunca escribirla.

---


## 🟢 En Progreso

*Actualmente en espera de la definición de nuevos objetivos y prioridades por parte de Oskar.*

## ✅ Completado

### Fase 23.7 — Rediseño de Historial y Detalle de Auditorías Premium (NOC Pro) ✅
- **Completada:** 2026-05-20
- **Rama:** `feature/audit-details-ui`
- **Resumen:** Rediseño completo de la interfaz de auditorías inmutables en la ficha del cliente (`clients/show.blade.php`). Reemplazado el `<details>` rústico nativo por un acordeón interactivo y animado con Alpine.js (`historyOpen`) con banners explicativos de "Fuente de Verdad Histórica". Rediseñado el modal de detalle de auditorías con un Bento Grid para metadatos del servidor, una consola inmutable de alta densidad de productos, y remoción de acciones inactivas que causaban confusión al usuario.
- **PR:** Pendiente — merged a dev

### Fase 23.6 — Normalización Tabs, Filtro de Descriptores Léxicos, Caché & Modal Teatral ✅
- **Completada:** 2026-05-20
- **Rama:** `feature/manual-normalization`
- **Resumen:** Restauración del diseño de 3 pestañas dinámicas con persistencia Alpine.js (`localStorage`). Optimización del motor léxico para omitir descriptores corporativos de prefijo, eliminando falsos positivos. Implementada transliteración ASCII (`iconv`) para evitar que acentos rompan los tokens léxicos (ej: *Codesal* vs *Oregi*). Cacheado del escáner e invalidación automática. Centrado geométrico absoluto del modal de escaneo en la UI y remoción de retardo simulado en Alpine.js.
- **PR:** #024 — merged a dev

### Fase 23 — Normalización de Identidades con IA & Unificación Forzada ✅
- **Completada:** 2026-05-20
- **Rama:** `feature/ai-normalization-force`
- **Resumen:** Implementación completa del motor de normalización inteligente por IA (Gemini -> DeepSeek -> OpenRouter con fallback chain automático) y desarrollo de la unificación manual forzada mediante un buscador predictivo `<datalist>` HTML5 que migra de forma atómica contratos, licencias, deamonios, contactos y auditorías al cliente real, asocia el alias y destruye el duplicado.
- **PR:** #023 — merged a dev

### Resolución de Incidencia #020 y #017 — Sistema de Toasts & Estilos en Búsqueda de Usuarios ✅
- **Completada:** 2026-05-20
- **Rama:** `fix/clientes-search-style`
- **Resumen:** Implementación de un motor reactivo de notificaciones premium (Toasts) con Alpine.js y glassmorphism adaptativo claro/oscuro en `dx-v2-toast.css`. Reemplazadas todas las alertas inline obsoletas en las 7 vistas principales del portal. Corregida la barra de búsqueda y selector de roles sin estilos en Gestión de Usuarios.
- **PR:** Pendiente — merged a dev

### Fase 19 y 21 — Modularización CSS & Limpieza UI (DX-V2) ✅
- **Iniciada:** 2026-05-16
- **Completada:** 2026-05-19
- **Rama:** `feature/css-tokens`
- **Resumen:** Refactorización CSS ultra-granular y modularización del monolito legacy de 10k líneas en 35 hojas estructuradas en 6 capas jerárquicas y unificadas en `dx-v2-main.css`. Erradicación de `style=` inline, inyección de namespaces `.dx-v2-`, variables HSL y soporte completo light/dark mode.
- [x] Subfase 19.4 (Dashboard Centralization)
- [x] Subfase 19.5 (Clientes Listado)
- [x] Subfase 19.6 (Clientes Licencias)
- [x] Subfase 19.7 (Clientes Contratos / Importación CSV)
- [x] Subfase 19.8 (Clientes Contactos / Certificados COD)
- [x] #017 (P3): Unificación CSS — Subfases 19.1 a 19.29 (Ejecución & Hardening).
- [x] Fase 21: Modularización CSS en 35 archivos e invalidación robusta de caché en login.

### Resolución de Incidencia #016 — Fix COD File Deletion ✅
- **Completada:** 2026-05-16
- **Rama:** fix/cod-delete-file-fail
- **Resumen:** Corregido el fallo por el cual los archivos PDF de los CODs permanecían en el disco tras ser borrados de la UI. Se implementó una normalización agresiva de rutas y se añadieron logs de telemetría en el borrado físico.
- [x] Fix: Normalización de espacios y caracteres en `CodService`.
- [x] Refuerzo: Logs de éxito/fallo y borrado atómico en `CodController`.
- [x] Telemetría: Registro detallado de rutas en `laravel.log`.

### Resolución de Incidencia #015 — Fix Preview COD & UI Optimization ✅
- **Completada:** 2026-05-16
- **Rama:** fix/cod-preview-fail
- **Resumen:** Reparado el fallo de anidamiento HTML que impedía abrir la vista previa de CODs. Se reubicó el asistente IA de Composite a una posición contextual en "Nueva Máquina" y se refactorizó la lógica de almacenamiento para usar nombres reales en MAYÚSCULAS.
- [x] Fix: Eliminado bloque HTML duplicado e incompleto en `cod.blade.php`.
- [x] UI: Reubicado botón "Analizar Composite.txt" a cabecera de sección.
- [x] Storage: Refactor de `CodService` para carpetas en MAYÚSCULAS.
- [x] Limpieza: Eliminada carpeta residual `storage/private`.


### Resolución de Incidencia #004 — UI Multi-Sold-To (NOC Pro v2) ✅
- **Completada:** 2026-05-15
- **Rama:** feature/multi-soldto-ui
- **Resumen:** Rediseño estético de alta fidelidad para licencias unificadas. Se implementó una marca de agua técnica sutil (Watermark) y una franja minimalista para Sold-Tos adicionales con resaltado en amarillo claro (#fde68a), eliminando el ruido visual en el header del daemon.
- [x] UI: Implementada marca de agua `fa-network-wired` (Opacidad 0.04).
- [x] UI: Nueva franja técnica transparente con IDs en amarillo suave.
- [x] Limpieza: Eliminado badge "UNIFICADA" y popover experimental para un look más industrial.

### Resolución de Incidencia #013 — Sincronización Moldex3D ✅
- **Completada:** 2026-05-15
- **Rama:** feature/fix-client-license-filter
- **Resumen:** Solucionada la invisibilidad de licencias Moldex3D integrando `ClientNormalizationService` para evitar bloqueos por nombres distintos y manejando correctamente los errores de sincronización en `MoldexController`.
- **PR:** #013 — merged a dev


### Resolución de Incidencia #003 — Filtros Clientes (Siemens/Moldex) ✅
- **Completada:** 2026-05-15
- **Rama:** chore/error-tracking
- **Resumen:** Implementación de filtros dinámicos en inventario para separar licencias Siemens y Moldex3D, permitiendo un conteo preciso por tecnología.
- [x] Backend: Lógica de filtrado en el repositorio de licencias.
- [x] UI: Switch de selección en la vista de inventario.
- [x] Verificación: Conteo correcto en entornos de prueba.

### Resolución de Incidencia #005 — Lector de Logs Profesional ✅
- **Completada:** 2026-05-15
- **Rama:** fix/system-log-reader
- **Resumen:** Transformación del lector de logs de sistema de texto plano a una herramienta de diagnóstico profesional. Incluye parser Regex para estructurar `laravel.log`, UI interactiva con Alpine.js (trazas colapsables) y sincronización de telemetría de alertas (DB + Fichero).
- [x] Backend: Implementado parser estructurado con niveles de severidad.
- [x] UI: Rediseño con Alpine.js y resaltado de código propio vs vendor.
- [x] Telemetría: Sincronización del contador de "Alertas" en tiempo real.
- [x] Robustez: Blindaje contra tablas inexistentes en el módulo de auditoría.

### Resolución de Incidencia #012 — Hotfix Persistencia Redis ✅
- **Completada:** 2026-05-15
- **Rama:** fix/redis-persistence-error
- **Resumen:** Resolución de error crítico `MISCONF` en Redis. Se restauró la persistencia RDB mediante corrección de permisos en caliente y se securizó la infraestructura con volúmenes nombrados en Docker Compose.
- [x] Diagnóstico: Identificado conflicto de permisos (`root` vs `redis`) en `/data`.
- [x] Hotfix: Aplicado `chown redis:redis` en el contenedor Beta.
- [x] Infraestructura: Implementados volúmenes nombrados en `docker-compose.beta.yml` y `docker-compose.prod.yml`.
- [x] Verificación: Confirmado `BGSAVE` exitoso y restauración de políticas de escritura.

### Resolución de Incidencia #011 — Estabilización Global de Herramientas ✅
- **Completada:** 2026-05-15
- **Rama:** fix/nx-ui-validation
- **Resumen:** Resolución integral de fallos en el pipeline de licencias Siemens y Moldex3D. Se ha implementado validación de extensiones en el cliente (Alpine.js), blindaje de memoria (256MB) y gestión de errores avanzada (try-catch) con degradación elegante para asegurar la descarga de archivos incluso ante fallos de servicios secundarios.
- [x] UI: Validación Alpine.js en NX, StarCCM+, HEEDS y Moldex3D con mensajes de error temporales.
- [x] Soporte: Ampliación de extensiones permitidas a `.dat` y `.cid` en todas las herramientas Siemens.
- [x] Backend: Implementación de `ini_set('memory_limit', '256M')` y `try-catch` global en controladores.
- [x] Robustez: Garantizada la descarga del archivo transformado aunque falle la auditoría IA o el almacenamiento.
- [x] Parser: Optimización del `LicenseParserService` para procesar archivos grandes línea a línea.
- [x] Regex: Soporte para múltiples daemons Siemens (`saltd`, `cdlmd`, `RCTECH`) en la extracción de metadatos.
 
### Soporte Multi-Sold-To (Licencias Unificadas) ✅
- **Completada:** 2026-05-14
- **Rama:** feature/multi-sold-to
- **Resumen:** Implementada la capacidad de procesar licencias Siemens que contienen múltiples IDs de cliente (Other Installs). El sistema ahora extrae todos los Sold-Tos mediante IA (n8n v2.1), crea mapeos automáticos y los visualiza en el inventario activo.
- [x] n8n: Actualización del flujo de auditoría IA v2.1 para detección de IDs unificados.
- [x] DB: Migración para añadir `additional_sold_tos` (JSON) en `license_inventory_daemons`.
- [x] Backend: Lógica de persistencia en `InventorySyncService` y auto-mapeo en `AuditService`.
- [x] UI: Badges de Sold-To adicionales en el perfil de cliente (Blade) y modal de auditoría (Alpine).
- [x] Verificación: Validado mediante simulación de callback con datos reales de Gurutzpe.

### Estabilización de Sesión JWT (Fix #014) ✅
- **Completada:** 2026-05-15
- **Rama:** fix/jwt-premature-expiration
- **Resumen:** Implementación de rotación atómica de tokens con ventana de gracia de 30s en Redis para evitar expulsiones en peticiones concurrentes. Sincronización de TTL a 15 min y fix de desincronización de secretos en `.env`.
- [x] Backend: Implementada rotación en `JwtAuth.php`.
- [x] Infra: Configurado periodo de gracia en Redis.
- [x] Config: Sincronización de `JWT_SECRET` en `infra/.env.beta`.
- [x] Mantenimiento: Purga automática de blacklist.
- [x] Emergencia: Restauración de tablas maestras tras vaciado accidental.

- **Completada:** 2026-05-14
- **Rama:** chore/error-tracking
- **Resumen:** Creación del archivo `management/ERRORS.md` con estética industrial para el registro y triaje de errores detectados por el desarrollador.
- [x] Gestión: Creación de `ERRORS.md` con tabla de incidencias y protocolo de resolución.
- [x] Workflow: Implementación de la rama de mantenimiento.

### Resolución de Incidencia #002 — Scripts de Backup ✅
- **Completada:** 2026-05-15
- **Rama:** dev (directo tras cirugía Git)
- **Resumen:** Reparado el script `backup-db.sh` convirtiéndolo a formato Unix (LF), corrigiendo errores de sintaxis en bloques `if` y blindando variables de entorno. Se añadió mejora de naming dinámico (`beta_manual_...`) para distinguir origen en la UI.
- [x] Fix: Conversión CRLF -> LF.
- [x] Syntax: Blindaje de variables y corrección de bloques `if`.
- [x] Mejora: Naming con etiqueta manual/system.
- [x] UI: Nueva columna "Origen" en la gestión de backups.

### Soporte Multi-Sold-To (Licencias Unificadas) ✅
- **Completada:** 2026-05-14
- **Rama:** feature/multi-sold-to
- **Resumen:** Implementación del soporte para licencias Siemens que contienen múltiples IDs de cliente (Sold-To). Incluye auto-mapeo en el inventario y rediseño visual de badges industriales.
- [x] Base de Datos: Columna `additional_sold_tos` (JSON) en `license_inventory_daemons`.
- [x] Backend: Lógica de auto-mapeo en `AuditService` y `InventorySyncService`.
- [x] UI: Rediseño de badges `fa-link` con alta densidad técnica.
- [x] n8n: Actualización del motor de extracción IA.

### Optimización: Salto de Auditoría IA para Temporales ✅
- **Completada:** 2026-05-14
- **Rama:** fix/skip-ai-audit-temporary-licenses
- **Resumen:** Eliminación del consumo innecesario de tokens de IA para licencias temporales de 7 días. El sistema ahora detecta automáticamente estas licencias en NX, Star-CCM+ y HEEDS, marcándolas como `skipped` sin realizar llamadas externas.
- [x] Backend: Modificación de `AuditService` para interceptar peticiones temporales.
- [x] Controladores: Actualización de `NXSuiteController`, `StarCcmController` y `HeedsController` con detección de tipo.
- [x] Trazabilidad: Registro en BD con estado `skipped` y mensaje informativo para el usuario.
- [x] Verificación: Validación de sintaxis y logs mediante SSH/Docker en el servidor.

### Fix: Validación MIME en Herramientas (Moldex3D) ✅
- **Completada:** 2026-05-14
- **Rama:** fix/moldex-mime-type-validation
- **Resumen:** Corregido error que impedía subir archivos `.mac` en algunos entornos debido a la detección inconsistente de tipos MIME por parte del navegador.
- [x] Backend: Eliminada restricción de `mimetypes` en `MoldexController`.
- [x] Preventivo: Aplicada la misma mejora en controladores de Siemens (NX, StarCCM, Heeds) para mayor robustez.
- [x] Verificación: Sintaxis validada en el servidor.
### Optimización Visual Dashboard ✅
- **Completada:** 2026-05-14
- **Rama:** feature/dashboard-ui-optimization
- **Resumen:** Transformación de las tarjetas de métricas del Dashboard a estilo premium (línea de acento superior de 3px), unificación de colores corporativos (verde para activas) e integración de iconos de fondo NOC Pro (Lucide SVGs).
- [x] UI: Implementación de pseudo-elementos `::before` para líneas de acento.
- [x] Identidad: Cambio de color de "Licencias Activas" a verde (`success`).
- [x] Estética: Integración de iconos de fondo con opacidad `0.08` y rotación `-15deg`.
- [x] UX: Sombreado dinámico en hover y unificación de `border-radius`.

### Fase 14 — Planificador de Renovaciones (Motor & UI) ✅
- **Completada:** 2026-05-14
- **Rama:** feature/renewal-planner
- **Resumen:** Implementación completa del módulo operativo para el seguimiento mensual de renovaciones. Incluye motor de filtrado cíclico, soporte para múltiples archivos adjuntos por acción y visualización de historial en la ficha del cliente. Optimización final con diseño NOC Pro y sincronización de identidad corporativa.
- [x] Infraestructura: Tablas `renewal_logs` y `renewal_log_files`.
- [x] Backend: Lógica de filtrado por mes y gestión de subidas múltiples.
- [x] UI NOC Pro: Rediseño de alta densidad con selector de mes Alpine.js y layout espejo de Clientes.
- [x] Identidad: Sincronización total de colores de estados con `identities.json`.
- [x] Lógica: Sistema de reversión de logs (Undo) y limpieza rápida de filtros.
- [x] Historial: Pestaña dedicada en Perfil de Cliente con descarga de archivos.
- [x] Seguridad: Blindaje de dashboard administrativo para no-admins.

### Dashboard: Enfoque en Licencias ✅
- **Completada:** 2026-05-13
- **Rama:** feature/dashboard-license-focus (merged to dev)
- **Resumen:** Cambio de fuente de datos en la portada: de Contratos CSV a Licencias del Inventario.
- [x] Backend: Agrupación por `daemon_id` y fecha mínima de expiración.
- [x] UI: Rediseño de tabla (una sola línea VENDOR · SOLD-TO).
- [x] UX: Enlaces directos a fichas de cliente desde la tabla.

### Rediseño Visual de Alertas ✅
- **Completada:** 2026-05-13
- **Rama:** feature/alerts-ui-redesign (merged to dev)
- **Resumen:** Rediseño integral de la configuración de umbrales en el panel de alertas. Layout vertical de alta densidad con iconos semánticos y campos técnicos optimizados.
- [x] UI: Migración de Grid horizontal a Lista vertical.
- [x] Layout: Ajuste de grid principal 1fr:1.5fr.
- [x] UX: Labels de rango dinámicos y mayor espacio para inputs.

### Estandarización de Nomenclatura y Transformación ✅
- **Completada:** 2026-05-13
- **Rama:** feature/license-naming-standard (merged to dev)
- **Resumen:** Implementación del estándar profesional de nombres para todos los productos Siemens (NX, StarCCM, Heeds). Incluye corrección de bug en la transformación de vendor y forzado de localhost en temporales.
- [x] Backend: Refactor de `NXSuiteService`, `StarCcmService` y `HeedsService`.
- [x] Naming: Formato `[ID]_[HOST]_[CLIENTE]_V[VER]_Valida_[FECHA].lic`.
- [x] Unificada: Soporte para múltiples Sold-To concatenados.
- [x] Fix: Regex estricta para línea `VENDOR` (evita corrupción de INCREMENT).
- [x] Fix: SERVER `localhost` para licencias temporales.

### Gestión de Clientes — Identificador de Licencias e Inventario ✅
- **Completada:** 2026-05-13
- **Rama:** feature/client-license-filter + fix/alpine-audit-null-errors (merged)
- **Resumen:** Implementación de filtrado y señalización visual de clientes con licencias activas. Incluye Switch Técnico Industrial (diseño cuadrado 6px, look profesional sin glow), unificación de badges de inventario y blindaje preventivo contra errores de nulos en Alpine.js (`x-if` + optional chaining) en todo el portal.
- [x] Backend: Query optimizada con `withCount(['contracts', 'inventoryDaemons'])`.
- [x] Filtro Persistente: Implementación de lógica de Sesión en `ClientController`.
- [x] UI Industrial: Switch de alta precisión con bordes técnicos de 6px y knob físico.
- [x] Estabilidad Alpine: Blindaje total en modales de auditoría y herramientas (COD, Moldex).
- [x] Unificación: Badges de Sold-To alineados al sistema de diseño oficial.

---


### Fase 10.5 — Docker Monitor NOC Pro ✅
- **Completada:** 2026-05-13
- **Rama:** feature/docker-monitor
- **Resumen:** Implementación de monitorización de contenedores Docker en tiempo real. Incluye telemetría de CPU/RAM con indicadores circulares, gestión segura de reinicios desde el panel y optimización de rendimiento mediante desacoplamiento del dashboard principal.
- [x] Instalación de `docker-cli` y mapeo de socket en infraestructura.
- [x] Implementación de `DockerMonitorService` (telemetría por entorno).
- [x] Vista dedicada `/admin/system/docker` con diseño "Bento/NOC Pro".
- [x] Iconografía oficial (FontAwesome 6) y colores corporativos por servicio.
- [x] Acción de reinicio segura con confirmación de sistema.

---

## ⏸️ Pausado / En Espera

- [ ] **Integración n8n v2.2+**: Evolución del flujo lineal a ramificado por Vendor/Tipo Licencia.


---

En planificación — se detallan una por una tras validación de la fase anterior.
 
| Fase | Nombre                   | Prerequisito        |
| :--- | :----------------------- | :------------------ |
| 9    | Moldex3D (9.1→9.2)       | ✅ Fase 8.4 validada  |
| 10   | Dashboard del Sistema    | ✅ Fase 9 validada  |
 
---
 

### Fase 13 — Alertas y Notificaciones ✅
- **Completada:** 2026-05-13
- **Rama:** feature/expiration-alerts-system
- **Resumen:** Implementación del sistema de alertas de caducidad de licencias con reporte global interno. Incluye lógica de filtrado de inventario, mailable bilingüe consolidado, historial de envíos automático y panel administrativo de control.
- [x] Implementación de `GlobalLicenseExpirationReport`.
- [x] Lógica de filtrado en `LicenseExpirationService` (0, 7, 15, 30 días).
- [x] Integración con `EmailLoggerListener` (trazabilidad única).
- [x] Panel Administrativo `/admin/alerts` (Bento UI).
- [x] Fix de permisos y duplicidad de logs.

### UI/UX — Fixes Menores ✅
- [x] **Fix Bug Modal Auditoría**: El botón de ojo no abre el modal en Beta (Arreglado anteriormente).

### Fase 8.5/9.2 — Módulo de Recursos y Enlaces Standalone ✅
- **Completada:** 2026-05-12
- **Rama:** feature/resource-links-module
- **Módulo de Recursos (Fase 8.5/9.2)**: Implementación de sistema dinámico de gestión de enlaces y documentación con páginas independientes para Siemens y Moldex3D. Incluye panel de gestión reactivo (Alpine.js) para Staff/Admin.
- [x] Modelo, Migración y Seeder de Recursos iniciales.
- [x] Páginas independientes: `/herramientas/siemens/recursos` y `/herramientas/moldex3d/recursos`.
- [x] UI de gestión dinámica con RBAC (Staff, Technician, Admin).
- [x] Integración en el Hub de Herramientas con rutas directas.

### Herramientas IA — Asistente de Composite (COD)
- **Completada:** 2026-05-12
- **Rama:** feature/cod-composite-parser
- **Resumen:** Integración de motor Gemini 3.1 Flash-Lite para el análisis inteligente de logs de hardware. Incluye zona de carga Drag & Drop, identificación automática de adaptadores físicos y volcado de datos al generador de COD.
- [x] Integración de `CompositeParserService` con Gemini 3.1.
- [x] UI Premium: Drag & Drop con estética "blue dashed".
- [x] Dashboard: Iconos de marca y colores para servicios IA, Infraestructura y Procesadores.
- [x] Fix: Centrado de iconos, dimensiones 34x34 y sombras elevadas.

### Fase 14 — Gestión de Backups
- **Completada:** 2026-05-12
- **Rama:** feature/backup-management-system
- **Resumen:** Centralización de la gestión de backups, automatización de rotación y limpieza de archivos de sistema, y panel de control para el administrador.
- [x] Implementación de `BackupRotationService`.
- [x] Panel de control de backups (Download/Delete/Sync).
- [x] Notificaciones de éxito/fallo vía webhook.

### Fase 11 — Usuarios y Acceso
- **Completada:** 2026-05-12
- **Rama:** feature/rbac-user-management
- **Resumen:** Implementación completa del sistema de gestión de usuarios con RBAC granular. Incluye CRUD administrativo, toggle de estado vía AJAX, sistema de invitaciones con generación de contraseñas aleatorias y notificaciones profesionales.
- [x] CRUD de usuarios y asignación de roles.
- [x] Toggle de estado Activo/Inactivo con persistencia inmediata.
- [x] Sistema de seguridad: bloqueo de auto-acciones para el admin activo.
- [x] Notificaciones de bienvenida con credenciales.

### Fase 10 — Dashboard del Sistema (NOC Pro)

- **Completada:** 2026-05-11
- **Rama:** fix/dashboard-git-styling, fix/quick-actions-styling, fix/app-locale-es
- **Resumen:** Evolución completa a centro de mando NOC Pro. Telemetría de hardware y red, integración Git (hash/fecha localizada), acciones administrativas rápidas y estabilización de módulos de Backup y Auditoría.
- [x] Dashboard NOC Pro: Grid de alta densidad con telemetría en tiempo real.
- [x] Quick Actions: Panel interactivo (Caché, Workers, Backup, Mantenimiento).
- [x] Localización: Traducción dinámica de fechas de despliegue y locale global `es`.
- [x] Infraestructura: Fix de permisos Git y despliegue de módulos independientes.

### Fase 9 — Moldex3D
 
 ### Fase 10.4 — Modularización Administrativa (Backups & Logs) ✅
 - **Completada:** 2026-05-11
 - **Rama:** feature/system-modules-backups-audit
 - **Resumen:** Desacoplamiento total de la gestión de backups y logs del dashboard principal. Creación de módulos independientes con lógica dedicada, infraestructura de backups estabilizada (mariadb-client) y UI unificada siguiendo el estándar de diseño del portal.
 - [x] Migración a `BackupController` y `AuditLogController`.
 - [x] Implementación de Database Vault con gestión de archivos (Download/Delete).
 - [x] Centro de Auditoría con filtrado avanzado y estadísticas internas.
 - [x] Unificación estética de cabeceras (Estilo Importación).
 - [x] Fix de permisos en script de backup para gestión web.
 
 ### Fase 10 — Dashboard del Sistema (NOC Pro) ✅
 - **Completada:** 2026-05-11
 - **Rama:** feature/system-dashboard-noc
 - **Resumen:** Evolución a Dashboard de alta densidad "NOC Pro" con telemetría profunda, acciones rápidas y trazabilidad total.
 - [x] Métricas: PHP, nginx, MariaDB, Redis, almacenamiento (Hardware Grid)
 - [x] Telemetría Avanzada: Tráfico ETH0 (RX/TX), hilos DB y slow queries.
 - [x] Quick Actions: Control de caché, reinicio de workers, backups y modo mantenimiento.
 - [x] Mantenimiento Selectivo: Implementado bypass para administradores con aviso visual persistente.
 - [x] Git Integration: Hash de commit y fecha de despliegue en tiempo real.
 - [x] System Live Feed: Últimos 10 registros de auditoría administrativa.
 
 ### Fase 9 — Moldex3D (Auditoría y Persistencia) ✅
 - **Completada:** 2026-05-09
 - **Rama:** feature/moldex3d-persistence
 - **Resumen:** Implementación del motor de auditoría y persistencia para licencias Moldex3D. El sistema ahora procesa archivos `.mac`, extrae Machine IDs y sincroniza automáticamente el inventario de productos vinculándolos a clientes reales mediante lógica de similitud.
 - [x] Parser local determinista para archivos `.mac`.
 - [x] Implementación de `MoldexSyncService` para persistencia en inventario.
 - [x] Sistema de vinculación inteligente de clientes (Fuzzy Match).
 - [x] UI/UX Premium con vista "Property List" y feedback de sincronización en tiempo real.
 - [x] Gestión de seguridad: almacenamiento privado y proceso local 100% determinista.
 
 ---
 
### Fase 8.4 — Siemens COD (Certificado Cese) ✅
- **Completada:** 2026-05-08
- **Rama:** feature/cod-generation
- **Resumen:** Implementación completa del generador de certificados de cese oficial de Siemens. Incluye soporte bilingüe, alta fidelidad visual con fuentes Calibri, y sistema de almacenamiento seguro en disco privado.
- [x] Instalación y configuración de Dompdf con fuentes corporativas.
- [x] Modelo `CodCertificate` y servicio `CodService` (bilingüe).
- [x] Interfaz reactiva con Alpine.js en el Hub de Herramientas.
- [x] Integración de historial y accesos directos en la ficha de cliente.
- [x] Almacenamiento seguro y visible en `Z:\DX-License-Manager\storage\private\licenses\siemens\{client}\COD\`.
- [x] **Subida Directa**: Refactorización a formulario auto-enviable para máxima robustez.
- [x] **Permisos Automáticos**: Script de ajuste de permisos para visibilidad inmediata en Samba.

---
_Firmado por: **Antigravity (DX Agent)** 🦾_

### Fase 8.3 — HEEDS & UI Polish ✅
- **Completada:** 2026-05-08
- **Rama:** feature/heeds-ui-polish
- **Resumen:** Implementación del motor de auditoría HEEDS y unificación estética de todas las herramientas Siemens. Se eliminó la redundancia de estilos y se estabilizó el layout frente a cambios de scroll.
- [x] Implementación de `HeedsService` y vista `tools/heeds.blade.php`.
- [x] Unificación de `dx-styles.css` (300px sidebar, 24px gap, 4px radius).
- [x] Implementación de `overflow-y: scroll` para estabilidad de píxeles.
- [x] Integración de Engine Selector lateral en todas las herramientas.
 
### Fase 8.2 — STAR-CCM+ ✅
- **Completada:** 2026-05-08
- **Rama:** feature/heeds-ui-polish (integrada)
- **Resumen:** Implementación del motor de auditoría STAR-CCM+ y dashboard técnico.
- [x] Implementación de `StarCcmService` (cdlmd -> saltd).
- [x] Vista `tools/star-ccm.blade.php` con integración de Auditoría IA.
 
### Fase 6.5 — Normalización e Identidades (Cross-Module)
- **Completada:** 2026-05-08
- **Rama:** feature/normalization-cross-module
- **Resumen:** Implementación del motor de normalización inteligente y la bandeja administrativa. El sistema ahora limpia identidades de clientes tanto en el CSV como en las Licencias (AI) de forma automática.
- [x] Implementación de `ClientNormalizationService` (Fuzzy Match 85%).
- [x] Bandeja de Normalización Centralizada (`NormalizationController`).
- [x] Unificación total: Migración automática de contratos, licencias, inventario y contactos.
- [x] Persistencia de descartes en `normalization_decisions`.
- [x] Integración de avisos en `AiAuditResult`.

### Fase 8.1 — Siemens NX Suite (Inteligencia, Auditoría e Inventario)
- **Completada:** 2026-05-07
- **Rama:** feature/siemens-audit-motor
- **Resumen:** Implementación completa del ciclo de vida de licencias Siemens: transformación, auditoría IA, persistencia de inventario y visualización multi-Sold-To.
- [x] Migración `ai_audit_results` y `client_mappings`.
- [x] Implementación `LicenseParserService` (limpieza FlexLM).
- [x] Implementación `AuditService` e integración con n8n.
- [x] Rediseño de UI de Inventario Activo con CSS puro (Bento técnico).
- [x] Soporte para múltiples **Sold-To** agrupados por cliente.
- [x] Identificación de hardware (**MAC/Dongle**) en la UI.
- [x] Optimización de Prompt IA v2.2 (Soporte Dongle e IDs Numéricos) ⏳ (Pte. Verificar).
- [x] **Fix**: Validación MIME en Herramientas (Moldex3D, Siemens).

### Fase 8.1 (Parte 1) — Siemens NX Suite (Mecanismo)
- **Completada:** 2026-05-07
- **Rama:** feature/nx-suite-colors
- **Resumen:** Rediseño UI con colores semánticos, extensión .cid, corrección límite subida (413).
- [x] Corrección límite 413.
- [x] UI NX Suite con colores semánticos.

### Fase 6.3 — Contactos de Envío
- **Completada:** 2026-05-06
- **Rama:** feature/clients-base (continuación)
- **Resumen:** Implementación del sistema de gestión de contactos por cliente con persistencia de pestañas y UI compacta.
- [x] CRUD de contactos (Modelo, Controlador, Rutas).
- [x] UI de contactos en perfil de cliente con modales Alpine.js.
- [x] Persistencia de pestaña activa en `localStorage`.
- [x] Refinamiento estético de tablas y botones de acción horizontales.
- [x] Seeder de datos DEMO para pruebas de integración.

### Fase 5 — Portal Principal (Dashboard)

- **Completada:** 2026-05-06
- **Rama:** feature/dashboard-base
- **Resumen:** Implementación del Dashboard interactivo con métricas reales, tabla de vencimientos dinámicos y layouts corregidos para ultra-panorámicas.
- [x] Implementación de `DashboardController` con métricas de base de datos.
- [x] UI de Dashboard alineada con `DESIGN.md`.
- [x] Corrección de layouts (Header/Footer simétricos).
- [x] Extensión de sesión JWT a 60 minutos.

### Fase 4 — Importación CSV

- **Completada:** 2026-05-06
- **Rama:** feature/csv-importer-base
- **Resumen:** Implementación del motor de importación masiva con lógica de Upsert/Bajas, normalización de clientes y soporte para campo `sub_product`. UI administrativa optimizada.
- [x] Migraciones incrementales para `vendors`, `clients`, `contracts` e `import_logs`.
- [x] Implementación de `CsvImportService` (Auto-separator, Smart Header, 9 columnas).
- [x] Normalización de clientes en _Title Case_.
- [x] Lógica de marcado automático como "Baja" para contratos ausentes.
- [x] UI administrativa `/admin/import` alineada con `DESIGN.md` y balanceada (5/4).
- [x] Centralización de `.env` vía volúmenes de Docker.

### Fase 3 — Login

- [x] Implementación de `JwtService` y `AuthController`.
- [x] Middleware de RBAC (`JwtAuth`, `CheckPermission`).
- [x] Refinamiento de UI Login para ultra-panorámicas.
- [x] Implementación de Rate Limiting en login.
- [x] Suite de tests `AuthTest.php` (PASS).
- [x] Persistencia de tema dark/light.

### Fase 2 — Layouts Blade + Laravel

- [x] Instalación de Laravel 11 en `backend/`.
- [x] Configuración de Docker Compose para Laravel (PHP, MariaDB, Redis).
- [x] Implementación de layout base Blade (`app.blade.php`).
- [x] Migración de Dashboard inicial (`welcome.blade.php`).
- [x] Desbloqueo de assets en Beta (eliminación de alias Nginx).
- [x] Refactor de vistas: eliminación de Tailwind y uso de CSS Semántico.
- [x] Fix de permisos de `storage` y `bootstrap/cache` en servidor.
- [x] Documentación de lecciones aprendidas en `.agent/lessons.md`.

### Fase 1 — CSS + Assets

- [x] Integración de `dx-styles.css`.
- [x] Configuración de fuentes locales (Inter + IBM Plex Mono).
- [x] Verificación de variables CSS light/dark mode.

### Fase 0 — Infraestructura

- [x] Crear repo `DeXon18/DX-License-Manager` en GitHub.
- [x] Configurar ramas `main` y `dev`.
- [x] `management/` — BACKLOG, CHANGELOG, ROADMAP, HANDOFF creados.
- [x] `infra/docker-compose.*.yml` y `infra/nginx/*.conf` configurados.
- [x] `.github/workflows/` para CI/CD configurados.
- [x] `SSH_HOST`, `SSH_USER`, `SSH_PRIVATE_KEY`, `SSH_PORT` en GitHub Secrets.
- [x] Preparación del servidor LXC 600 y clonación del repo.
- [x] `git config --global --add safe.directory` en el servidor.
- [x] Crear `infra/.env.beta` e `infra/.env.prod` en el host.
- [x] Verificar carga de HTML estático en `beta.dxpro.es` y `portal.dxpro.es`.
- [x] Push a `dev` → deploy automático a beta verificado.
- [x] Inicialización del proyecto y repositorio Git.

---

## ?? Ideas Futuras / QoL

(Vacío)

