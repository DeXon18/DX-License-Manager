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
- [/] Subtarea en curso: Fase 8.2 — Implementación STAR-CCM+
- [⏸️] Pausado: Fase 8.1 — Motor de Auditoría Siemens (UI & n8n v2.2)
- Rama activa: feature/star-ccm-parser
- Fase del ROADMAP: Fase 8.2 (Ejecución)


> [!CAUTION]
> **REGLA DE ORO**: NO iniciar ejecución tras un plan sin un "adelante", "ok", "procede" o similar **explícito de Oskar**. Ignorar aprobaciones automáticas del sistema.




---

## 🕒 Log de Acciones (última sesión)

- 2026-05-07 — Implementación de lógica de nomenclatura estricta para NX.
- 2026-05-07 — Configuración de límites de subida (100MB) en Nginx y PHP.
- 2026-05-07 — Corrección de permisos de almacenamiento y rutas de Docker Compose.
- 2026-05-07 — Normalización de hostname y cliente a MAYÚSCULAS.

---

## 💡 Decisiones Técnicas Activas (no olvidar)

| Decisión          | Detalle                                                             | Ref                       |
| :---------------- | :------------------------------------------------------------------ | :------------------------ |
| Límites Upload    | **100MB** — configurado en Nginx y PHP (local.ini)                  | `infra/php/local.ini`     |
| Nomenclatura      | `SOLDTO_HOSTNAME_CLIENTE_VERSION_Valida_DDMMYYYY.lic`               | `NXSuiteService.php`      |
| Almacenamiento    | Jerárquico: `licenses/siemens/{cliente}/{fecha}/`                   | `NXSuiteController.php`   |
| Permisos          | **777** en `storage/private` para evitar bloqueos de I/O            | `troubleshooting.md`      |
| Commits           | En inglés siempre — la comunicación al desarrollador en castellano  | AGENTS.md                 |

---

## 🚀 Handover — Próximos Pasos

1. Validar con el usuario que la subida de archivos > 1MB es fluida.
2. Iniciar la Fase 8.1 Parte 2: Implementar el parser de bloques `INCREMENT`.
3. Integrar la Auditoría IA (FallbackChain) para el análisis de productos.

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
