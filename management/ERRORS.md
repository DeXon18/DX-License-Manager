# 🛠️ Error Tracking — DX License Manager

Registro centralizado de bugs, errores de UI y discrepancias técnicas detectadas durante el uso de la plataforma.

---

## 📊 Estado de la Flota

| Críticos (P1) | Importantes (P2) | Menores (P3) | Resueltos |
| :--- | :--- | :--- | :--- |
| 0 | 4 | 4 | 3 |

---

## 📝 Registro de Incidencias

| ID | Incidencia | Módulo | Prio | Estado | Fecha Detect. |
| :--- | :--- | :--- | :--- | :--- | :--- |
| #012 | RedisException: MISCONF (Persistencia fallida) | Infra/Redis | P1 | ✅ Resuelto | 2026-05-15 |
| #011 | Transformación de Licencia (NX) falla (No descarga/procesa) | Siemens NX | P1 | ✅ Resuelto | 2026-05-15 |
| #010 | Indicadores de Seguridad siempre a 0 | Dashboard | P2 | 🆕 Nuevo | 2026-05-14 |
| #009 | Limpieza de archivos basura y registros huérfanos | Sistema | P3 | 🆕 Nuevo | 2026-05-14 |
| #008 | Unificación de estilos CSS en archivo central | UI/UX | P3 | 🆕 Nuevo | 2026-05-14 |
| #007 | Fallo en Normalización / Duplicidad de Clientes | Normalización | P2 | 🆕 Nuevo | 2026-05-14 |
| #006 | Acciones rápidas sin vínculos / Estáticas | Dashboard | P3 | 🆕 Nuevo | 2026-05-14 |
| #005 | Mejora en Lector de Logs (laravel.log) | Admin/Logs | P2 | 🆕 Nuevo | 2026-05-14 |
| #004 | Revisar visualización de "Other Installs" | UI/UX | P3 | 🆕 Nuevo | 2026-05-14 |
| #003 | Filtro "Solo con Licencias" limitado a Siemens | Clientes | P2 | 🆕 Nuevo | 2026-05-14 |
| #002 | Error de sintaxis y CRLF en backup-db.sh | Infra/Scripts | P1 | ✅ Resuelto | 2026-05-14 |
| #001 | [Ejemplo] Error de contraste en modo claro | UI/UX | P3 | 🆕 Nuevo | 2026-05-14 |


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
- **Acción**: Actualizar la lógica del filtro para incluir clientes con licencias de ambos vendors o permitir selección específica.

### #004 — Revisar visualización de "Other Installs"
- **Síntoma**: Los Sold-To adicionales se muestran como badges, pero se requiere una revisión estética para asegurar que no rompen el layout en casos con muchos IDs.
- **Causa probable**: Diseño inicial funcional pero no optimizado para alta densidad de IDs adicionales.
- **Impacto**: Mejora de UX en la visualización de licencias unificadas.
- **Acción**: Ajustar estilos en `clients/show.blade.php` para asegurar una disposición armoniosa de los IDs adicionales (ej. envolver en contenedor con scroll o grid compacto).

### #005 — Mejora en Lector de Logs (laravel.log)
- **Síntoma**: El lector de logs del sistema (`admin/audit?tab=system`) muestra trazas completas ilegibles y no parece estar capturando alertas correctamente (contador en 0).
- **Causa probable**: El parser de `AuditLogController` o el servicio correspondiente no está filtrando las líneas de stack trace de Laravel y solo muestra el texto plano.
- **Impacto**: Dificultad para el diagnóstico técnico desde la UI.
- **Acción**: 
  - Pulir la visualización eliminando líneas de `#0 /var/www/...` que no aportan valor visual.
  - Implementar un sistema de "colapsado" de trazas.
  - Verificar por qué el contador de alertas no se sincroniza con el archivo físico.

### #006 — Acciones rápidas sin vínculos / Estáticas
- **Síntoma**: Los botones del panel de "Acciones Rápidas" en el Dashboard no redirigen a ninguna parte o carecen de lógica funcional dinámica.
- **Causa probable**: Implementación inicial centrada en el diseño visual (Bento) sin terminar de cablear los enlaces y la lógica de backend.
- **Impacto**: Experiencia de usuario incompleta en el centro de mando.
- **Acción**: 
  - Vincular acciones a rutas reales (Limpiar caché, Backups, Auditoría, etc.).
  - Estudiar la posibilidad de que sean configurables o dinámicas según el rol del usuario.

### #007 — Fallo en Normalización / Duplicidad de Clientes
- **Síntoma**: Se detectan clientes duplicados con nombres muy similares que no están siendo agrupados automáticamente (ej: "Fundacion Tecnalia" vs "Fundación Tecnalia Research & Innovation").
- **Causa probable**: El umbral del motor de normalización actual (85%) o la lógica de comparación no está capturando variaciones largas. La bandeja de normalización no está sugiriendo estos casos.
- **Impacto**: Inventario fragmentado (Sold-To en un cliente, Contratos en otro).
- **Acción**: 
  - Estudiar integración con IA (Gemini/DeepSeek) para una identificación semántica de clientes.
  - Revisar comportamiento de la bandeja de normalización.
  - Ajustar lógica de `ClientNormalizationService`.

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
  - Realizar una auditoría de directorios (scripts, tmp, etc.).
  - Verificar registros huérfanos en tablas de inventario y auditoría.
  - Ejecutar limpieza controlada.

### #010 — Indicadores de Seguridad siempre a 0
- **Síntoma**: Los contadores de "Errores Críticos", "Logins Fallidos" y "JWT Blacklist" en el Dashboard principal siempre muestran valor 0, incluso tras forzar eventos de prueba.
- **Causa probable**: El `DashboardController` no está consultando correctamente las tablas de `audit_log`, logs de autenticación o el estado de Redis para el TTL de los tokens.
- **Impacto**: Falsa sensación de seguridad o ceguera ante ataques/errores reales.
- **Acción**: 
  - Revisar métodos de conteo en el controlador del Dashboard.
  - Verificar que los eventos de seguridad se están registrando en la base de datos/Redis.
  - Asegurar que el rango de tiempo (24h) está correctamente implementado.

---

## 🛡️ Protocolo de Resolución

1. **Detección**: El desarrollador anota el error en este archivo.
2. **Triaje**: El agente asigna prioridad y analiza la causa.
3. **Fix**: Se crea una rama `fix/descripcion` para solucionar el problema.
4. **Verificación**: Se comprueba en el entorno Beta.
5. **Cierre**: Se marca como `✅ Resuelto` y se añade la fecha de cierre.

---
_Firmado por: **Antigravity (DX Agent)** 🦾_
