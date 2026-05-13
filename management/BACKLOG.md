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
 
- [x] **Fase 10**: Dashboard del Sistema y Modularización (Completada 2026-05-11).
- [x] **Fase 16**: Centro de Logs Unificado y Auditoría Pro (Completada 2026-05-12).
- [x] **Fase 13**: Alertas y Notificaciones (Completada 2026-05-13).
- [x] **Fase 10.5**: Docker Monitor NOC Pro (Completada 2026-05-13).

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

- [ ] **AI Hardware Assistant**: Sistema de análisis de archivos `composite.txt` mediante IA para recomendar el ID más estable (Ethernet físico) a personal no técnico. Evita errores en trámites de licencia.

