# 🛠️ Error Tracking — DX License Manager

Registro centralizado de bugs, errores de UI y discrepancias técnicas detectadas durante el uso de la plataforma.

---

## 📊 Estado de la Flota

| Críticos (P1) | Importantes (P2) | Menores (P3) | Resueltos |
| :--- | :--- | :--- | :--- |
| 0 | 1 | 0 | 14 |

---

---

### ⏳ Pendientes

| ID | Incidencia | Módulo | Prioridad | Estado | Fecha detección |
|---:|---|---|:---:|:---:|:---:|
| #008 | Unificación de estilos CSS en archivo central | UI/UX | 🟢 P3 | 🆕 Nuevo | 2026-05-14 |


### ✅ Resueltos

| ID | Incidencia | Módulo | Prioridad | Estado | Fecha detección | Fecha resolución |
|---:|---|---|:---:|:---:|:---:|:---:|
| #015 | Fallo en vista previa COD (nesting HTML roto) | Herramientas | 🔴 P1 | ✅ Resuelto | 2026-05-16 | 2026-05-16 |
| #004 | Revisar visualización de "Other Installs" | UI/UX | 🟢 P3 | ✅ Resuelto | 2026-05-14 | 2026-05-15 |

| #007 | Fallo en normalización / duplicidad de clientes | Normalización | 🟠 P2 | ✅ Resuelto | 2026-05-14 | 2026-05-15 |
| #003 | Filtro "Solo con Licencias" limitado a Siemens | Clientes | 🟠 P2 | ✅ Resuelto | 2026-05-14 | 2026-05-15 |
| #009 | Limpieza de archivos basura y registros huérfanos | Sistema | 🟢 P3 | ✅ Resuelto | 2026-05-14 | 2026-05-15 |
| #006 | Acciones rápidas sin vínculos / estáticas | Dashboard | 🟢 P3 | ✅ Resuelto | 2026-05-14 | 2026-05-15 |
| #013 | Invisibilidad de licencias Moldex3D en inventario | Inventario | 🔴 P1 | ✅ Resuelto | 2026-05-15 | 2026-05-15 |
| #014 | Expiración prematura de sesión JWT | Auth/JWT | 🟠 P2 | ✅ Resuelto | 2026-05-15 | 2026-05-15 |
| #012 | RedisException: MISCONF — persistencia fallida | Infra/Redis | 🔴 P1 | ✅ Resuelto | 2026-05-15 | 2026-05-15 |
| #011 | Transformación de licencia NX falla — no descarga/procesa | Siemens NX | 🔴 P1 | ✅ Resuelto | 2026-05-15 | 2026-05-15 |
| #002 | Error de sintaxis y CRLF en `backup-db.sh` | Infra/Scripts | 🔴 P1 | ✅ Resuelto | 2026-05-14 | 2026-05-14 |
| #010 | Indicadores de seguridad siempre a 0 | Dashboard | 🟠 P2 | ✅ Resuelto | 2026-05-15 | 2026-05-15 |
| #005 | Mejora en lector de logs `laravel.log` | Admin/Logs | 🟠 P2 | ✅ Resuelto | 2026-05-15 | 2026-05-15 |
| #001 | [Ejemplo] Error de contraste en modo claro | UI/UX | 🟢 P3 | ✅ Resuelto | 2026-05-14 | 2026-05-14 |

### Leyenda de prioridad

| Prioridad | Significado |
|:---:|---|
| 🔴 P1 | Alta — impacto crítico o bloqueo funcional |
| 🟠 P2 | Media — afecta a funcionalidades importantes |
| 🟢 P3 | Baja — mejora, revisión visual o tarea no bloqueante |
---

## 🔍 Detalle de Incidencias

