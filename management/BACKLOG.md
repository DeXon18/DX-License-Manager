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

_(ninguna actualmente)_

---

## 🟠 Pendiente — Fase 0 (Infraestructura)

### Crear repo en GitHub
- Crear `DeXon18/DX-License-Manager` desde cero. - ✅ Completada
- Configurar ramas `main` y `dev`.

### Copiar archivos base al repo
- `management/` — BACKLOG, CHANGELOG, ROADMAP, HANDOFF
- `infra/docker-compose.beta.yml` y `docker-compose.prod.yml`
- `infra/nginx/beta.conf` y `prod.conf`
- `infra/html/index.html`
- `infra/.env.beta.example`
- `.gitignore`
- `.github/workflows/` — ci.yml, deploy-beta.yml, deploy-prod.yml
- `AGENTS.md`, `.agent/rules/`, `.agent/workflows/`

### Configurar GitHub Secrets
`SSH_HOST`, `SSH_USER`, `SSH_PRIVATE_KEY`, `SSH_PORT` indicar al usuario que datos tiene que poner en https://github.com/DeXon18/DX-License-Manager/settings/secrets/actions

### Preparar el servidor
- Limpiar directorio si existe
- Clonar repo nuevo
- `git config --global --add safe.directory /opt/web-projects/DX-License-Manager`
- Crear `infra/.env.beta` e `infra/.env.prod`

### Levantar stack beta y verificar
`beta.dxpro.es` accesible desde fuera de la red local.
- Revisar que carga el HTML estático.

### Levantar stack prod y verificar
`portal.dxpro.es` accesible desde fuera de la red local.
- Revisar que carga el HTML estático.

### Verificar GitHub Actions
- Push a `dev` → deploy automático a beta verificado.
  Verificarlo desde https://github.com/DeXon18/DX-License-Manager/actions/new

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

_(ninguna aún)_
