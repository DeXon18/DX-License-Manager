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

- **Fase 0 — Infraestructura**: Repo creado, Docker stacks beta/prod activos, CI/CD configurado.
- **Fase 1 — CSS + Assets**: Integración de `dx-styles.css` y fuentes locales.
- **Fase 2 — Layouts Blade + Laravel**: Refactor de `app.blade.php` y `welcome.blade.php` con CSS Semántico. Desbloqueo de assets en Beta.
- Inicialización del proyecto y repositorio Git.
- Configuración de workflows de CI/CD.
- Preparación inicial del servidor LXC 600.