### #012 — RedisException: MISCONF (Persistencia fallida)
- **Síntoma**: El portal devuelve un error 500 con el mensaje `MISCONF Redis is configured to save RDB snapshots, but it's currently unable to persist to disk`. Las escrituras en base de datos que dependen de caché o sesión están bloqueadas.
- **Impacto**: Bloqueo total del uso del portal (Login, Acciones, Audit).
- **Causa probable**: El proceso de fondo `BGSAVE` de Redis está fallando, posiblemente por falta de permisos en el volumen de datos o por memoria insuficiente en el contenedor para realizar el fork.
- **Acción inmediata**: Ejecutar `config set stop-writes-on-bgsave-error no` vía redis-cli para desbloquear las escrituras. Investigar logs del contenedor Redis.
- **Resolución**: 
  - Diagnóstico SSH confirmó `Permission denied` en el volumen anónimo de `/data` (pertenecía a root).
  - Aplicado `chown redis:redis /data` en caliente para restaurar el guardado RDB.
  - Securizada la configuración mediante **volúmenes nombrados** en `docker-compose.beta.yml` y `docker-compose.prod.yml` para delegar la gestión de permisos a Docker de forma persistente.
  - Verificado `BGSAVE` exitoso y restaurada la política de seguridad `stop-writes-on-bgsave-error yes`.

### #011 — Transformación de Licencia (NX) falla (No descarga/procesa)
- **Síntoma**: Al intentar transformar una licencia NX, el sistema no descarga el archivo resultante ni parece procesar la lógica de transformación.
- **Impacto**: Bloqueo total en la generación de licencias para clientes Siemens.
- **Causa probable**: Inconsistencia en stream de descarga o fallo en lógica Multi-Sold-To.
- **Acción**: Investigar el flujo en `NXSuiteController` y `NXSuiteService` para detectar fallos en la generación del stream de descarga o errores silenciosos en el procesamiento.
- **Resolución**: 
  - Se corrigió el flujo de descarga en el controlador evitando bloqueos por peticiones AJAX asíncronas.
  - Implementada gestión de memoria avanzada (`256M`) y `try-catch` con degradación elegante.
  - El sistema ahora asegura la entrega del archivo transformado incluso si fallan servicios secundarios (IA/Storage).
  - Validado el soporte para extensiones `.dat` y `.cid`.
  - Añadida validación UI con Alpine.js para feedback inmediato al usuario.


### #002 — Error de sintaxis y CRLF en backup-db.sh
- **Síntoma**: El script de backup falla al ejecutarse en el contenedor PHP.
- **Error Logs**: 
  - `$'\r': command not found` (CRLF detectado).
  - `syntax error: unexpected end of file from 'if' command on line 27`.
- **Causa**: Finales de línea Windows (CRLF) que rompen la interpretación de Bash y falta de comillas en variables de entorno.
- **Impacto**: Imposibilidad de generar copias de seguridad de la base de datos.
- **Acción**: Conversión a LF (Unix), limpieza de sintaxis en bloques `if` y blindaje de variables con comillas.


### #003 — Filtro "Solo con Licencias" limitado a Siemens
- **Síntoma**: Al activar el filtro de licencias en Gestión de Clientes, solo aparecen los que tienen licencias Siemens en el inventario.
- **Causa probable**: La query en `ClientController` probablemente solo está contando `inventory_daemons` (donde están las de Siemens) o ignorando el flag de Moldex3D.
- **Impacto**: Inconsistencia en la gestión de clientes que solo tienen Moldex3D.
- **Resolución**: ✅ Resuelto el 2026-05-15. Implementado switch de filtrado multi-vendor con conteo dinámico Siemens/Moldex.

