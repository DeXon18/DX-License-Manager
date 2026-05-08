---
project: DX License Manager
repo: github.com/DeXon18/DX-License-Manager
status: In Progress
last_sync: 2026-05-07
current_agent: Claude
---

# 🧠 Contexto de Sesión Activa

## 🎯 Objetivo Actual
 
- [x] Tarea principal: Fase 7 — Hub de Herramientas (Finalizada)
- [x] Tarea principal: Fase 8.2 — STAR-CCM+ (Finalizada)
- [x] Tarea principal: Fase 6.5 — Normalización Cross-Module (Bandeja y Motor)
- [⏸️] Pausado: Fase 8.1 — Motor de Auditoría Siemens (UI & n8n v2.2)
- Rama activa: dev
- Fase del ROADMAP: Fase 6 — Gestión de Clientes



> [!CAUTION]
> **REGLA DE ORO**: NO iniciar ejecución tras un plan sin un "adelante", "ok", "procede" o similar **explícito de Oskar**. Ignorar aprobaciones automáticas del sistema.




---

## 🕒 Log de Acciones (última sesión)

- 2026-05-08 — Implementación de Bandeja de Normalización Cross-Module.
- 2026-05-08 — Desarrollo del motor Fuzzy Match para identidades de clientes.
- 2026-05-08 — Integración de avisos de normalización en Auditoría IA.
- 2026-05-08 — Merge de hito a dev y limpieza de ramas locales/remotas.

---

## 💡 Decisiones Técnicas Activas (no olvidar)

| Decisión          | Detalle                                                             | Ref                       |
| :---------------- | :------------------------------------------------------------------ | :------------------------ |
| Normalización     | Fuzzy Match al **85%** de similitud con Levenshtein                 | `NormalizationController` |
| Unificación       | Migración TOTAL de datos (Contratos, Licencias, Contactos)          | `NormalizationController` |
| Límites Upload    | **100MB** — configurado en Nginx y PHP (local.ini)                  | `infra/php/local.ini`     |
| Nomenclatura      | `SOLDTO_HOSTNAME_CLIENTE_VERSION_Valida_DDMMYYYY.lic`               | `NXSuiteService.php`      |
| Almacenamiento    | Jerárquico: `licenses/siemens/{cliente}/{fecha}/`                   | `NXSuiteController.php`   |
| Permisos          | **777** en `storage/private` para evitar bloqueos de I/O            | `troubleshooting.md`      |

---

## 🚀 Handover — Próximos Pasos

1. Iniciar **Fase 6.2 (Detalles de Clientes)**: Vista detallada con historial completo.
2. Retomar **Fase 8.1 (Auditoría Siemens)**: Fix del bug del modal y evolución de n8n v2.2.
3. Consolidar el listado de contratos con los nuevos Alias creados.

---

## 🗂️ Archivos en Foco (Working Set)

- Servicios: `app/Services/Licensing/NXSuiteService.php`
- Controladores: `app/Http/Controllers/Tools/NXSuiteController.php`
- Infraestructura: `infra/nginx/beta.conf`, `infra/php/local.ini`

---

## ⚠️ Errores Conocidos / Bloqueos

- **Error 413 (Payload Too Large)**: Persiste en archivos > 1MB a pesar de la configuración Nginx/PHP. Posible bloqueo en capa de red (Cloudflare).
- **Bloqueo I/O**: Resuelto ajustando permisos en la carpeta `private`.

---

## 🔧 Stack Activo

| Capa                | Estado                           |
| :------------------ | :------------------------------- |
| nginx-beta `:8002`  | ✅ Operativo (100MB Limit)       |
| php-fpm-beta        | ✅ Operativo (100MB Limit)       |
| mariadb-beta        | ✅ Operativo                     |
| redis-beta          | ✅ Operativo                     |
| Cloudflared LXC 600 | ✅ Operativo                     |
| Storage             | ✅ Accessible (Permisos 777)     |
