# ROADMAP — DX License Manager

> Hoja de ruta completa del proyecto. Las fases completadas se marcan con ✅ pero nunca se eliminan.
> **Regla:** Nunca eliminar fases. Marcar como completado, no borrar.

---

## ⛔ Regla de Validación — Sin Excepciones

**Ninguna fase puede iniciarse sin validación explícita del desarrollador (Oskar).**

El agente NUNCA avanza a la siguiente fase por iniciativa propia. El flujo es siempre:

```
Agente completa la fase
  → Agente presenta evidencia de que funciona
  → Agente espera validación explícita
  → Oskar revisa, prueba y da el visto bueno
  → Solo entonces el agente inicia la siguiente fase
```

Si el desarrollador no ha dicho explícitamente "aprobado", "adelante", "siguiente fase" o similar → **el agente no avanza.**

---

## Visión del Producto

**SoporteAYS** es un portal interno para gestión y auditoría de licencias de software industrial (Siemens PLM y Moldex3D).

**URLs:**
- Producción: `portal.dxpro.es` → `192.168.50.60:8001`
- Beta: `beta.dxpro.es` → `192.168.50.60:8002`

---

## Estructura del Proyecto

```
DX License Manager
├── 1. CSS + assets
├── 2. Layouts Blade
├── 3. Login
├── 4. Inicio
├── 5. Clientes
│   ├── Licencias
│   ├── ContraHeaders / Contratos      — contratos activos, estados y caducidades
│   ├── Contactos de envío             — datos del destinatario como referencia
│   └── Certificados de cese firmados  — historial de CODs firmados recibidos
├── 6. Herramientas
├── 7. Páginas de herramientas & recursos
│   ├── SIEMENS | Automatización industrial y digitalización
│   │   ├── NX Suite                   — ugslmd: NX, Designcenter, Teamcenter, Simcenter 3D & Amesim
│   │   ├── STAR-CCM+                  — cdlmd: STAR-CCM+
│   │   ├── HEEDS                      — RCTECH: HEEDS
│   │   ├── COD                        — generación de certificados de cese, PDF descargable
│   │   └── Recursos & enlaces
│   └── Moldex3D | Software de simulación de moldeo por inyección de plástico
│       ├── Moldex3D                   — archivos .mac: auditoría y procesamiento
│       └── Recursos & enlaces
└── 8. Sistema y configuración
    ├── Dashboard del sistema
    ├── Usuarios y acceso
    │   ├── Listado de usuarios
    │   ├── Crear / Editar usuario
    │   └── Roles y permisos
    ├── Datos e importación
    │   ├── Importar CSV
    │   ├── Historial de importaciones
    │   └── Errores de importación
    ├── Repositorio de licencias
    │   ├── Archivo semanal             — procesados de la semana, descargable como ZIP
    │   └── Historial de archivos       — qué se archivó, cuándo y quién lo descargó
    ├── Alertas y notificaciones
    │   ├── Alertas de caducidad
    │   │   ├── Configuración de umbrales
    │   │   ├── Destinatarios
    │   │   └── Historial de envíos
    │   └── Configuración SMTP
    ├── Backups
    │   ├── Backup manual
    │   ├── Historial de backups
    │   └── Configuración de backup automático
    ├── Integraciones IA
    │   ├── Gemini
    │   ├── Deepseek
    │   ├── OpenRouter
    │   ├── Telegram Bot
    │   └── Estado de conexión
    └── Logs y auditoría
        ├── Logs de actividad
        ├── Logs de errores
        └── Logs de auditoría IA
```

---

## ▸ BLOQUE 1 — Base Técnica

> Sin datos, sin UI real. Prerequisito para todo lo demás.

---

### ✅ Fase 0 — Infraestructura

**Estado:** COMPLETADA
**Validación:** ✅ Verificado por Oskar el 2026-05-05.

- [x] Repo `DeXon18/DX-License-Manager` creado en GitHub
- [x] Ramas `main` y `dev` configuradas
- [x] Estructura base de carpetas y `.gitignore`
- [x] Docker stack beta — nginx sirviendo HTML estático en `beta.dxpro.es`
- [x] Docker stack prod — nginx sirviendo HTML estático en `portal.dxpro.es`
- [x] Ambas URLs accesibles desde fuera de la red local
- [x] GitHub Actions deploy automático verificado (Node.js 24 fix incluido)

---

### ✅ Fase 1 — CSS + Assets

**Estado:** COMPLETADA (Técnicamente)
**Validación:** ⚠️ Estilos no cargan en Beta por posible caché de Cloudflare.
**Fecha:** 2026-05-05

- [x] `dx-styles.css` integrado en el proyecto
- [x] Fuentes Inter + IBM Plex Mono cargadas
- [x] Variables CSS light/dark mode operativas
- [x] Componentes base verificados (clases de utilidad añadidas)

---

### ✅ Fase 2 — Layouts Blade + Laravel

**Estado:** COMPLETADA
**Validación:** ✅ Layout principal operativo en Beta.
**Fecha:** 2026-05-05