### #004 — Revisar visualización de "Other Installs"
- **Síntoma**: Los Sold-To adicionales se muestran como badges, pero se requiere una revisión estética para asegurar que no rompen el layout en casos con muchos IDs.
- **Causa probable**: Diseño inicial funcional pero no optimizado para alta densidad de IDs adicionales.
- **Impacto**: Mejora de UX en la visualización de licencias unificadas.
- **Resolución**: 
  - Rediseño estético "NOC Pro v2" con marca de agua técnica (`fa-network-wired`) al 4% de opacidad.
  - Los Sold-Tos adicionales se han movido a una franja minimalista transparente bajo el header.
  - IDs resaltados en color crema/amarillo suave (#fde68a) para un look industrial premium.
  - ✅ Resuelto el 2026-05-15. Visualización de licencias unificadas optimizada para alta densidad.

### #005 — Mejora en Lector de Logs (laravel.log)
- **Síntoma**: El lector de logs del sistema (`admin/audit?tab=system`) muestra trazas completas ilegibles y no parece estar capturando alertas correctamente (contador en 0).
- **Causa probable**: El parser de `AuditLogController` solo mostraba texto plano sin estructurar.
- **Acción**: 
  - Implementado parser Regex en backend para estructurar logs.
  - Implementada UI interactiva con Alpine.js (Trazas colapsables).
  - Sincronizado contador de alertas unificando DB + Fichero físico.
- **Resolución**: ✅ Resuelto el 2026-05-15. Diagnóstico de sistema profesionalizado.

### #006 — Acciones rápidas sin vínculos / Estáticas
- **Síntoma**: Los botones del panel de "Acciones Rápidas" en el Dashboard no redirigen a ninguna parte o carecen de lógica funcional dinámica.
- **Causa probable**: Implementación inicial centrada en el diseño visual (Bento) sin terminar de cablear los enlaces y la lógica de backend.
- **Impacto**: Experiencia de usuario incompleta en el centro de mando.
- **Acción**: 
  - Implementado **Buscador Global Express** en el Dashboard con soporte para Sold-To, Machine ID y Nombre de Cliente.
  - Vinculadas acciones rápidas a herramientas reales: Generación de COD, Planificador de Renovaciones y Hub de Auditoría IA.
  - Implementado **Contador de Renovaciones Pendientes** del mes actual con badge dinámico.
- **Resolución**: ✅ Resuelto el 2026-05-15. Dashboard transformado en centro operativo funcional para el usuario.

### #007 — Fallo en Normalización / Duplicidad de Clientes
- **Síntoma**: Se detectan clientes duplicados con nombres muy similares que no están siendo agrupados automáticamente (ej: "Fundacion Tecnalia" vs "Fundación Tecnalia Research & Innovation").
- **Causa probable**: El umbral del motor de normalización actual (85%) o la lógica de comparación no está capturando variaciones largas. La bandeja de normalización no está sugiriendo estos casos.
- **Impacto**: Inventario fragmentado (Sold-To en un cliente, Contratos en otro).
- **Resolución**: 
  - Implementado motor de búsqueda semántica mediante `ClientNormalizationService`.
  - Integrada lógica de Alias y Fuzzy Matching en todos los controladores de sincronización.
  - ✅ Resuelto el 2026-05-15.

### #008 — Unificación de estilos CSS en archivo central
- **Síntoma**: Existen estilos dispersos en archivos Blade o archivos CSS secundarios que dificultan el mantenimiento global.
- **Causa probable**: Crecimiento orgánico del proyecto y personalizaciones ad-hoc en vistas específicas.
- **Impacto**: Mayor dificultad para aplicar cambios de diseño globales y riesgo de inconsistencias visuales.
- **Acción**: Migrar todos los estilos inline y CSS secundarios a `public/css/dx-styles.css` (o un sistema de importaciones ordenado) para centralizar la identidad visual.

### #009 — Limpieza de archivos basura y registros huérfanos
- **Síntoma**: Presencia de directorios temporales, archivos de backup antiguos o registros en base de datos que ya no tienen relación con identidades activas.
- **Causa probable**: Acumulación por pruebas de desarrollo, migraciones incompletas o falta de una política de purga automática.
- **Impacto**: Desorden en el sistema de archivos y posible degradación ligera del rendimiento de la base de datos.
- **Acción**: 
  - Eliminada carpeta redundante `./storage` de la raíz y migrados datos a `backend/storage/app` para cumplir el estándar Laravel.
  - Borrados archivos `.sql` y trazas de log antiguas.
  - Configurado Git para ignorar el estado "dirty" de los submódulos de diseño/skills.
- **Resolución**: ✅ Resuelto el 2026-05-15. Sistema de archivos normalizado y libre de residuos.

### #010 — Indicadores de Seguridad siempre a 0
- **Síntoma**: Los contadores de "Errores Críticos", "Logins Fallidos" y "JWT Blacklist" en el Dashboard principal siempre muestran valor 0, incluso tras forzar eventos de prueba.
- **Causa probable**: El `DashboardController` (SystemDashboardController) no estaba consultando las tablas correctas y el sistema no estaba registrando logs de login fallidos ni gestionando la blacklist.
- **Impacto**: Falsa sensación de seguridad o ceguera ante ataques/errores reales.
- **Acción**: 
  - Implementado registro de `login_failed` en `AuthController`.
  - Implementada gestión de `jwt_blacklist` en Redis (ZSET) en Logout y Middleware.
  - Sincronizados niveles de error (`warning` vs `error`) en la telemetría del dashboard.
- **Resolución**: ✅ Resuelto el 2026-05-15. Sistema de telemetría ahora 100% operativo.

### #014 — Expiración Prematura de Sesión JWT
- **Síntoma**: El sistema cierra la sesión del usuario de forma inesperada antes de cumplirse los 15 minutos de inactividad configurados. El usuario es redirigido al login sin previo aviso.
- **Impacto**: Interrupción del flujo de trabajo y posible pérdida de datos no guardados en formularios largos.
- **Causa probable**: 
    - Desincronización entre el TTL del token JWT y el tiempo de vida de la cookie en el navegador.
    - El middleware de rotación de Refresh Tokens podría estar invalidando el token actual incorrectamente al detectar múltiples peticiones asíncronas simultáneas.
    - El `SESSION_LIFETIME` en Laravel podría estar configurado con un valor inferior al esperado.
- **Resolución**: 
    - Identificada desincronización de secretos entre `backend/.env` e `infra/.env.beta` (causa raíz de la invalidación).
    - Implementada **Rotación Inteligente** de tokens (cool-off de 5 min) para evitar colisiones AJAX.
    - Ampliada ventana de gracia en Redis a 120s.
    - Aumentado TTL de sesión a 60 min.
    - ✅ Resuelto el 2026-05-15. Sesión estabilizada con políticas de grado industrial.


### #013 — Invisibilidad de Licencias Moldex3D en Inventario
- **Síntoma**: No se detectan licencias activas de Moldex3D en el inventario a pesar de haber realizado auditorías previas. El conteo de daemons devuelve 0 para este vendor (`moldex_daemons_count`).
- **Impacto**: Bloqueo en el seguimiento de renovaciones y parque de licencias Moldex3D. El filtro granular implementado muestra resultados vacíos para este vendor.
- **Causa probable**: 
    - Desconexión entre `AiAuditResult` y la persistencia en `LicenseInventoryDaemon`.
    - Fallo en el mapeo semántico del nombre del cliente en `MoldexSyncService`.
    - El daemon name `moldex3d` podría no ser el único usado en archivos `.mac`.
- **Acción inmediata**: 
    - Verificar logs de `MoldexSync` (actualmente sin entradas recientes).
    - Crear un entorno de pruebas con datos reales para validar la persistencia.
    - Se ha creado un registro manual para "Walter Pack Sl" para verificar que la UI funciona correctamente, pero el problema de fondo en la sincronización persiste.
- **Resolución**: ✅ Resuelto el 2026-05-15. Refactorizado `MoldexSyncService` para usar `ClientNormalizationService` (soporte Alias y Fuzzy Matching) y blindado `MoldexController` para retornar HTTP 422 si falla la persistencia.

---

## 🛡️ Protocolo de Resolución

1. **Detección**: El desarrollador anota el error en este archivo.
2. **Triaje**: El agente asigna prioridad y analiza la causa.
3. **Fix**: Se crea una rama `fix/descripcion` para solucionar el problema.
4. **Verificación**: Se comprueba en el entorno Beta.
5. **Cierre**: Se marca como `✅ Resuelto` y se añade la fecha de cierre.

---
_Firmado por: **Antigravity (DX Agent)** 🦾_
