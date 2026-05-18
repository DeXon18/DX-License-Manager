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
├── 1. CSS + assets ✅
├── 2. Layouts Blade ✅
├── 3. Login ✅
├── 4. Inicio ✅
├── 5. Clientes ✅
│   ├── Licencias ✅ (Inventario unificado)
│   ├── ContraHeaders / Contratos ✅ (Importación CSV)
│   ├── Contactos de envío ✅ (Gestión por cliente)
│   ├── Certificados de cese firmados ✅ (Gestión de CODs)
│   └── Planificador de Renovaciones ✅ (Fase 14)
├── 6. Herramientas ✅
├── 7. Páginas de herramientas & recursos
│   ├── SIEMENS | Automatización industrial y digitalización
│   │   ├── NX Suite ✅ (ugslmd, saltd)
│   │   ├── STAR-CCM+ ✅ (cdlmd)
│   │   ├── HEEDS ✅ (RCTECH)
│   │   ├── COD ✅ (Generador + Asistente IA)
│   │   └── Recursos & enlaces 📋
│   └── Moldex3D | Software de simulación de moldeo por inyección de plástico
│       ├── Moldex3D ✅ (Parser .mac + Sincronización)
│       └── Recursos & enlaces 📋
└── 8. Sistema y configuración ✅
    ├── Dashboard del sistema ✅ (NOC Pro + Brand Icons)
    ├── Usuarios y acceso ✅
    │   ├── Listado de usuarios ✅
    │   ├── Crear / Editar usuario ✅
    │   └── Roles y permisos ✅
    ├── Datos e importación ✅
    │   ├── Importar CSV ✅
    │   ├── Historial de importaciones ✅
    │   └── Errores de importación ✅
    ├── Repositorio de licencias ✅
    │   ├── Archivo semanal ✅
    │   └── Historial de archivos ✅
    ├── Alertas y notificaciones ✅
    │   ├── Alertas de caducidad ✅
    │   ├── Configuración de umbrales ✅
    │   ├── Destinatarios ✅
    │   ├── Historial de envíos ✅
    │   └── Configuración SMTP ✅ (Producción activa)
    ├── Backups ✅
    │   ├── Backup manual ✅
    │   ├── Historial de backups ✅
    │   └── Configuración de backup automático ✅
    ├── Integraciones IA ✅
    │   ├── Gemini ✅ (Flash 3.1)
    │   ├── Deepseek ✅
    │   ├── OpenRouter ✅
    │   ├── Telegram Bot ✅
    │   └── Estado de conexión ✅
    └── Logs y auditoría ✅
        ├── Logs de actividad ✅
        ├── Logs de errores ✅
        └── Logs de auditoría IA ✅
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

### 📋 Fase 6 — Clientes ✅ COMPLETADA

**Estado:** COMPLETADA
**Prerequisito:** ✅ Fase 5 validada por Oskar
**Validación requerida antes de Fase 7:** Perfil de cliente completo con todas las subsecciones funcionando.

#### 6.1 — ContraHeaders / Contratos ✅ COMPLETADA
- [x] Listado con búsqueda, filtros y paginación
- [x] Badges de estado y caducidad por colores (Afinado con Oskar)
- [x] Vista global de caducidades (Dashboard + Perfil)

#### 6.2 — Licencias ✅ COMPLETADA
- [x] Migraciones: `license_inventory_daemons`, `license_inventory_products`
- [x] Subida y auditoría de archivos `.lic` (Siemens) y `.mac` (Moldex3D)
- [x] Asociación automática a cliente por motor de similitud (Fuzzy Match)

#### 6.3 — Contactos de envío ✅ COMPLETADA
- [x] CRUD de contactos por cliente (Listado compacto + Modales)
- [x] Persistencia de pestaña en localStorage
- [x] Seeder de datos DEMO para pruebas

#### 6.4 — Certificados de cese firmados ✅ COMPLETADA
- [x] Subida y almacenamiento seguro de CODs firmados recibidos
- [x] Historial por cliente con visualización de estados
- [x] Ruta de descarga protegida para archivos firmados

#### 6.5 — Normalización e Identidades ✅ COMPLETADA
- [x] Motor de similitud (Fuzzy Match 85%) y sistema de Alias.
- [x] Bandeja de Normalización Centralizada (Admin).
- [x] Integración cruzada: Captura automática de typos en CSV y Licencias (AI).
- [x] Lógica de unificación total (migración de contratos, licencias, contactos).
- [x] Sistema de descarte persistente de sospechas.

---

## ▸ BLOQUE 4 — Herramientas

---

### ✅ Fase 7 — Hub de Herramientas
 
**Estado:** COMPLETADA
**Validación:** ✅ Verificado por Oskar el 2026-05-06. Hub dinámico sincronizado con identities.json.
 
- [x] Vista hub dinámica controlada por Feature Flags
- [x] Cards agrupadas por vendor (Siemens / Moldex3D)
- [x] Sincronización de llaves y labels con `identities.json`
- [x] Badge "Próximamente" para herramientas inactivas

---

### ✅ Fase 8 — Siemens ✅ COMPLETADA

**Estado:** COMPLETADA
**Validación:** ✅ Verificado por Oskar el 2026-05-12. Ecosistema Siemens completo.

