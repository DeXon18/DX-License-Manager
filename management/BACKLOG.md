# BACKLOG — DX License Manager

> Gestión de tareas del proyecto. Las tareas completadas se mueven a la sección correspondiente pero **nunca se eliminan**.
> **Regla:** Mover, no borrar.

---

## ⛔ Regla de Validación

**Ninguna fase puede iniciarse sin validación explícita de Oskar.**
El agente no avanza hasta recibir "aprobado", "adelante" o similar de forma explícita.

---

## 🔴 Bloqueado

_(ninguna actualmente)_

---

## 🔵 En Progreso

### Fase 3 — Autenticación y JWT
- [ ] Vista de login siguiendo `infra/html/01-login.html`.
- [ ] Implementar `JwtService` y `AuthController`.
- [ ] Middleware de RBAC.

---

## 🟠 Pendiente — Fases 4 a 16

En planificación — se detallan una por una tras validación de la fase anterior.

| Fase | Nombre | Prerequisito |
| :--- | :----- | :----------- |
| 4 | Importación CSV | ✅ Fase 3 validada |
| 5 | Inicio | ✅ Fase 4 validada |
| 6 | Clientes (6.1→6.4) | ✅ Fase 5 validada |
| 7 | Hub de Herramientas | ✅ Fase 6 validada |
| 8 | Siemens (8.1→8.5) | ✅ Fase 7 validada |
| 9 | Moldex3D (9.1→9.2) | ✅ Fase 8 validada |
| 10 | Dashboard del Sistema | ✅ Fase 9 validada |
| 11 | Usuarios y Acceso | ✅ Fase 10 validada |
| 12 | Repositorio de Licencias | ✅ Fase 11 validada |
| 13 | Alertas y Notificaciones | ✅ Fase 12 validada |
| 14 | Backups | ✅ Fase 13 validada |
| 15 | Integraciones IA | ✅ Fase 14 validada |
| 16 | Logs y Auditoría | ✅ Fase 15 validada |

---

## ✅ Completado

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
