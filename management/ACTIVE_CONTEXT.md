---
project: DX License Manager
repo: github.com/DeXon18/DX-License-Manager
status: In Progress
last_sync: 2026-05-05
current_agent: Claude
---

# 🧠 Contexto de Sesión Activa

## 🎯 Objetivo Actual

- [ ] Tarea principal: Fase 0 — Verificación de Infraestructura
- [ ] Subtarea en curso: Levantar stacks y verificar despliegue automático
- Rama activa: dev
- Fase del ROADMAP: Fase 0

---

## 🕒 Log de Acciones (última sesión)

- 2026-05-05 — Inicialización de Git local y remoto.
- 2026-05-05 — Configuración de GitHub Workflows (`ci`, `deploy-beta`, `deploy-prod`).
- 2026-05-05 — Preparación del servidor LXC 600 (clonado y archivos .env listos).
- 2026-05-05 — Configuración de GitHub Secrets finalizada por el desarrollador.

---

## 💡 Decisiones Técnicas Activas (no olvidar)

| Decisión          | Detalle                                                             | Ref                       |
| :---------------- | :------------------------------------------------------------------ | :------------------------ |
| Fuente UI         | **Inter** — elegida por el desarrollador                            | DESIGN.md                 |
| Referencia visual | HTMLs estáticos en `infra/html/` — replicar en Blade sin improvisar | `infra/html/*.html`       |
| Verificación      | Tinker antes de tests formales                                      | IDENTITY.md               |
| Parser .lic       | PHP extrae localmente, nunca enviar archivo completo a la IA        | `security-check.md §3`    |
| Commits           | En inglés siempre — la comunicación al desarrollador en castellano  | AGENTS.md                 |
| Merge             | Solo via `/merge` workflow con CI en verde — nunca manual           | `merge-feature.md`        |

---

## 🚀 Handover — Próximos Pasos

1. Realizar push de prueba a `dev` para verificar el despliegue automático en Beta.
2. Verificar acceso a `beta.dxpro.es:8002`.
3. Realizar push de prueba a `main` para verificar el despliegue automático en Producción.
4. Verificar acceso a `portal.dxpro.es:8001`.

---

## 🗂️ Archivos en Foco (Working Set)

- Workflows: `.github/workflows/`
- Infraestructura: `infra/`
- Gestión: `management/`

---

## ⚠️ Errores Conocidos / Bloqueos

- El directorio del proyecto ha sido renombrado a `DX-License-Manager`.

---

## 🔧 Stack Activo

| Capa                | Estado                           |
| :------------------ | :------------------------------- |
| nginx-beta `:8002`  | ❌ Pendiente de primer deploy    |
| php-fpm-beta        | ❌ No incluido en Fase 0         |
| mariadb-beta        | ❌ No incluido en Fase 0         |
| redis-beta          | ❌ No incluido en Fase 0         |
| nginx-prod `:8001`  | ❌ Pendiente de primer deploy    |
| Cloudflared LXC 600 | ✅ Operativo                     |
| GitHub Actions      | ✅ Configurado                   |