#### 8.1 — NX Suite (`siemens_nx_suite`) ✅
- [x] Mecanismo de transformación Siemens NX (Standard, Dongle, Unificada)
- [x] Normalización estricta de nomenclatura (MAYÚSCULAS)
- [x] Almacenamiento jerárquico y gestión de duplicados
- [x] Parser de contenido (INCREMENT), Auditoría IA y Resultados Estructurados
- [x] Rediseño de UI de Inventario Activo (Alta Densidad Técnica)
- [x] Soporte para múltiples Sold-To por cliente
- [x] Optimización de Auditoría IA (v2.2): Soporte Dongle e IDs Numéricos
- [x] **Validación UI:** ✅ Verificado por Oskar el 2026-05-08 (Unificación total).

#### 8.2 — STAR-CCM+ (`siemens_star_ccm`) ✅
- [x] Parser PHP local de archivos `.lic` cdlmd
- [x] Transformación automática a SALT (29000/29001) e identificador STARCCM
- [x] Almacenamiento jerárquico por Mes-Año (MM-YYYY)
- [x] Auditoría IA via n8n FallbackChain
- [x] **Validación:** ✅ Motor y UI unificada OK.

#### 8.3 — HEEDS (`siemens_heeds`) ✅
- [x] Parser PHP local de archivos `.lic` RCTECH (Extracción avanzada de cabecera)
- [x] Transformación automática a SALT (29000/29001) e identificador HEEDS
- [x] Almacenamiento jerárquico por Cliente/Mes-Año
- [x] Auditoría IA via n8n FallbackChain
- [x] **Nomenclatura Estándar (Refinamiento)**: Unificación de formatos para NX, StarCCM y HEEDS ✅
- [x] **Validación:** ✅ Motor y UI unificada OK (2026-05-13).

#### 8.4 — COD (`siemens_cod`) ✅
**Estado:** COMPLETADA
**Validación:** ✅ Verificado por Oskar el 2026-05-08. Generador bilingüe con fidelidad Calibri.
- [x] Formulario: Sold-To, solicitante, empresa, tipo de cambio con múltiples MACs.
- [x] Generación de PDF oficial Siemens con fuentes corporativas.
- [x] Guardado en historial del cliente y almacenamiento seguro.
- [x] **AI Hardware Assistant (QoL)**: Análisis de `composite.txt` con Gemini (Subida de archivo, Drag & Drop y pegado) ✅

#### 8.5 — Recursos & enlaces ✅
- [x] Links a documentación oficial Siemens
- [x] Recursos internos de referencia
- [x] Gestión dinámica Staff/Admin via UI


---

### ✅ Fase 9 — Moldex3D
 
 **Estado:** COMPLETADA
 **Prerequisito:** ✅ Fase 8 validada por Oskar
 **Validación:** ✅ Verificado por Oskar el 2026-05-09. Parser local y persistencia en inventario operativos.
 
 #### 9.1 — Moldex3D ✅
 - [x] Parser PHP local de archivos `.mac` (Regex determinista)
 - [x] Extracción de Machine ID y metadatos de cliente
 - [x] Auditoría de productos, cantidades y fechas de expiración
 - [x] Persistencia automática en Inventario Activo (`MoldexSyncService`)
 - [x] Vinculación inteligente de clientes (Fuzzy Match)
 
 #### 9.2 — Recursos & enlaces ✅
 - [x] Links a documentación oficial Moldex3D
 - [x] Gestión dinámica Staff/Admin via UI

---

## ▸ BLOQUE 5 — Sistema y Configuración

---

### ✅ Fase 10 — Dashboard del Sistema (NOC Pro)
 
**Estado:** COMPLETADA
**Validación:** ✅ Verificado con telemetría de kernel (ETH0), acciones administrativas funcionales y tests de integración el 2026-05-11.
**Descripción:** Evolución a Dashboard de alta densidad "NOC Pro" con telemetría profunda, acciones rápidas y trazabilidad total.
 
- [x] Métricas: PHP, nginx, MariaDB, Redis, almacenamiento (Hardware Grid)
- [x] **Telemetría Avanzada**: Tráfico ETH0 (RX/TX), hilos DB y slow queries.
- [x] **Quick Actions**: Control de caché, reinicio de workers, backups y modo mantenimiento.
- [x] **Mantenimiento Selectivo**: Implementado bypass para administradores con aviso visual persistente.
- [x] **Git Integration**: Hash de commit y fecha de despliegue en tiempo real.
- [x] **Localización Git**: Traducción dinámica de fechas relativas al castellano ("hace X segundos").
- [x] **System-wide Safe Directory**: Configuración de permisos Git en Docker para acceso multi-usuario (`www-data`).
- [x] **UI Semántica**: Código de colores representativos en métricas y botones de acción.
- [x] **System Live Feed**: Últimos 10 registros de auditoría administrativa.
- [x] Estado de servicios IA con badges en tiempo real (Telegram check)
- [x] Visualización de tendencias (7 días) y distribución de Daemons (Chart.js)
- [x] Tests de integración y validación de seguridad (RBAC)

---

### ✅ Fase 10.4 — Modularización de Sistema (Backups & Audit)

**Estado:** COMPLETADA
**Prerequisito:** ✅ Fase 10 (NOC Pro) estable
**Validación:** ✅ Verificado por Oskar el 2026-05-11. Módulos independientes, infraestructura de backups estabilizada y UI unificada.

- [x] Despliegue de secciones independientes (Backups, Logs)
- [x] Implementación de Backup Vault avanzado (Download/Delete/Size)
- [x] **Infraestructura Robusta**: Instalación de `mariadb-client` y script de backup seguro (SSL 0, bash).
- [x] Centro de Auditoría con filtros avanzados (Actividad, Errores, IA)
- [x] Limpieza y enlace desde el Dashboard principal
- [x] **Header Standardization**: Cabeceras de administración unificadas con estilo "Importación".
- [x] **UX Quick Actions**: Alineación a la izquierda y micro-interacciones de desplazamiento lateral.

