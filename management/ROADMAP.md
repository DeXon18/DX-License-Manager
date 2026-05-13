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
│   └── Certificados de cese firmados ✅ (Gestión de CODs)
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

### ✅ Fase 14 — Gestión de Backups ✅ COMPLETADA

- [x] Historial de backups en disco -> OK
- [x] Generación manual y automatizada (Cron) -> OK
- [x] Descarga y borrado de archivos -> OK
- [x] Sistema de restauración con seguridad crítica -> OK
- [x] UI con cuenta atrás dinámica y estados -> OK

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

### ✅ Fase 16 — Centro de Logs Unificado (Auditoría Pro)

**Estado:** COMPLETADA
**Validación:** ✅ Verificado por Oskar el 2026-05-12. Logs de sistema, actividad y email integrados con UI NOC Pro.

- [x] **Logs de Actividad**: Timeline detallado de acciones de usuarios con filtros avanzados.
- [x] **Logs de Sistema**: Visor nativo PHP de `laravel.log` (últimas 200 líneas) integrado en UI.
- [x] **Logs de Email**: Trazabilidad completa de correos enviados (destinatario, asunto, estado).
- [x] **Gestión de Logs**: Funcionalidad de Reset por sección con registro de evento de seguridad.
- [x] **UI/UX Premium**: Sistema de pestañas, indicadores NOC Pro y diseño oscuro optimizado.

---

### 📋 Fase 17 — Consolidación y Limpieza UI

**Estado:** Planificado (Fase Final de Bloque 8.1)

- [ ] Auditoría de estilos redundantes en vistas Blade
- [ ] Migración de componentes CSS de `show.blade.php` a `dx-styles.css`
- [ ] Optimización de selectores y reducción de especificidad innecesaria
- [ ] Verificación de consistencia visual en Mobile/Responsive tras la consolidación

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

