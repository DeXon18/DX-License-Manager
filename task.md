# 📋 Task List — DX License Manager

> Este archivo define las tareas de la sesión actual.
> Cada tarea tiene checks de verificación obligatorios — no se puede marcar como completa sin haberlos superado todos.
> El agente actualiza este archivo en tiempo real conforme avanza.
> Si hay un problema o bug, el agente debe solucionarlo antes de continuar.
> Todas las tareas y subtareas completadas deben ser movidas a la sección "Tareas Completadas Esta Sesión".

---

## Cómo Usar Este Archivo

Cada tarea sigue este formato:

```markdown
- [ ] N. Nombre de la tarea
  - [ ] Regla aplicada: [archivo de regla §sección]
  - [ ] Check pre-ejecución: [qué verificar antes de empezar]
  - [ ] Evidencia requerida: [qué demostrar para marcarla como hecha]
  - [ ] Rama: [nombre de la rama donde se ejecuta]
```

El agente NO marca `[x]` una tarea hasta que todos sus checks están completados y la evidencia mostrada.

---

## Tareas de la Sesión Actual

### 🎨 Fase 19 — Unificación CSS & Limpieza UI (Activa)
- [ ] Subfase 19.26: Páginas de Error (`errors/`: 403, 404, 419, 500, 503)
  - [ ] Regla aplicada: `AGENTS.md` §0.3 (DESIGN.md) & §0.4 (Descomposición)
  - [ ] Check pre-ejecución: Analizar bloque local `<style>` en `errors/503.blade.php`.
  - [ ] Evidencia: Purga completa del bloque `<style>` de más de 200 líneas y vinculación exitosa a `dx-styles.css`.
  - [ ] Rama: feature/css-tokens

### 🛡️ Seguridad & RBAC Hardening
- [ ] 1. Corrección de Seguridad Fase 2 (MIME & Fallbacks)
  - [ ] Regla aplicada: `security-check.md` §4 & §3
  - [ ] Check pre-ejecución: Revisar reporte `260509_auditoria-seguridad-fase2.md`.
  - [ ] Evidencia: Tests de subida de archivos rechazando extensiones no válidas.
  - [ ] Rama: fix/security-audit-fase2

- [ ] 2. Blindaje de Rutas Admin (RBAC)
  - [ ] Regla aplicada: `security-check.md` §6
  - [ ] Check pre-ejecución: Revisar `web.php` y middleware `CheckPermission`.
  - [ ] Evidencia: Test de acceso denegado (403) para rol `viewer` en `/admin/*`.
  - [ ] Rama: fix/rbac-hardening

---

## Tareas Completadas Esta Sesión

### 🎨 Fase 19 — Unificación CSS (Completadas en feature/css-tokens)
- [x] Subfase 19.20: Datos e importación (importar CSV, historial, logs y detalles)
- [x] Subfase 19.21: Repositorio de licencias (archivo semanal, historial)
- [x] Subfase 19.22: Alertas y notificaciones (caducidad, umbrales, destinatarios, historial, SMTP)
- [x] Subfase 19.23: Backups (manual, historial, configuración automática)
- [x] Subfase 19.24: Integraciones IA (Gemini, Deepseek, OpenRouter, Telegram Bot, estado de conexión)
- [x] Subfase 19.25: Logs y auditoría (actividad, errores, auditoría IA)

### 📊 Historial de Tareas Previas

 
- [x] 0. Estabilización de Tests de Integración del Dashboard
  - [x] Regla aplicada: `troubleshooting.md` (Error SQL Unknown column 'role')
  - [x] Check pre-ejecución: Forzar conexión SQLite en `SystemDashboardTest`.
  - [x] Evidencia: Ejecución en verde (`PASS`) de `SystemDashboardTest` en el servidor.
  - [x] Rama: fix/test-stabilization
- [x] 1. Implementación de System Dashboard (Operator Control Center)
  - [x] Regla aplicada: `DESIGN.md` (Bento Grid, IBM Plex Mono, Outfit)
  - [x] Refinar UI del Dashboard (NOC Style)
    - [x] Corregir detección de memoria en LXC (cgroups)
    - [x] Implementar visualización de iconos y fuente Outfit
    - [x] Categorizar Services Matrix (Infra, Proc, AI)
    - [x] Localizar etiquetas a castellano natural
    - [x] Corregir conteo de usuarios online (Redis Presence)
  - [x] Evidencia: Dashboard NOC funcional con métricas precisas de contenedor (cgroups).
  - [x] Refinamiento: Centrado de valores, fuente premium `Outfit` e iconos "Ghost".
  - [x] Rama: feature/system-dashboard
- [x] 2. Estabilización de Métricas de Contenedor
  - [x] Fix: Lectura de `/sys/fs/cgroup/memory.max` para reportar RAM real del LXC.
  - [x] Fix: Formateo de CPU Load separado por intervalos.

- [x] 3. Evolución a NOC Pro Dashboard
  - [x] Regla aplicada: `AGENTS.md` (Integridad de Datos, Una cosa a la vez)
  - [x] Telemetría avanzada: Tráfico ETH0 y métricas profundas MariaDB.
  - [x] Quick Actions: Implementación de SystemActionController y panel Alpine.js.
  - [x] Git Integration: Detección automática de hash y fecha de despliegue.
  - [x] Live Feed: Historial de auditoría administrativa en tiempo real.
  - [x] Evidencia: Dashboard con controles funcionales y telemetría de kernel activa.
  - [x] Rama: feature/system-noc-pro

- [x] 4. Mantenimiento Selectivo (Admin Friendly)
  - [x] Regla aplicada: `security-check.md` §6 (RBAC)
  - [x] Middleware: Crear `SelectiveMaintenance` para filtrar tráfico por rol.
  - [x] UI: Banner de aviso superior para admins.
  - [x] UI: Vista 503 profesional para usuarios estándar.
  - [x] Evidencia: Admin puede navegar en mantenimiento; usuario estándar ve 503.
  - [x] Rama: feature/system-noc-pro

---

## Notas de Sesión

- **Dashboard**: Los tests ahora pasan correctamente usando SQLite en memoria. Se corrigió el nombre de la cookie a `jwt_token` y el texto de validación a "Salud del Sistema".
- **Unknown Column 'role'**: Se confirmó que el error era un desajuste de esquema en los tests; el modelo usa `role_id` correctamente.
- **Seguridad**: Próximo paso es aplicar las remediaciones del reporte de auditoría del 09/05.