---

### ✅ Fase 10.5 — Docker Monitor NOC Pro ✅ COMPLETADA

**Estado:** COMPLETADA
**Prerequisito:** ✅ Fase 10.4 estable
**Validación:** ✅ Verificado por Oskar el 2026-05-13. Telemetría y reinicios funcionales.
- [x] UI de monitorización de recursos (CPU/RAM/Uptime).
- [x] Acciones de reinicio de contenedores desde el portal.


---

### ✅ Fase 11 — Usuarios y Acceso ✅ COMPLETADA

**Estado:** COMPLETADA
**Validación:** ✅ Verificado por Oskar el 2026-05-12. CRUD y roles 100% operativos.

- [x] Listado de usuarios con filtro por rol
- [x] Crear / Editar usuario con asignación de rol (Generación de contraseñas OK)
- [x] Activar / Desactivar usuario (Toggle AJAX)
- [x] Gestión de roles y permisos (Asignación funcional)

---

### ✅ Fase 12 — Repositorio de Licencias
 
**Estado:** COMPLETADA
**Prerequisito:** ✅ Fase 11 validada por Oskar
**Validación:** ✅ Verificado por Oskar el 2026-05-12. Generación de ZIP, normalización de carpetas y envío de reporte semanal operativos.
 
- [x] Agrupación automática de archivos procesados por semana (Previous ISO Week)
- [x] Normalización de carpetas (MAYÚSCULAS, sin puntos/comas)
- [x] Generación de ZIP estructurado
- [x] Envío automático por correo a soporte (Lunes 07:00 AM)
- [x] Historial administrativo y descarga manual

---

### ✅ Fase 13 — Alertas y Notificaciones
 
**Estado:** COMPLETADA
**Prerequisito:** ✅ Fase 12 validada por Oskar
**Validación:** ✅ Verificado por Oskar el 2026-05-13. Reporte global enviado correctamente a soporte.
 
- [x] Configuración de umbrales de caducidad (0, 7, 15, 30 días)
- [x] Reporte Global Consolidado (Interno Soporte)
- [x] Historial de envíos unificado
- [x] Configuración SMTP (Producción activa)

---

### ✅ Fase 14 — Planificador de Renovaciones ✅ COMPLETADA

**Estado:** COMPLETADA
**Validación:** ✅ Implementación técnica finalizada el 2026-05-13.
**Descripción:** Herramienta operativa para el seguimiento mensual de renovaciones con soporte multi-archivo.

- [x] **Motor de Seguimiento**: Filtrado cíclico por `MONTH(end_date)` para identificar tareas mensuales.
- [x] **Registro de Acciones**: Tabla `renewal_logs` para trazabilidad de quién y cuándo envió licencias.
- [x] **Soporte Multi-archivo**: Capacidad para adjuntar múltiples archivos `.lic` (NX, Star, Heeds) simultáneamente.
- [x] **Repositorio de Renovaciones**: Almacenamiento seguro y estructurado por cliente.
- [x] **UI Alta Densidad**: Vista de planificador estilo NOC Pro con contadores de progreso.
- [x] **Integración Perfil Cliente**: Nueva pestaña de historial con acceso a descargas históricas.

---

### 📋 Fase 15 — Integraciones IA & Readiness

**Estado:** PAUSADA
**Prerequisito:** ✅ Fase 14 validada por Oskar
**Validación requerida antes de Fase 16:** Todos los proveedores IA respondiendo y FallbackChain verificado.

- [ ] Configuración Gemini + test de conexión
- [ ] Configuración Deepseek + test de conexión
- [ ] Configuración OpenRouter + test de conexión
- [ ] Configuración Telegram Bot + test de notificación
- [ ] Estado de conexión en tiempo real
- [ ] FallbackChain verificado extremo a extremo

---

### ✅ Fase 15.5 — Inventario Granular y UI

**Estado:** COMPLETADA
**Validación:** ✅ Verificado por Oskar el 2026-05-15.
**Descripción:** Refinamiento de filtros de inventario y estandarización UI.

- [x] Filtro segmentado de 4 estados (OFF, ALL, Siemens, Moldex3D).
- [x] Rediseño premium de la barra de búsqueda (600px, glassmorphism).
- [x] Persistencia de filtros multi-vendor por sesión.

---

### ✅ Fase 16 — Centro de Logs Unificado (Auditoría Pro)

**Estado:** COMPLETADA
**Validación:** ✅ Verificado por Oskar el 2026-05-12. Logs de sistema, actividad y email integrados con UI NOC Pro.

- [x] **Logs de Actividad**: Timeline detallado de acciones de usuarios con filtros avanzados.
- [x] **Logs de Sistema**: Visor nativo PHP de `laravel.log` (últimas 200 líneas) integrado en UI.
- [x] **Logs de Email**: Trazabilidad completa de correos enviados (destinatario, asunto, estado).
- [x] **Gestión de Logs**: Funcionalidad de Reset por sección con registro de evento de seguridad.
- [x] **UI/UX Premium**: Sistema de pestañas, indicadores NOC Pro y diseño oscuro optimizado.

---

### ✅ Fase 18 — Estabilización de Sesión JWT
 
**Estado:** COMPLETADA
**Validación:** ✅ Verificado por Oskar el 2026-05-15. Sesión estable con rotación atómica y ventana de gracia.
 
