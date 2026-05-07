# BACKLOG — DX License Manager

> Gestión de tareas del proyecto. Las tareas completadas se mueven a la sección correspondiente pero **nunca se eliminan**.
> **Regla:** Mover, no borrar.

---

## ⛔ Regla de Validación

**Ninguna fase puede iniciarse sin validación explícita de Oskar.**
El agente no avanza hasta recibir "aprobado", "adelante" o similar de forma explícita.

---

## 🟢 En Progreso

- [ ] **Fase 8.2**: Auditoría STAR-CCM+.

---

## 🟠 Pendiente — Fases 4 a 16

En planificación — se detallan una por una tras validación de la fase anterior.

| Fase | Nombre                   | Prerequisito        |
| :--- | :----------------------- | :------------------ |
| 4    | Importación CSV          | ✅ Fase 3 validada  |
| 5    | Inicio                   | ✅ Fase 4 validada  |
| 6    | Clientes (6.1→6.4)       | ✅ Fase 5 validada  |
| 7    | Hub de Herramientas      | ✅ Fase 6 validada  |
| 8    | Siemens (8.1→8.5)        | ✅ Fase 7 validada  |
| 9    | Moldex3D (9.1→9.2)       | ✅ Fase 8 validada  |
| 10   | Dashboard del Sistema    | ✅ Fase 9 validada  |
| 11   | Usuarios y Acceso        | ✅ Fase 10 validada |
| 12   | Repositorio de Licencias | ✅ Fase 11 validada |
| 13   | Alertas y Notificaciones | ✅ Fase 12 validada |
| 14   | Backups                  | ✅ Fase 13 validada |
| 15   | Integraciones IA         | ✅ Fase 14 validada |
| 16   | Logs y Auditoría         | ✅ Fase 15 validada |

---

## ✅ Completado

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