- [x] Laravel 11 instalado en `backend/`
- [x] Tailwind CSS + Alpine.js configurados (vía clases utilidad en CSS)
- [x] Layout principal Blade (sidebar, header, footer)
- [x] Nginx actualizado para servir Laravel

---

### ✅ Fase 3 — Login
 
**Estado:** COMPLETADA
**Validación:** ✅ Login funcional con roles y acceso denegado verificados por Oskar y tests automatizados.
**Fecha:** 2026-05-05

- [x] Vista de login siguiendo `infra/html/01-login.html` (Rediseñada a Full Background)
- [x] JWT — access token 15min + refresh 24h con rotación
- [x] Middleware `JwtAuth` y `CheckPermission`
- [x] Migraciones: `users`, `roles`, `feature_flags`
- [x] Seeders: roles + usuario admin Oskar
- [x] Tests feature: login, logout, refresh, acceso denegado
- [x] Afinar estilos finales del login (Ultra-Wide Fix)

---

## ▸ BLOQUE 2 — Datos

> ⚠️ Bloque crítico. Sin importación CSV no hay datos y las vistas del Bloque 3 no tienen contenido.

---

### ✅ Fase 4 — Importación CSV
 
**Estado:** COMPLETADA
**⚠️ ADVERTENCIA CRÍTICA:** A partir de esta fase, Beta usa datos reales. **PROHIBIDO** `migrate:fresh`. Solo migraciones incrementales.
**Prerequisito:** ✅ Fase 3 validada por Oskar
**Validación:** ✅ Verificado por Oskar el 2026-05-06. 603 registros importados correctamente.
 
- [x] Migraciones: `vendors`, `clients`, `contracts`, `import_logs`
- [x] Panel de importación CSV en admin
- [x] Lógica upsert por `contract_number`
- [x] Normalización Title Case
- [x] Detección de bajas (desaparece del CSV → status Baja)
- [x] Informe post-importación con contadores y errores
- [x] Historial de importaciones
- [x] Tests: upsert, normalización, bajas, formato fecha

---

## ▸ BLOQUE 3 — Portal Principal

> Requiere Fase 4 completada y validada.

---

### 📋 Fase 5 — Inicio ✅ COMPLETADA

**Estado:** COMPLETADA
**Prerequisito:** ✅ Fase 4 validada por Oskar
**Validación requerida antes de Fase 6:** Dashboard mostrando datos reales de contratos y caducidades.

- [x] Vista de inicio siguiendo `infra/html/02-inicio.html`
- [x] Métricas: total contratos, críticos, próximos, por vendor
- [x] Widget de caducidades próximas
- [x] Accesos rápidos a herramientas

---

### 📋 Fase 6 — Clientes 🔜 EN CURSO

**Estado:** EN CURSO
**Prerequisito:** ✅ Fase 5 validada por Oskar
**Validación requerida antes de Fase 7:** Perfil de cliente completo con todas las subsecciones funcionando.

#### 6.1 — ContraHeaders / Contratos ✅ COMPLETADA
- [x] Listado con búsqueda, filtros y paginación
- [x] Badges de estado y caducidad por colores (Afinado con Oskar)
- [x] Vista global de caducidades (Dashboard + Perfil)

#### 6.2 — Licencias
- [ ] Migraciones: `license_files`, `license_products`
- [ ] Subida de archivos `.lic` y `.mac`
- [ ] Asociación automática a cliente por nombre

#### 6.3 — Contactos de envío
- [ ] CRUD de contactos por cliente
- [ ] Campos: nombre, email, empresa, cargo

#### 6.4 — Certificados de cese firmados
- [ ] Subida y almacenamiento de CODs firmados recibidos
- [ ] Historial por cliente

---

## ▸ BLOQUE 4 — Herramientas

---

### 📋 Fase 7 — Hub de Herramientas

**Estado:** En planificación
**Prerequisito:** ✅ Fase 6 validada por Oskar
**Validación requerida antes de Fase 8:** Hub visible con cards por vendor y feature flags funcionando.

- [ ] Vista hub siguiendo `infra/html/03-herramientas.html`
- [ ] Cards agrupadas por vendor (Siemens / Moldex3D)
- [ ] Feature flags controlan visibilidad y acceso
- [ ] Badge "Próximamente" para herramientas inactivas

---

### 📋 Fase 8 — Siemens

**Estado:** En planificación
**Prerequisito:** ✅ Fase 7 validada por Oskar
**Validación requerida antes de Fase 9:** Todas las subherramientas Siemens funcionando en beta.

#### 8.1 — NX Suite
- [ ] Parser PHP local de archivos `.lic` ugslmd
- [ ] Auditoría IA via n8n FallbackChain
- [ ] Resultado visual + descarga del archivo transformado

#### 8.2 — STAR-CCM+
- [ ] Parser PHP local de archivos `.lic` cdlmd
- [ ] Auditoría IA via n8n FallbackChain

