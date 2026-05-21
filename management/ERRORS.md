# 🛠️ Error Tracking — DX License Manager

> Registro centralizado de bugs, errores de UI y discrepancias técnicas detectadas durante el uso de la plataforma.

---

## 📊 Estado de la Flota

| 🔴 Críticos (P1) | 🟠 Importantes (P2) | 🟢 Menores (P3) | ✅ Resueltos |
| :---: | :---: | :---: | :---: |
| 0 | 0 | 0 | 22 |

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

*No hay incidencias activas pendientes. ¡El portal está 100% libre de errores!*

---

## ✅ Resueltos

| ID | Incidencia | Módulo | Prioridad | Detectado | Resuelto |
|---:|---|---|:---:|:---:|:---:|
| [#022] | Clientes duplicados ya persistidos no aparecen en la Bandeja de Normalización | Normalización | 🟠 P2 | 2026-05-20 | 2026-05-20 |
| [#020] Mensajes de confirmación sin feedback visual destacado (toasts) | UI/UX | 🟠 P2 | 2026-05-19 | 2026-05-20 |
| [#017] Barra de búsqueda sin estilos en Gestión de Usuarios | UI/UX | 🟢 P3 | 2026-05-19 | 2026-05-20 |
| [#021] Inputs sin estilos en Generador COD (herramientas/cod) | UI/UX | 🟠 P2 | 2026-05-20 | 2026-05-20 |
| [#019] Campo "Copia Interna (Emails)" ilegible por fondo oscuro | UI/UX | 🟢 P3 | 2026-05-19 | 2026-05-20 |
| [#018] Bloque derecho rompe layout en Gestión de Importación | UI/UX | 🟢 P3 | 2026-05-19 | 2026-05-20 |
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

*No hay incidencias pendientes.*

---

### Resueltos

---

#### #022 — Clientes duplicados ya persistidos no aparecen en la Bandeja de Normalización

| Campo | Valor |
|---|---|
| **Módulo** | Normalización / UI |
| **Prioridad** | 🟠 P2 |
| **Estado** | ✅ Resuelto |
| **Detectado** | 2026-05-20 |
| **Resuelto** | 2026-05-20 |

- **Síntoma**: Clientes que ya han sido guardados como registros separados e independientes en la base de datos (por ejemplo, `Tag Automotive S.l.` y `Tag Automotive(Nifco Products Espana,S.l.u.)`) no generan advertencias ni sospechas de duplicidad en las importaciones subsiguientes porque el motor encuentra coincidencias exactas para ambos. Por tanto, no aparecen en la Bandeja de Normalización y no hay forma de unificarlos desde allí.
- **Causa**: La Bandeja de Normalización solo se alimenta de advertencias o sospechas generadas durante el flujo de importación (cuando entra un nombre desconocido). Si los dos duplicados ya existen en la BD, la importación mapea cada uno con su correspondiente cliente ID sin emitir alertas.
- **Resolución**:
  - Habilitada una **vista de pestañas premium** organizada en Alpine.js: "Sospechas de Importación", "Escáner de Duplicados" y "Unificación Manual Libre".
  - Desarrollada la pestaña **Escáner de Duplicados** que ejecuta un algoritmo de similitud ortográfica en backend (Levenshtein y similar_text a nivel base) detectando duplicados latentes en BD.
  - Implementada **verificación semántica por IA interactiva** vía AJAX: un botón que consulta a Gemini sobre la similitud de la pareja de clientes en vivo.
  - Enriquecidas las opciones con el **ID del cliente** para permitir comparar visualmente qué ficha es más antigua/histórica.
  - Blindada la ruta de unificación atómica para extraer y parsear IDs con expresiones regulares, verificando e impidiendo auto-unificación.

---

---

#### #020 — Mensajes de confirmación sin feedback visual destacado

| Campo | Valor |
|---|---|
| **Módulo** | UI/UX — Global |
| **Prioridad** | 🟠 P2 |
| **Estado** | ✅ Resuelto |
| **Detectado** | 2026-05-19 |
| **Resuelto** | 2026-05-20 |

- **Síntoma**: Los mensajes de éxito como *"El reporte semanal se ha generado y enviado a los contactos suscritos"* aparecen en una posición poco visible, sin destacar suficientemente la acción completada.
- **Causa**: Implementación inicial de alertas inline estáticas que causaban duplicidad e inconsistencia visual.
- **Resolución**:
  - Implementado un motor de toasts reactivo y flotante con **Alpine.js** en [layouts/partials/toasts.blade.php](file:///y:/DX-License-Manager/backend/resources/views/layouts/partials/toasts.blade.php) que incluye autocierre, soporte rico en HTML (`x-html`) y helper global `window.dxToast()`.
  - Diseñada la hoja de estilos [shared/dx-v2-toast.css](file:///y:/DX-License-Manager/backend/public/assets/css/shared/dx-v2-toast.css) con soporte HSL y glassmorphism adaptativo claro/oscuro.
  - Eliminados todos los banners de alertas inline de los módulos principales (Usuarios, Alertas, Importación, Repositorio, Logs, Perfil, Normalización) delegando todo el feedback en el sistema unificado de toasts.

---

#### #017 — Barra de búsqueda sin estilos en Gestión de Usuarios

| Campo | Valor |
|---|---|
| **Módulo** | UI/UX |
| **Prioridad** | 🟢 P3 |
| **Estado** | ✅ Resuelto |
| **Detectado** | 2026-05-19 |
| **Resuelto** | 2026-05-20 |

- **Síntoma**: El campo de búsqueda de la vista de Gestión de Usuarios aparece sin estilos aplicados (input nativo del navegador, sin integración visual con el sistema de diseño DX).
- **Causa**: El componente no heredaba las clases del sistema de diseño centralizado al utilizar la clase legacy obsoleta `.gui-input`.
- **Resolución**:
  - Reemplazadas todas las referencias de `.gui-input` por `.dx-v2-form-input` y `.dx-v2-form-select` en `backend/resources/views/admin/users/index.blade.php`.
  - Enriquecida la clase `.dx-v2-users-filter-select` en `dx-v2-users.css` agregando la flecha premium SVG interactiva y configurando la caché.

---

#### #021 — Inputs sin estilos en Generador COD (herramientas/cod)

| Campo | Valor |
|---|---|
| **Módulo** | UI/UX |
| **Prioridad** | 🟠 P2 |
| **Estado** | ✅ Resuelto |
| **Detectado** | 2026-05-20 |
| **Resuelto** | 2026-05-20 |

- **Síntoma**: En la vista de Generador COD, los campos del formulario de solicitud (Sold-To, Solicitante, Empresa, Hostname, Composite, MAC) aparecen sin estilos visuales (cajas nativas del navegador blancas con texto negro ilegible en modo oscuro).
- **Causa**: Estos inputs siguen utilizando la clase obsoleta `.gui-input`, la cual fue eliminada del disco en el commit [#008] al purgar el CSS legacy `dx-styles.css`.
- **Resolución**:
  - Migradas todas las 11 referencias de `.gui-input` a la clase semántica unificada `.dx-v2-form-input` en `backend/resources/views/tools/cod.blade.php`.
  - Purgadas las cachés en el servidor beta y verificado el contraste dinámico correcto en modo oscuro.

---

#### #019 — Campo "Copia Interna (Emails)" ilegible por fondo oscuro

| Campo | Valor |
|---|---|
| **Módulo** | UI/UX |
| **Prioridad** | 🟢 P3 |
| **Estado** | ✅ Resuelto |
| **Detectado** | 2026-05-19 |
| **Resuelto** | 2026-05-20 |

- **Síntoma**: En Alertas y Notificaciones, el campo "Copia Interna (Emails)" presenta un fondo oscuro que hace que los emails introducidos sean invisibles o muy difíciles de leer.
- **Causa**: Conflicto de variables CSS entre el tema del formulario y los estilos heredados del campo — posible `background-color` o `color` heredado que no respeta las variables del sistema de diseño.
- **Resolución**:
  - Migrado el componente textarea de la clase obsoleta `.gui-input` a la clase unificada `.dx-v2-form-textarea` en `index.blade.php`.
  - Corregida la definición de la clase `.dx-v2-alerts-copy-textarea` en `dx-v2-alerts.css` vinculando el color de texto directamente a la variable global `--dx-v2-primary` para soportar contraste dinámico impecable.

---

#### #018 — Bloque derecho rompe layout en Gestión de Importación

| Campo | Valor |
|---|---|
| **Módulo** | UI/UX |
| **Prioridad** | 🟢 P3 |
| **Estado** | ✅ Resuelto |
| **Detectado** | 2026-05-19 |
| **Resuelto** | 2026-05-20 |

- **Síntoma**: El bloque derecho (panel lateral) se renderiza por debajo del bloque central en la vista de Gestión de Importación, y en todas las vistas de herramientas y recursos (`nx-suite`, `star-ccm`, `heeds`, `moldex3d`, `resources`).
- **Causa**: La clase de rejilla `.grid-main` y las clases `.main-panel` y `.sidebar-panel` fueron completamente omitidas/borradas durante la modularización del CSS legacy.
- **Resolución**: Se creó el archivo de rejilla estructural `layout/dx-v2-grid.css` con la definición moderna de CSS Grid de dos columnas (`1fr 320px`), gaps alineados al sistema de diseño e integración de responsive stacking a `1024px` para pantallas móviles y tablets. Se importó correctamente en `dx-v2-main.css`.

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