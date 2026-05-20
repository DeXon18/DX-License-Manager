# 🛠️ Error Tracking — DX License Manager

> Registro centralizado de bugs, errores de UI y discrepancias técnicas detectadas durante el uso de la plataforma.

---

## 📊 Estado de la Flota

| 🔴 Críticos (P1) | 🟠 Importantes (P2) | 🟢 Menores (P3) | ✅ Resueltos |
| :---: | :---: | :---: | :---: |
| 0 | 1 | 3 | 16 |

---

## 📋 Índice rápido

- [⏳ Pendientes](#-pendientes)
- [✅ Resueltos](#-resueltos)
- [🔍 Detalle de incidencias](#-detalle-de-incidencias)
  - [Pendientes](#pendientes)
  - [Resueltos](#resueltos-1)
- [🛡️ Protocolo de resolución](#️-protocolo-de-resolución)

---

## ⏳ Pendientes

| ID | Incidencia | Módulo | Prioridad | Estado | Detectado |
|---:|---|---|:---:|:---:|:---:|
| [#020] Mensajes de confirmación sin feedback visual destacado (toasts) | UI/UX | 🟠 P2 | 🔍 En análisis | 2026-05-19 |
| [#017] Barra de búsqueda sin estilos en Gestión de Usuarios | UI/UX | 🟢 P3 | 🔍 En análisis | 2026-05-19 |
| [#018] Bloque derecho rompe layout en Gestión de Importación | UI/UX | 🟢 P3 | 🔍 En análisis | 2026-05-19 |
| [#019] Campo "Copia Interna (Emails)" ilegible por fondo oscuro | UI/UX | 🟢 P3 | 🔍 En análisis | 2026-05-19 |

---

## ✅ Resueltos

| ID | Incidencia | Módulo | Prioridad | Detectado | Resuelto |
|---:|---|---|:---:|:---:|:---:|
| [#016] Filtro "Sin estado" no filtra en Planificador de Renovaciones | Renovaciones | 🟠 P2 | 2026-05-19 | 2026-05-20 |
| [#008] Unificación de estilos CSS en archivo central | UI/UX | 🟢 P3 | 2026-05-14 | 2026-05-19 |
| [#015] Fallo en vista previa COD (nesting HTML roto) | Herramientas | 🔴 P1 | 2026-05-16 | 2026-05-16 |
| [#013] Invisibilidad de licencias Moldex3D en inventario | Inventario | 🔴 P1 | 2026-05-15 | 2026-05-15 |
| [#014] Expiración prematura de sesión JWT | Auth/JWT | 🟠 P2 | 2026-05-15 | 2026-05-15 |
| [#012] RedisException: MISCONF — persistencia fallida | Infra/Redis | 🔴 P1 | 2026-05-15 | 2026-05-15 |
| [#011] Transformación de licencia NX falla — no descarga/procesa | Siemens NX | 🔴 P1 | 2026-05-15 | 2026-05-15 |
| [#010] Indicadores de seguridad siempre a 0 | Dashboard | 🟠 P2 | 2026-05-15 | 2026-05-15 |
| [#007] Fallo en normalización / duplicidad de clientes | Normalización | 🟠 P2 | 2026-05-14 | 2026-05-15 |
| [#005] Mejora en lector de logs `laravel.log` | Admin/Logs | 🟠 P2 | 2026-05-15 | 2026-05-15 |
| [#003] Filtro "Solo con Licencias" limitado a Siemens | Clientes | 🟠 P2 | 2026-05-14 | 2026-05-15 |
| [#006] Acciones rápidas sin vínculos / estáticas | Dashboard | 🟢 P3 | 2026-05-14 | 2026-05-15 |
| [#004] Revisar visualización de "Other Installs" | UI/UX | 🟢 P3 | 2026-05-14 | 2026-05-15 |
| [#009] Limpieza de archivos basura y registros huérfanos | Sistema | 🟢 P3 | 2026-05-14 | 2026-05-15 |
| [#002] Error de sintaxis y CRLF en `backup-db.sh` | Infra/Scripts | 🔴 P1 | 2026-05-14 | 2026-05-14 |
| [#001] [Ejemplo] Error de contraste en modo claro | UI/UX | 🟢 P3 | 2026-05-14 | 2026-05-14 |

---

### Leyenda de prioridad

| Prioridad | Significado |
|:---:|---|
| 🔴 P1 | Alta — impacto crítico o bloqueo funcional |
| 🟠 P2 | Media — afecta a funcionalidades importantes |
| 🟢 P3 | Baja — mejora, revisión visual o tarea no bloqueante |

---

## 🔍 Detalle de incidencias

### Pendientes

---

#### #020 — Mensajes de confirmación sin feedback visual destacado

| Campo | Valor |
|---|---|
| **Módulo** | UI/UX — Global |
| **Prioridad** | 🟠 P2 |
| **Estado** | 🔍 En análisis |
| **Detectado** | 2026-05-19 |

- **Síntoma**: Los mensajes de éxito como *"El reporte semanal se ha generado y enviado a los contactos suscritos"* aparecen en una posición poco visible, sin destacar suficientemente la acción completada.
- **Causa probable**: Implementación inicial de alertas inline estáticas sin un sistema de notificaciones centralizado.
- **Impacto**: El usuario puede no percibir el feedback de acciones completadas, generando dudas sobre si la operación se ejecutó correctamente, con riesgo de repetir acciones por error.
- **Acción**:
  - Implementar un sistema de **toast/snackbar flotante** (esquina inferior o superior derecha) para todos los mensajes de confirmación, éxito, error y advertencia.
  - Estandarizar el componente para que sea reutilizable en todos los módulos y sustituya las alertas inline actuales.
  - Definir duración de auto-cierre (ej. 4s para éxito, persistente para error) y soporte para cierre manual.
- **Módulos afectados**: Reportes, Alertas y Notificaciones, Gestión de Usuarios, Importación, Dashboard.

---

#### #017 — Barra de búsqueda sin estilos en Gestión de Usuarios

| Campo | Valor |
|---|---|
| **Módulo** | UI/UX |
| **Prioridad** | 🟢 P3 |
| **Estado** | 🔍 En análisis |
| **Detectado** | 2026-05-19 |

- **Síntoma**: El campo de búsqueda de la vista de Gestión de Usuarios aparece sin estilos aplicados (input nativo del navegador, sin integración visual con el sistema de diseño DX).
- **Causa probable**: El componente no hereda las clases del sistema de diseño centralizado o quedó fuera de la migración CSS del [#008](#008--unificación-de-estilos-css-en-archivo-central).
- **Impacto**: Inconsistencia visual, degradación de la experiencia de usuario.
- **Acción**: Aplicar las clases de `dx-v2-main.css` al input de búsqueda de esta vista.

---

#### #018 — Bloque derecho rompe layout en Gestión de Importación

| Campo | Valor |
|---|---|
| **Módulo** | UI/UX |
| **Prioridad** | 🟢 P3 |
| **Estado** | 🔍 En análisis |
| **Detectado** | 2026-05-19 |

- **Síntoma**: En la vista de Gestión de Importación, el bloque derecho (panel lateral) se renderiza por debajo del bloque central en lugar de aparecer a su derecha en un layout de columnas.
- **Causa probable**: Fallo en la rejilla CSS (grid/flex) del layout de dos columnas, posiblemente por clases faltantes o conflicto tras la refactorización CSS del [#008](#008--unificación-de-estilos-css-en-archivo-central).
- **Impacto**: Layout roto que obliga al usuario a hacer scroll innecesario y dificulta la usabilidad.
- **Acción**: Revisar la estructura Blade y las clases de layout de la vista de Importación para restaurar la disposición en columnas.

---

#### #019 — Campo "Copia Interna (Emails)" ilegible por fondo oscuro

| Campo | Valor |
|---|---|
| **Módulo** | UI/UX |
| **Prioridad** | 🟢 P3 |
| **Estado** | 🔍 En análisis |
| **Detectado** | 2026-05-19 |

- **Síntoma**: En Alertas y Notificaciones, el campo "Copia Interna (Emails)" presenta un fondo oscuro que hace que los emails introducidos sean invisibles o muy difíciles de leer.
- **Causa probable**: Conflicto de variables CSS entre el tema del formulario y los estilos heredados del campo — posible `background-color` o `color` hardcodeado que no respeta las variables del sistema de diseño.
- **Impacto**: El usuario no puede verificar los emails introducidos, con riesgo de errores en la configuración de notificaciones.
- **Acción**: Corregir el color de texto y/o fondo usando las variables CSS del sistema (`dx-v2-main.css`) para garantizar el contraste adecuado.

---

### Resueltos

---

#### #016 — Filtro "Sin estado" no filtra en Planificador de Renovaciones

| Campo | Valor |
|---|---|
| **Módulo** | Renovaciones |
| **Prioridad** | 🟠 P2 |
| **Estado** | ✅ Resuelto |
| **Detectado** | 2026-05-19 |
| **Resuelto** | 2026-05-20 |

- **Síntoma**: Al seleccionar el filtro "Sin estado" en el Planificador de Renovaciones, no se devuelve ningún resultado a pesar de existir registros sin estado asignado.
- **Causa**: El filtro buscaba la cadena literal `"Sin estado"` o no mapeaba `null` en `availableStatuses`, mientras que en BD los contratos sin estado se almacenan con valor `NULL`.
- **Resolución**: Se modificó `availableStatuses` en `RenewalPlannerController.php` para mapear `null`/`""` a `""` (que se muestra como "Sin estado" en el Blade). Se actualizó la query Eloquent para filtrar de forma robusta con `orWhereNull('status')->orWhere('status', '')` cuando se selecciona la opción sin estado.

---

#### #015 — Fallo en vista previa COD (nesting HTML roto)

| Campo | Valor |
|---|---|
| **Módulo** | Herramientas |
| **Prioridad** | 🔴 P1 |
| **Estado** | ✅ Resuelto |
| **Detectado** | 2026-05-16 |
| **Resuelto** | 2026-05-16 |

---

#### #008 — Unificación de estilos CSS en archivo central

| Campo | Valor |
|---|---|
| **Módulo** | UI/UX |
| **Prioridad** | 🟢 P3 |
| **Estado** | ✅ Resuelto |
| **Detectado** | 2026-05-14 |
| **Resuelto** | 2026-05-19 |

- **Síntoma**: Estilos dispersos en archivos Blade y CSS secundarios dificultaban el mantenimiento global.
- **Causa**: Crecimiento orgánico del proyecto y personalizaciones ad-hoc en vistas específicas.
- **Resolución**:
  - Unificación y modularización en 35 archivos CSS independientes, organizados en 6 capas funcionales bajo `backend/public/assets/css/`.
  - Toda la importación consolidada en el archivo maestro `dx-v2-main.css`.
  - Purgado el monolito legacy `dx-styles.css` de disco y Git.
  - Actualizados todos los layouts Blade (`app.blade.php`, `login.blade.php`, `503.blade.php`) con cache-buster dinámico.

---

#### #014 — Expiración prematura de sesión JWT

| Campo | Valor |
|---|---|
| **Módulo** | Auth/JWT |
| **Prioridad** | 🟠 P2 |
| **Estado** | ✅ Resuelto |
| **Detectado** | 2026-05-15 |
| **Resuelto** | 2026-05-15 |

- **Síntoma**: El sistema cerraba la sesión antes de cumplirse los 15 minutos de inactividad configurados, redirigiendo al usuario al login sin previo aviso.
- **Causa probable**:
  - Desincronización entre el TTL del token JWT y el tiempo de vida de la cookie.
  - El middleware de rotación de Refresh Tokens invalidaba el token activo al detectar peticiones AJAX simultáneas.
  - `SESSION_LIFETIME` en Laravel con valor inferior al esperado.
- **Resolución**:
  - Identificada desincronización de secretos entre `backend/.env` e `infra/.env.beta` (causa raíz).
  - Implementada **Rotación Inteligente** de tokens con cool-off de 5 min para evitar colisiones AJAX.
  - Ventana de gracia en Redis ampliada a 120s.
  - TTL de sesión aumentado a 60 min.

---

#### #013 — Invisibilidad de licencias Moldex3D en inventario

| Campo | Valor |
|---|---|
| **Módulo** | Inventario |
| **Prioridad** | 🔴 P1 |
| **Estado** | ✅ Resuelto |
| **Detectado** | 2026-05-15 |
| **Resuelto** | 2026-05-15 |

- **Síntoma**: No se detectaban licencias activas de Moldex3D en el inventario. El conteo de daemons devolvía 0 para `moldex_daemons_count`.
- **Causa probable**:
  - Desconexión entre `AiAuditResult` y la persistencia en `LicenseInventoryDaemon`.
  - Fallo en el mapeo semántico del nombre del cliente en `MoldexSyncService`.
  - El daemon name `moldex3d` podría no ser el único usado en archivos `.mac`.
- **Resolución**: Refactorizado `MoldexSyncService` para usar `ClientNormalizationService` (Alias y Fuzzy Matching). `MoldexController` blindado para retornar HTTP 422 si falla la persistencia.

---

#### #012 — RedisException: MISCONF (Persistencia fallida)

| Campo | Valor |
|---|---|
| **Módulo** | Infra/Redis |
| **Prioridad** | 🔴 P1 |
| **Estado** | ✅ Resuelto |
| **Detectado** | 2026-05-15 |
| **Resuelto** | 2026-05-15 |

- **Síntoma**: El portal devolvía error 500 — `MISCONF Redis is configured to save RDB snapshots, but it's currently unable to persist to disk`. Login, acciones y auditoría bloqueados.
- **Causa**: El proceso `BGSAVE` de Redis fallaba por permisos incorrectos en el volumen de datos (`/data` pertenecía a root).
- **Resolución**:
  - Aplicado `chown redis:redis /data` en caliente para restaurar el guardado RDB.
  - Securizada la configuración con **volúmenes nombrados** en `docker-compose.beta.yml` y `docker-compose.prod.yml`.
  - Verificado `BGSAVE` exitoso y restaurada la política `stop-writes-on-bgsave-error yes`.

---

#### #011 — Transformación de licencia NX falla (no descarga/procesa)

| Campo | Valor |
|---|---|
| **Módulo** | Siemens NX |
| **Prioridad** | 🔴 P1 |
| **Estado** | ✅ Resuelto |
| **Detectado** | 2026-05-15 |
| **Resuelto** | 2026-05-15 |

- **Síntoma**: Al transformar una licencia NX el sistema no descargaba el archivo ni procesaba la lógica de transformación.
- **Causa probable**: Inconsistencia en el stream de descarga o fallo en lógica Multi-Sold-To.
- **Resolución**:
  - Corregido el flujo de descarga en el controlador, evitando bloqueos por peticiones AJAX asíncronas.
  - Implementada gestión de memoria avanzada (`256M`) y `try-catch` con degradación elegante.
  - Validado soporte para extensiones `.dat` y `.cid`.
  - Añadida validación UI con Alpine.js para feedback inmediato.

---

#### #010 — Indicadores de seguridad siempre a 0

| Campo | Valor |
|---|---|
| **Módulo** | Dashboard |
| **Prioridad** | 🟠 P2 |
| **Estado** | ✅ Resuelto |
| **Detectado** | 2026-05-15 |
| **Resuelto** | 2026-05-15 |

- **Síntoma**: Los contadores de "Errores Críticos", "Logins Fallidos" y "JWT Blacklist" siempre mostraban 0, incluso tras forzar eventos de prueba.
- **Causa**: `DashboardController` no consultaba las tablas correctas; el sistema no registraba login fallidos ni gestionaba la blacklist.
- **Resolución**:
  - Implementado registro de `login_failed` en `AuthController`.
  - Implementada gestión de `jwt_blacklist` en Redis (ZSET) en Logout y Middleware.
  - Sincronizados niveles de error (`warning` vs `error`) en la telemetría del dashboard.

---

#### #007 — Fallo en normalización / duplicidad de clientes

| Campo | Valor |
|---|---|
| **Módulo** | Normalización |
| **Prioridad** | 🟠 P2 |
| **Estado** | ✅ Resuelto |
| **Detectado** | 2026-05-14 |
| **Resuelto** | 2026-05-15 |

- **Síntoma**: Clientes duplicados con nombres similares no agrupados automáticamente (ej: "Fundacion Tecnalia" vs "Fundación Tecnalia Research & Innovation").
- **Causa**: Umbral del motor de normalización (85%) insuficiente para variaciones largas.
- **Resolución**: Implementado motor de búsqueda semántica en `ClientNormalizationService`. Integrada lógica de Alias y Fuzzy Matching en todos los controladores de sincronización.

---

#### #005 — Mejora en lector de logs (laravel.log)

| Campo | Valor |
|---|---|
| **Módulo** | Admin/Logs |
| **Prioridad** | 🟠 P2 |
| **Estado** | ✅ Resuelto |
| **Detectado** | 2026-05-15 |
| **Resuelto** | 2026-05-15 |

- **Síntoma**: El lector de logs (`admin/audit?tab=system`) mostraba trazas completas ilegibles y el contador de alertas en 0.
- **Causa**: El parser de `AuditLogController` solo mostraba texto plano sin estructurar.
- **Resolución**: Parser Regex en backend para estructurar logs. UI interactiva con Alpine.js (trazas colapsables). Contador de alertas sincronizado unificando DB + fichero físico.

---

#### #003 — Filtro "Solo con Licencias" limitado a Siemens

| Campo | Valor |
|---|---|
| **Módulo** | Clientes |
| **Prioridad** | 🟠 P2 |
| **Estado** | ✅ Resuelto |
| **Detectado** | 2026-05-14 |
| **Resuelto** | 2026-05-15 |

- **Síntoma**: El filtro de licencias en Gestión de Clientes solo devolvía clientes con licencias Siemens, ignorando Moldex3D.
- **Causa**: La query en `ClientController` solo contaba `inventory_daemons` (Siemens) ignorando el flag Moldex3D.
- **Resolución**: Implementado switch de filtrado multi-vendor con conteo dinámico Siemens/Moldex.

---

#### #006 — Acciones rápidas sin vínculos / estáticas

| Campo | Valor |
|---|---|
| **Módulo** | Dashboard |
| **Prioridad** | 🟢 P3 |
| **Estado** | ✅ Resuelto |
| **Detectado** | 2026-05-14 |
| **Resuelto** | 2026-05-15 |

- **Síntoma**: Los botones del panel "Acciones Rápidas" no redirigían a ninguna parte ni tenían lógica funcional.
- **Causa**: Implementación centrada en el diseño visual (Bento) sin terminar de cablear enlaces y lógica de backend.
- **Resolución**:
  - Implementado **Buscador Global Express** con soporte para Sold-To, Machine ID y Nombre de Cliente.
  - Vinculadas acciones a herramientas reales: Generación de COD, Planificador de Renovaciones y Hub de Auditoría IA.
  - Implementado **Contador de Renovaciones Pendientes** del mes actual con badge dinámico.

---

#### #004 — Revisar visualización de "Other Installs"

| Campo | Valor |
|---|---|
| **Módulo** | UI/UX |
| **Prioridad** | 🟢 P3 |
| **Estado** | ✅ Resuelto |
| **Detectado** | 2026-05-14 |
| **Resuelto** | 2026-05-15 |

- **Síntoma**: Los Sold-To adicionales se mostraban como badges sin optimización para alta densidad de IDs.
- **Resolución**:
  - Rediseño "NOC Pro v2" con marca de agua técnica (`fa-network-wired`) al 4% de opacidad.
  - Sold-Tos adicionales movidos a una franja minimalista transparente bajo el header.
  - IDs resaltados en color crema/amarillo suave (`#fde68a`).

---

#### #009 — Limpieza de archivos basura y registros huérfanos

| Campo | Valor |
|---|---|
| **Módulo** | Sistema |
| **Prioridad** | 🟢 P3 |
| **Estado** | ✅ Resuelto |
| **Detectado** | 2026-05-14 |
| **Resuelto** | 2026-05-15 |

- **Síntoma**: Directorios temporales, archivos de backup antiguos y registros sin relación con identidades activas.
- **Resolución**:
  - Eliminada carpeta redundante `./storage` de la raíz; datos migrados a `backend/storage/app`.
  - Borrados archivos `.sql` y trazas de log antiguas.
  - Git configurado para ignorar el estado "dirty" de submódulos de diseño/skills.

---

#### #002 — Error de sintaxis y CRLF en backup-db.sh

| Campo | Valor |
|---|---|
| **Módulo** | Infra/Scripts |
| **Prioridad** | 🔴 P1 |
| **Estado** | ✅ Resuelto |
| **Detectado** | 2026-05-14 |
| **Resuelto** | 2026-05-14 |

- **Síntoma**: El script de backup fallaba al ejecutarse en el contenedor PHP.
- **Errores**:
  - `$'\r': command not found` — CRLF detectado.
  - `syntax error: unexpected end of file from 'if' command on line 27`.
- **Causa**: Finales de línea Windows (CRLF) que rompían la interpretación de Bash, y variables de entorno sin comillas.
- **Resolución**: Conversión a LF (Unix), limpieza de sintaxis en bloques `if` y blindaje de variables con comillas.

---

#### #001 — [Ejemplo] Error de contraste en modo claro

| Campo | Valor |
|---|---|
| **Módulo** | UI/UX |
| **Prioridad** | 🟢 P3 |
| **Estado** | ✅ Resuelto |
| **Detectado** | 2026-05-14 |
| **Resuelto** | 2026-05-14 |

---

## 🛡️ Protocolo de resolución

| Paso | Acción |
|:---:|---|
| 1 · Detección | El desarrollador anota el error en este archivo con ID correlativo |
| 2 · Triaje | El agente asigna prioridad y analiza la causa probable |
| 3 · Fix | Se crea una rama `fix/descripcion` para solucionar el problema |
| 4 · Verificación | Se comprueba el fix en el entorno Beta |
| 5 · Cierre | Se marca como `✅ Resuelto` y se registra la fecha de cierre |

---

*Firmado por: **Antigravity (DX Agent)** 🦾*