#### 8.3 — HEEDS
- [ ] Parser PHP local de archivos `.lic` RCTECH
- [ ] Auditoría IA via n8n FallbackChain

#### 8.4 — COD
- [ ] Formulario: Sold-To, solicitante, empresa, tipo de cambio
- [ ] Generación de PDF oficial Siemens
- [ ] Guardado en historial del cliente

#### 8.5 — Recursos & enlaces
- [ ] Links a documentación oficial Siemens
- [ ] Recursos internos de referencia

---

### 📋 Fase 9 — Moldex3D

**Estado:** En planificación
**Prerequisito:** ✅ Fase 8 validada por Oskar
**Validación requerida antes de Fase 10:** Herramienta Moldex3D funcionando en beta.

#### 9.1 — Moldex3D
- [ ] Parser PHP local de archivos `.mac`
- [ ] Extracción de Machine ID
- [ ] Auditoría IA via n8n FallbackChain

#### 9.2 — Recursos & enlaces
- [ ] Links a documentación oficial Moldex3D

---

## ▸ BLOQUE 5 — Sistema y Configuración

---

### 📋 Fase 10 — Dashboard del Sistema

**Estado:** En planificación
**Prerequisito:** ✅ Fase 9 validada por Oskar
**Validación requerida antes de Fase 11:** Dashboard mostrando métricas reales de infra y servicios.

- [ ] Métricas: PHP, nginx, MariaDB, Redis, almacenamiento
- [ ] Estado de servicios IA con badges en tiempo real
- [ ] Estado de conexión Telegram

---

### 📋 Fase 11 — Usuarios y Acceso

**Estado:** En planificación
**Prerequisito:** ✅ Fase 10 validada por Oskar
**Validación requerida antes de Fase 12:** CRUD completo de usuarios verificado con todos los roles.

- [ ] Listado de usuarios con filtro por rol
- [ ] Crear / Editar usuario con asignación de rol
- [ ] Activar / Desactivar usuario
- [ ] Gestión de roles y permisos

---

### 📋 Fase 12 — Repositorio de Licencias

**Estado:** En planificación
**Prerequisito:** ✅ Fase 11 validada por Oskar
**Validación requerida antes de Fase 13:** Archivo semanal generado y descargable correctamente.

- [ ] Agrupación automática de archivos procesados por semana
- [ ] Descarga como ZIP
- [ ] Historial: qué se archivó, cuándo, quién

---

### 📋 Fase 13 — Alertas y Notificaciones

**Estado:** En planificación
**Prerequisito:** ✅ Fase 12 validada por Oskar
**Validación requerida antes de Fase 14:** Alerta de caducidad enviada correctamente a destinatario de prueba.

- [ ] Configuración de umbrales de caducidad
- [ ] Gestión de destinatarios por cliente
- [ ] Historial de envíos
- [ ] Configuración SMTP

---

### 📋 Fase 14 — Backups

**Estado:** En planificación
**Prerequisito:** ✅ Fase 13 validada por Oskar
**Validación requerida antes de Fase 15:** Backup manual generado y restauración verificada.

- [ ] Backup manual de BD
- [ ] Configuración de backup automático (cron)
- [ ] Historial de backups
- [ ] Verificación de restauración

---

### 📋 Fase 15 — Integraciones IA

**Estado:** En planificación
**Prerequisito:** ✅ Fase 14 validada por Oskar
**Validación requerida antes de Fase 16:** Todos los proveedores IA respondiendo y FallbackChain verificado.

- [ ] Configuración Gemini + test de conexión
- [ ] Configuración Deepseek + test de conexión
- [ ] Configuración OpenRouter + test de conexión
- [ ] Configuración Telegram Bot + test de notificación
- [ ] Estado de conexión en tiempo real
- [ ] FallbackChain verificado extremo a extremo

---

### 📋 Fase 16 — Logs y Auditoría

**Estado:** En planificación
**Prerequisito:** ✅ Fase 15 validada por Oskar
**Validación requerida:** Logs de las tres categorías visibles y filtrables en beta.

- [ ] Logs de actividad de usuarios
- [ ] Logs de errores del sistema
- [ ] Logs de auditoría IA (proveedor usado, resultado, timestamp)

---

## Stack Tecnológico

| Capa           | Tecnología                     |
| :------------- | :----------------------------- |
| Backend        | PHP 8.2 / Laravel 11           |
| Vistas         | Laravel Blade                  |
| CSS            | Tailwind CSS + dx-styles.css   |
| JS             | Alpine.js                      |
| BD             | MariaDB 10.11 LTS              |
| Caché / Colas  | Redis 7.x                      |
| Web server     | Nginx 1.25+                    |
| Contenedores   | Docker 24+ / Compose V2        |
| SSL            | Cloudflare       
| 5 — Portal Principal          
| ✅ COMPLETADA     
| Inicio y Gestión de Clientes/Contratos         |        |
| Automatización | n8n                            |
| Auditoría IA   | Gemini → DeepSeek → OpenRouter |
| Notificaciones | Telegram                       |
