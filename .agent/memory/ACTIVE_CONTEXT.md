---
project: DX License Manager
repo: github.com/DeXon18/DX-License-Manager
status: Planning
last_sync: 2026-05-04
current_agent: Claude
---

# 🧠 Contexto de Sesión Activa

## 🎯 Objetivo Actual

- [ ] Tarea principal: Fase 0 — Verificación de Infraestructura
- [ ] Subtarea en curso: Crear repo en GitHub y estructura base
- Rama activa: main
- Fase del ROADMAP: Fase 0

---

## 🕒 Log de Acciones (última sesión)

- 2026-05-04 — Documentación de gestión reseteada al estado real. El proyecto parte desde cero.

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

1. Crear repo `DeXon18/DX-License-Manager` en GitHub
2. Configurar ramas `main` y `dev`
3. Subir estructura base de carpetas y archivos
4. Configurar GitHub Secrets
5. Preparar servidor LXC 600 y levantar stacks

---

## 🗂️ Archivos en Foco (Working Set)

- Documentación de gestión: `management/`
- Infraestructura: `infra/` (por crear)

---

## ⚠️ Errores Conocidos / Bloqueos

- Ninguno actualmente

---

## 🔧 Stack Activo

| Capa                | Estado                           |
| :------------------ | :------------------------------- |
| nginx-beta `:8002`  | ❌ no levantado aún              |
| php-fpm-beta        | ❌ no levantado aún              |
| mariadb-beta        | ❌ no levantado aún              |
| redis-beta          | ❌ no levantado aún              |
| nginx-prod `:8001`  | ❌ no levantado aún              |
| Cloudflared LXC 600 | ❌ no configurado aún            |
| GitHub Actions      | ❌ no configurado aún            |