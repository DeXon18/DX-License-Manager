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

### Fase 0 — Verificación de Infraestructura
- [x] Verificar carga de HTML estático en `beta.dxpro.es`. - ✅ Completada
- [ ] Verificar carga de HTML estático en `portal.dxpro.es`.

---

## 🟠 Pendiente — Fase 0 (Infraestructura)

### Crear repo en GitHub
- [x] Crear `DeXon18/DX-License-Manager` desde cero.
- [x] Configurar ramas `main` y `dev`.

### Copiar archivos base al repo
- [x] `management/` — BACKLOG, CHANGELOG, ROADMAP, HANDOFF
- [x] `infra/docker-compose.beta.yml` y `docker-compose.prod.yml`
- [x] `infra/nginx/beta.conf` y `prod.conf`
- [x] `infra/html/index.html`
- [x] `infra/.env.beta.example`
- [x] `.gitignore`
- [x] `.github/workflows/` — ci.yml, deploy-beta.yml, deploy-prod.yml
- [x] `AGENTS.md`, `.agent/rules/`, `.agent/workflows/`

### Configurar GitHub Secrets
- [x] `SSH_HOST`, `SSH_USER`, `SSH_PRIVATE_KEY`, `SSH_PORT` configurados en GitHub.

### Preparar el servidor
- [x] Limpiar directorio si existe.
- [x] Clonar repo nuevo.
- [x] `git config --global --add safe.directory /opt/web-projects/DX-License-Manager`.
- [x] Crear `infra/.env.beta` e `infra/.env.prod`.

### Levantar stack beta y verificar
- [x] `beta.dxpro.es` accesible desde fuera de la red local.
- [x] Revisar que carga el HTML estático.

### Levantar stack prod y verificar
- [ ] `portal.dxpro.es` accesible desde fuera de la red local.
- [ ] Revisar que carga el HTML estático.

### Verificar GitHub Actions
- [x] Push a `dev` → deploy automático a beta activado.

---

## 🟡 Pendiente — Fases 1 a 16

En planificación — se detallan una por una tras validación de la fase anterior.

| Fase | Nombre | Prerequisito |
| :--- | :----- | :----------- |
| 1 | CSS + Assets | ✅ Fase 0 validada |
| 2 | Layouts Blade + Laravel | ✅ Fase 1 validada |
| 3 | Login | ✅ Fase 2 validada |
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

- Inicialización del proyecto y repositorio Git.
- Configuración de workflows de CI/CD.
- Preparación inicial del servidor LXC 600.
