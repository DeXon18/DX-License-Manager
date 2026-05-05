---
project: DX License Manager
repo: github.com/DeXon18/DX-License-Manager
status: Ready for Merge
last_sync: 2026-05-05
current_agent: Antigravity (Caveman Mode)
---

# 🧠 Contexto de Sesión Activa

## 🎯 Objetivo Actual

- [x] Tarea principal: Fase 3 — Login (Finalizada)
- [ ] Subtarea en curso: Merge a `dev` e inicio Fase 4
- Rama activa: feature/auth-rbac-db
- Fase del ROADMAP: Fase 3 (Completada)

---

## 🕒 Log de Acciones (última sesión)

- 2026-05-05 — Sincronización multi-PC.
- 2026-05-05 — Fase 2 completada (Laravel 11 + Blade Layout).
- 2026-05-05 — Detectado bloqueo en Beta: `dx-styles.css` no carga por posible caché.

---

## 💡 Decisiones Técnicas Activas (no olvidar)

| Decisión          | Detalle                                                             | Ref                       |
| :---------------- | :------------------------------------------------------------------ | :------------------------ |
| Assets Beta       | Mapeados a `backend/public/assets` vía Nginx                        | `beta.conf`               |
| HTTPS Force       | Activado en `AppServiceProvider` para evitar Mixed Content          | `backend/app/`            |
| Fuente UI         | **Inter** — elegida por el desarrollador                            | DESIGN.md                 |
| Parser .lic       | PHP extrae localmente, nunca enviar archivo completo a la IA        | `security-check.md §3`    |
| Commits           | En inglés siempre — la comunicación al desarrollador en castellano  | AGENTS.md                 |

---

## 🚀 Handover — Próximos Pasos

1. Purgar caché de Cloudflare para `beta.dxpro.es`.
2. Verificar rutas de assets en el HTML generado por Laravel (`view source`).
3. Validar visualización en `portal.dxpro.es` (Producción).
4. Iniciar Fase 3 (Login) una vez visualización sea correcta.

---

## 🗂️ Archivos en Foco (Working Set)

- Configuración Nginx: `infra/nginx/`
- Layout Blade: `backend/resources/views/layouts/`
- Documentación: `management/`

---

## ⚠️ Errores Conocidos / Bloqueos

- **BLOQUEO**: Estilos CSS no cargan en Beta (posible caché Cloudflare).

---

## 🔧 Stack Activo

| Capa                | Estado                           |
| :------------------ | :------------------------------- |
| nginx-beta `:8002`  | ✅ running                       |
| php-fpm-beta        | ✅ running                       |
| mariadb-beta        | ✅ running                       |
| redis-beta          | ✅ running                       |
| nginx-prod `:8001`  | ✅ running                       |
| Cloudflared LXC 600 | ✅ running                       |
| GitHub Actions      | ✅ configured                    |