- [x] Implementación de **Rotación Atómica** de tokens.
- [x] Ventana de gracia de 30s en Redis para peticiones concurrentes.
- [x] Sincronización de TTL a 15 min (Cookies + Backend).
- [x] Desacoplamiento de `JWT_SECRET` de la `APP_KEY`.
- [x] Comando de limpieza automática de blacklist en Redis.

---

# 📋 Fase 19 — Unificación CSS & Limpieza UI

**Estado:** INICIADA
**Prerequisito:** ✅ Auditoría Forense CSS (#008) finalizada
**Validación requerida:** Consistencia visual total. Cero `style=` inline en vistas (excl. emails/pdf).

---

## 🔷 GIT — ESTRATEGIA DE RAMAS & COMMITS

**Rama única de trabajo:** `dev`

### Convención de commits

Usar prefijo `css:` para todos los commits de esta fase:

```
css(19.0): design tokens -- namespace --dx-v2-* y limpieza huérfanas
css(19.2): layouts -- extracción sidebar/footer a .dx-v2-
css(19.5): clientes -- limpieza style= inline en show.blade.php
css(19.28): componentes -- modales y tablas refactor namespace
css(19-hardening): eliminación !important sin justificación
css(19-docs): style guide interno + README fase
```

### Checkpoints de commit en `dev`

No acumular trabajo sin commitear. Puntos de commit recomendados:

- [x] **Checkpoint A** — tras 19.0 + 19.1 + 19.2 *(tokens + css base + layouts)*
- [ ] **Checkpoint B** — tras 19.3 → 19.9 *(auth + dashboard + clientes completo)*
- [ ] **Checkpoint C** — tras 19.10 → 19.17 *(herramientas + siemens + moldex3d)*
- [ ] **Checkpoint D** — tras 19.18 → 19.25 *(sistema & configuración completo)*
- [ ] **Checkpoint E** — tras 19.26 → 19.29 *(especiales + componentes + exclusiones)*
- [ ] **Commit final** — tras hardening + documentación

### Backup de seguridad antes de iniciar

- [x] Tag git en estado actual: `git tag backup/pre-fase-19`
- [x] Push del tag al remoto: `git push origin backup/pre-fase-19`

---

## 🔷 RELEASES (tags en `dev`)

- [ ] **Tag v2.19.0-rc1** — tras Checkpoint C *(parcial, herramientas limpias)*
    - [ ] `git tag v2.19.0-rc1 && git push origin v2.19.0-rc1`
- [ ] **Tag v2.19.0-rc2** — tras Checkpoint E *(completo, previo a hardening)*
    - [ ] `git tag v2.19.0-rc2 && git push origin v2.19.0-rc2`
- [ ] **Tag v2.19.0** — tras commit final + verificación de criterios de aceptación
    - [ ] `git tag v2.19.0 && git push origin v2.19.0`
    - [ ] CHANGELOG actualizado con lista de subfases completadas.
    - [ ] Nota de release: consistencia visual total, zero `style=` inline, namespace `.dx-v2-`.

---

## 🔷 AGRUPACIÓN DE ESTILOS COMUNES & PROBLEMÁTICOS

### Archivos compartidos a crear antes de refactorizar subfases

Identificar y extraer estos patrones repetidos a su hoja común **antes** de procesar cada subfase:

- [ ] **`dx-v2-status-badges.css`** — badges de estado (activo, caducado, próximo, suspendido)
    > Aparecen en: Clientes, Licencias, Planificador, Alertas, Logs.

- [ ] **`dx-v2-data-tables.css`** — tablas de datos con ordenación, filtros y paginación
    > Aparecen en: Licencias, Contratos, Usuarios, Importaciones, Repositorio, Logs.

- [ ] **`dx-v2-stat-cards.css`** — tarjetas de métricas / KPI cards
    > Aparecen en: Dashboard, Inicio, Sistema, Clientes show.

- [ ] **`dx-v2-timeline.css`** — líneas de tiempo y actividad
    > Aparecen en: Planificador, Logs de actividad, Historial de importaciones.

- [ ] **`dx-v2-tool-header.css`** — cabecera de página de herramienta (ícono + título + descripción + acciones)
    > Aparecen en: NX, STAR-CCM+, HEEDS, COD, Moldex3D.

- [ ] **`dx-v2-connection-status.css`** — indicadores de conexión / estado de servicio
    > Aparecen en: Dashboard Sistema, Integraciones IA, Docker Monitor.

- [ ] **`dx-v2-empty-states.css`** — estados vacíos (sin datos, sin resultados, primer uso)
    > Aparecen en: Licencias, Logs, Repositorio, Historial backups.

- [ ] **`dx-v2-forms-common.css`** — inputs, selects, grupos de formulario con validación
    > Aparecen en: toda la app (crear/editar en casi todos los módulos).

### Estilos problemáticos a vigilar durante el refactor

- [ ] Selectores de ID (`#id { }`) → reemplazar por clase `.dx-v2-`.
- [ ] Valores hardcodeados de `color`, `background`, `font-size` (hex/px) → reemplazar por tokens `--dx-v2-*`.
- [ ] `z-index` sin escala definida → centralizar tabla de z-index en el archivo de tokens.
- [ ] `transition` y `animation` duplicados entre componentes → centralizar en `dx-v2-motion.css`.

---

## 🔷 PRE-TRABAJO

- [x] **Subfase 19.0** — Design Tokens & Variables CSS *(obligatorio antes de 19.1)*
    - [x] Inventario completo de `--variables` en uso.
    - [x] Eliminación de variables huérfanas o duplicadas.
    - [x] Namespace unificado: `--dx-v2-*` (colores, espaciados, tipografía, radios, sombras).
    - [x] Centralizar tabla de `z-index`.
    - [x] Documentar mapa de tokens final.

---

## 🔷 [1–2] CSS GLOBAL & LAYOUTS

- [x] **Subfase 19.1** — CSS Base & Assets (`resources/css/`, `resources/js/`, `public/`)
    - [x] Consolidar hojas sueltas / imports redundantes (Optimizada cabecera de `app.blade.php` y `login.blade.php` eliminando llamadas externas).
    - [x] Verificar que el build (Vite/Mix) no purgue clases `.dx-v2-` usadas dinámicamente (JS/Alpine) (Confirmada inmunidad frente a purgas automáticas).
    - [x] Limpieza de `!important` sin justificación (Confirmados utilitarios legítimos y anulación de estilos en botones).
    > **Impacto visual:** Afecta a la tipografía de **todo el portal global y la pantalla de acceso**, migrando a fuentes locales offline para rendimiento ultra-rápido y privacidad.

- [x] **Subfase 19.2** — Layouts Blade (app layout, sidebar, topbar, footer, paginación `vendor/pagination/`) ✅ COMPLETADA
    - [x] Extracción y Namespace `.dx-v2-`.
    - [x] Limpieza de `style=` inline.
    - [x] Verificación visual (Light/Dark) y Responsive.

    > **Archivos Afectados:**
    > - [app.blade.php](file:///z:/DX-License-Manager/backend/resources/views/layouts/app.blade.php) - Modificados contenedores globales, header y el banner superior de advertencia por mantenimiento.
    > - [footer.blade.php](file:///z:/DX-License-Manager/backend/resources/views/layouts/partials/footer.blade.php) - Limpieza de colores inline de elementos dinámicos (ícono del corazón).
    > - [SelectiveMaintenance.php](file:///z:/DX-License-Manager/backend/app/Http/Middleware/SelectiveMaintenance.php) - Blindaje del middleware de mantenimiento para accesibilidad infalible de administradores.
    > - [dx.blade.php](file:///z:/DX-License-Manager/backend/resources/views/vendor/pagination/dx.blade.php), [dx-modern.blade.php](file:///z:/DX-License-Manager/backend/resources/views/vendor/pagination/dx-modern.blade.php), [dx-simple.blade.php](file:///z:/DX-License-Manager/backend/resources/views/vendor/pagination/dx-simple.blade.php), [dx-jump.blade.php](file:///z:/DX-License-Manager/backend/resources/views/vendor/pagination/dx-jump.blade.php) - Limpieza total de estilos inline y Javascript events en las 4 plantillas de paginación del portal.
    >
    > **Área Visual Afectada:**
    > - Envoltura global de la app, banner de mantenimiento persistente en la cabecera, pie de página del portal y controles interactivos de paginación en todas las listas de datos (Siemens, Moldex3D, Logs, Clientes, Usuarios, etc.).

---

## 🔷 [3] AUTH

- [x] **Subfase 19.3** — Login & Auth (login, forgot-password, reset, 2FA) ✅ COMPLETADA
    - [x] Extracción y Namespace `.dx-v2-`.
    - [x] Limpieza de `style=` inline.
    - [x] Verificación visual (Light/Dark) y Responsive.

    > **Archivos Afectados:**
    > - [dx-styles.css](file:///z:/DX-License-Manager/backend/public/assets/css/dx-styles.css) - Refactorizadas clases de login bajo el namespace unificado `.dx-v2-login-*` y añadida la clase de alerta de error `.dx-v2-login-error` con design tokens.
    > - [login.blade.php](file:///z:/DX-License-Manager/backend/resources/views/auth/login.blade.php) - Migración a clases namespaced y eliminación total del bloque de estilos inline de la alerta.
    >
    > **Área Visual Afectada:**
    > - Interfaz completa de la pantalla de inicio de sesión (diseño corporativo responsive, envoltura del formulario, campos de entrada, botones de acción, selector del interruptor del tema claro/oscuro y banner de visualización de errores del portal).

---

## 🔷 [4] INICIO / DASHBOARD

- [x] **Subfase 19.4** — Inicio & Dashboard (home, métricas, stats, widgets) ✅ COMPLETADA
    - [x] Extracción y Namespace `.dx-v2-`.
    - [x] Limpieza de `style=` inline.
    - [x] Verificación visual (Light/Dark) y Responsive.

    > **Archivos Afectados:**
    > - [dx-styles.css](file:///z:/DX-License-Manager/backend/public/assets/css/dx-styles.css) - Añadidas clases namespaced `.dx-v2-dashboard-*` y utilidades de color `.dx-v2-color-*` mapeando colores contractuales.
    > - [dashboard.blade.php](file:///z:/DX-License-Manager/backend/resources/views/dashboard.blade.php) - Limpieza total de estilos inline y JavaScript inline events (`onfocus`/`onblur`), integrando clases namespaced y selectores `:focus` nativos.
    >
    > **Área Visual Afectada:**
    > - Tarjetas de estadísticas de la cabecera (con íconos rotados traslúcidos), Buscador Global Express interactivo, tabla de vencimientos próximos de licencias y listado dinámico de contratos del panel lateral.

---

## 🔷 [5] CLIENTES

- [x] **Subfase 19.5** — Clientes: Vista principal (`index`, `show`) ✅ COMPLETADA
    - [x] Extracción y Namespace `.dx-v2-`.
    - [x] Limpieza de `style=` inline.
    - [x] Verificación visual (Light/Dark) y Responsive.

    > **Archivos Afectados:**
    > - [dx-styles.css](file:///z:/DX-License-Manager/backend/public/assets/css/dx-styles.css) - Añadidas utilidades globales `.text-xs` y clases de componentes semánticas `.dx-v2-clients-db-icon` y `.dx-v2-clients-empty-state`.
    > - [index.blade.php](file:///z:/DX-License-Manager/backend/resources/views/clients/index.blade.php) - Limpieza total de estilos locales, clases de utilidad obsoletas y corrección del bug estructural de colspan de la celda de la tabla de clientes.
    >
    > **Área Visual Afectada:**
    > - Listado de clientes, envoltura del estado vacío cuando no hay resultados (ahora centrado y expandido a lo largo de las 5 columnas simétricamente), ícono de la advertencia de base de datos de licencias y subtítulo de la página principal.

- [x] **Subfase 19.6** — Clientes: Licencias (inventario unificado) ✅ COMPLETADA
    - [x] Extracción y Namespace `.dx-v2-`.
    - [x] Limpieza de `style=` inline.
    - [x] Verificación visual (Light/Dark) y Responsive.

    > **Archivos Afectados:**
    > - [dx-styles.css](file:///z:/DX-License-Manager/backend/public/assets/css/dx-styles.css) - Corregidas variables CSS rotas (`--dx-v2-surface-raised` y `--dx-v2-text-muted`) por sus equivalentes unificados, y añadida directiva `[x-cloak]` global.
    > - [show.blade.php](file:///z:/DX-License-Manager/backend/resources/views/clients/show.blade.php) - Limpieza total de estilos inline locales `style="display: none;"` en tabs Alpine y modal, migrándolos al estándar `x-cloak`.
    >
    > **Área Visual Afectada:**
    > - Pestaña de Inventario Activo (Licencias) en la ficha detallada del cliente, pestañas secundarias (Certificados, Contactos, Renovaciones) y modal interactivo de gestión de contactos corporativos.

- [x] **Subfase 19.7** — Clientes: Contratos / ContraHeaders (importación CSV) ✅ COMPLETADA
    - [x] Extracción y Namespace `.dx-v2-`.
    - [x] Limpieza de `style=` inline.
    - [x] Verificación visual (Light/Dark) y Responsive.

    > **Archivos Afectados:**
    > - [dx-styles.css](file:///z:/DX-License-Manager/backend/public/assets/css/dx-styles.css) - Añadido el namespace `.dx-v2-import-*` para cubrir Dropzones, protocolo de mapeo, alertas de éxito y componentes de logs.
    > - [index.blade.php](file:///z:/DX-License-Manager/backend/resources/views/admin/import/index.blade.php) - Limpieza total de estilos inline y mapeo a clases del namespace.
    > - [logs/index.blade.php](file:///z:/DX-License-Manager/backend/resources/views/admin/import/logs/index.blade.php) - Eliminación del bloque de estilos local `<style>` incrustado e inline styles de botones.
    > - [logs/show.blade.php](file:///z:/DX-License-Manager/backend/resources/views/admin/import/logs/show.blade.php) - Eliminación del 100% de los estilos inline locales del breadcrumb, tarjetas de estadísticas y tablas de metadatos.
    >
    > **Área Visual Afectada:**
    > - Panel principal de Importación de Datos, historial de importaciones (logs) y detalle detallado del log de auditoría del sistema de contratos.


- [x] **Subfase 19.8** — Clientes: Contactos de envío & Certificados de cese (CODs) ✅ COMPLETADA
    - [x] Extracción y Namespace `.dx-v2-`.
    - [x] Limpieza de `style=` inline.
    - [x] Verificación visual (Light/Dark) y Responsive.

    > **Archivos Afectados:**
    > - [dx-styles.css](file:///z:/DX-License-Manager/backend/public/assets/css/dx-styles.css) - Añadido el namespace `.dx-v2-cod-*` para cubrir el generador de COD, el cargador y previsualizador de certificados de cese firmados y el asistente inteligente de análisis de hardware.
    > - [cod.blade.php](file:///z:/DX-License-Manager/backend/resources/views/tools/cod.blade.php) - Eliminación total del bloque de estilos local `<style>` incrustado (más de 850 líneas de CSS duplicado) e inline styles del formulario y del asistente IA, migrando todo al namespace.
    > - [show.blade.php](file:///z:/DX-License-Manager/backend/resources/views/clients/show.blade.php) - Auditoría y verificación de las pestañas de contactos y certificados COD, confirmando la ausencia de estilos locales y unificación bajo el estándar visual.
    >
    > **Área Visual Afectada:**
    > - Generador de Certificados de Cese (COD), asistente de Composite por IA, envoltura interactiva para arrastrar ficheros y listados de contactos corporativos.


- [x] **Subfase 19.9** — Planificador de Renovaciones ✅ COMPLETADA
    - [x] Extracción y Namespace `.dx-v2-`.
    - [x] Limpieza de `style=` inline y eventos mouseover/mouseout locales.
    - [x] Verificación visual (Light/Dark) y Responsive.

    > **Archivos Afectados:**
    > - [dx-styles.css](file:///z:/DX-License-Manager/backend/public/assets/css/dx-styles.css) - Incorporado el namespace `.dx-v2-planner-*` con clases estructurales y decorativas completas para el planificador, cabecera flexible, dropdown de mes custom, chips interactivos de estado y cuadrícula de alta densidad.
    > - [index.blade.php](file:///z:/DX-License-Manager/backend/resources/views/renewal-planner/index.blade.php) - Refactorización íntegra purgando más de 60 atributos de estilos inline, eliminando controladores `onmouseover` / `onmouseout` e implementando variables de entorno dinámicas CSS.
    >
    > **Área Visual Afectada:**
    > - Planificador de Renovaciones, selector interactivo de meses, chips segmentados de filtrado y tabla de contratos activos.

---

## 🔷 [6] HERRAMIENTAS

- [x] **Subfase 19.10** — Herramientas: Vista general / índice ✅ COMPLETADA
    - [x] Extracción y Namespace `.dx-v2-`.
    - [x] Limpieza de `style=` inline y remoción del bloque style local.
    - [x] Verificación visual (Light/Dark) y Responsive.

    > **Archivos Afectados:**
    > - [dx-styles.css](file:///z:/DX-License-Manager/backend/public/assets/css/dx-styles.css) - Incorporado el namespace `.dx-v2-tools-*` cubriendo tarjetas de herramientas, layouts responsivos, estados bloqueados, placeholders e iconos dinámicos.
    > - [index.blade.php](file:///z:/DX-License-Manager/backend/resources/views/tools/index.blade.php) - Refactorización completa purgando el bloque `<style>` incrustado y todos los estilos inline redundantes, delegando la interactividad y colores a variables CSS y clases globales.
    >
    > **Área Visual Afectada:**
    > - Hub de Herramientas, tarjetas de tecnologías (Siemens PLM, Moldex3D y Documentación), estados bloqueados y hover tridimensional.

---

## 🔷 [7] PÁGINAS DE HERRAMIENTAS & RECURSOS

- [x] **Subfase 19.11** — Siemens: NX Suite (ugslmd, saltd) ✅ COMPLETADA
    - [x] Extracción y Namespace `.dx-v2-`.
    - [x] Limpieza de `style=` inline.
    - [x] Verificación visual (Light/Dark) y Responsive.

    > **Archivos Afectados:**
    > - [dx-styles.css](file:///z:/DX-License-Manager/backend/public/assets/css/dx-styles.css) - Incorporado el namespace `.dx-v2-tools-nx-*` cubriendo tarjetas de motor, dropzone de arrastre, grids de especificaciones y paneles laterales.
    > - [nx-suite.blade.php](file:///z:/DX-License-Manager/backend/resources/views/tools/nx-suite.blade.php) - Refactorización completa purgando el 100% de los estilos inline locales, delegando la interactividad y colores a variables CSS y clases globales.
    >
    > **Área Visual Afectada:**
    > - Vista individual de herramienta NX Suite, selector de motor (Legacy vs SALT), dropzone de arrastre e información técnica del vendor.

- [ ] **Subfase 19.12** — Siemens: STAR-CCM+ (cdlmd)
    - [ ] Extracción y Namespace `.dx-v2-`.
    - [ ] Limpieza de `style=` inline.
    - [ ] Verificación visual (Light/Dark) y Responsive.

- [ ] **Subfase 19.13** — Siemens: HEEDS (RCTECH)
    - [ ] Extracción y Namespace `.dx-v2-`.
    - [ ] Limpieza de `style=` inline.
    - [ ] Verificación visual (Light/Dark) y Responsive.

- [ ] **Subfase 19.14** — Siemens: COD (Generador + Asistente IA)
    - [ ] Extracción y Namespace `.dx-v2-`.
    - [ ] Limpieza de `style=` inline.
    - [ ] Verificación visual (Light/Dark) y Responsive.

- [ ] **Subfase 19.15** — Siemens: Recursos & enlaces 
    - [ ] Definir estructura de vista antes de aplicar namespace.
    - [ ] Extracción y Namespace `.dx-v2-`.
    - [ ] Limpieza de `style=` inline.

- [ ] **Subfase 19.16** — Moldex3D (Parser .mac + Sincronización)
    - [ ] Extracción y Namespace `.dx-v2-`.
    - [ ] Limpieza de `style=` inline.
    - [ ] Verificación visual (Light/Dark) y Responsive.

- [ ] **Subfase 19.17** — Moldex3D: Recursos & enlaces 
    - [ ] Definir estructura de vista antes de aplicar namespace.
    - [ ] Extracción y Namespace `.dx-v2-`.
    - [ ] Limpieza de `style=` inline.

---

## 🔷 [8] SISTEMA & CONFIGURACIÓN

- [ ] **Subfase 19.18** — Dashboard del Sistema (NOC Pro + Brand Icons)
    - [ ] Extracción y Namespace `.dx-v2-`.
    - [ ] Limpieza de `style=` inline.
    - [ ] Verificación visual (Light/Dark) y Responsive.

- [ ] **Subfase 19.19** — Usuarios y acceso (listado, crear/editar, roles y permisos)
    - [ ] Extracción y Namespace `.dx-v2-`.
    - [ ] Limpieza de `style=` inline.
    - [ ] Verificación visual (Light/Dark) y Responsive.

- [ ] **Subfase 19.20** — Datos e importación (importar CSV, historial, errores)
    - [ ] Extracción y Namespace `.dx-v2-`.
    - [ ] Limpieza de `style=` inline.
    - [ ] Verificación visual (Light/Dark) y Responsive.

- [ ] **Subfase 19.21** — Repositorio de licencias (archivo semanal, historial)
    - [ ] Extracción y Namespace `.dx-v2-`.
    - [ ] Limpieza de `style=` inline.
    - [ ] Verificación visual (Light/Dark) y Responsive.

- [ ] **Subfase 19.22** — Alertas y notificaciones (caducidad, umbrales, destinatarios, historial, SMTP)
    - [ ] Extracción y Namespace `.dx-v2-`.
    - [ ] Limpieza de `style=` inline.
    - [ ] Verificación visual (Light/Dark) y Responsive.

- [ ] **Subfase 19.23** — Backups (manual, historial, configuración automática)
    - [ ] Extracción y Namespace `.dx-v2-`.
    - [ ] Limpieza de `style=` inline.
    - [ ] Verificación visual (Light/Dark) y Responsive.

- [ ] **Subfase 19.24** — Integraciones IA (Gemini, Deepseek, OpenRouter, Telegram Bot, estado de conexión)
    - [ ] Extracción y Namespace `.dx-v2-`.
    - [ ] Limpieza de `style=` inline.
    - [ ] Verificación visual (Light/Dark) y Responsive.

- [ ] **Subfase 19.25** — Logs y auditoría (actividad, errores, auditoría IA)
    - [ ] Extracción y Namespace `.dx-v2-`.
    - [ ] Limpieza de `style=` inline.
    - [ ] Verificación visual (Light/Dark) y Responsive.

---

## 🔷 VISTAS ESPECIALES

- [ ] **Subfase 19.26** — Páginas de Error (`errors/`: 403, 404, 419, 500, 503)
    - [ ] Extracción y Namespace `.dx-v2-`.
    - [ ] Limpieza de `style=` inline.
    - [ ] Verificación visual (Light/Dark).

---

## 🔷 COMPONENTES COMPARTIDOS

- [ ] **Subfase 19.27** — Componentes de Formulario (inputs, selects, textareas, checkboxes, radios, file uploads)
    - [ ] Extracción y Namespace `.dx-v2-`.
    - [ ] Limpieza de `style=` inline.
    - [ ] Verificación de estados: default, focus, disabled, error, readonly.
    - [ ] Verificación visual (Light/Dark) y Responsive.

- [ ] **Subfase 19.28** — Componentes UI (Modales, Tablas, Badges, Botones, Toasts/Alerts)
    - [ ] Extracción y Namespace `.dx-v2-`.
    - [ ] Limpieza de `style=` inline.
    - [ ] Verificación visual (Light/Dark) y Responsive.

---

## 🔷 EXCLUSIONES DOCUMENTADAS

- [ ] **Subfase 19.29** — Revisión (no refactor) de Emails & PDFs
    - [ ] Inventariar `style=` inline existente y justificar excepción.
    - [ ] Verificar que no hereden variables `--dx-v2-*` que se rompan en clientes de correo.
    - [ ] Registrar excepciones en CHANGELOG de la fase.

---

## 🔷 DOCUMENTACIÓN DE CÓDIGO

### En los archivos CSS

Header obligatorio en cada hoja nueva o refactorizada:

```css
/**
 * @module    dx-v2-[nombre]
 * @fase      19.[N]
 * @desc      [Qué cubre este archivo]
 * @afecta    [Vistas / componentes que lo consumen]
 * @version   2.19.0
 */
```

Comentario de sección para cada bloque temático:

```css
/* ─── Sidebar nav items ──────────────────────────────── */
/* ─── Responsive breakpoints ────────────────────────── */
/* ─── Dark mode overrides ───────────────────────────── */
```

Justificación obligatoria en cada `!important` que sobreviva al hardening:

```css
/* !important: sobrescribe librería vendor Bootstrap [componente X] */
```

### En los Blade components

Comentario en componentes que usen clases `.dx-v2-` no obvias:

```blade
{{-- dx-v2-badge--expired: estado caducado, ver resources/css/badges.css --}}
```

---

## 🔷 CIERRE & HARDENING

- [ ] Eliminación de `!important` sin comentario justificado.
- [ ] Eliminación de variables `--dx-v2-*` huérfanas post-refactor.
- [ ] Verificación Final: `grep -r 'style=' resources/views/` → cero resultados (excl. emails/pdf).
- [ ] Verificación build: clases dinámicas no purgadas por Vite.

---

## 🔷 DOCUMENTACIÓN FINAL

- [ ] Style guide interno: inventario de componentes `.dx-v2-` con ejemplos de uso.
- [ ] README de la fase: decisiones tomadas, excepciones justificadas, variables deprecadas.
- [ ] Actualizar CHANGELOG del proyecto.

---

## ✅ Criterios de Aceptación

| Criterio | Check |
|---|---|
| Zero `style=` en `resources/views/` (excl. emails/pdf) | [ ] |
| Zero clases sin namespace `.dx-v2-` en hojas nuevas | [ ] |
| Zero variables huérfanas post-refactor | [ ] |
| Zero `!important` sin comentario justificado | [ ] |
| Verificación visual OK — Light & Dark mode | [ ] |
| Verificación Responsive OK (mobile / tablet / desktop) | [ ] |
| Build Vite sin purge de clases dinámicas | [ ] |
| Style guide interno entregado | [ ] |
| CHANGELOG actualizado | [ ] |
| Tag `v2.19.0` pusheado al remoto desde `dev` | [ ] |

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
| SSL            | Cloudflare                     |
| Inicio y Gestión de Clientes/Contratos          |
| Automatización | n8n                            |
| Auditoría IA   | Gemini → DeepSeek → OpenRouter |
| Notificaciones | Telegram                       |
---

