# 🛠️ Error Tracking — DX License Manager

Registro centralizado de bugs, errores de UI y discrepancias técnicas detectadas durante el uso de la plataforma.

---

## 📊 Estado de la Flota

| Críticos (P1) | Importantes (P2) | Menores (P3) | Resueltos |
| :--- | :--- | :--- | :--- |
| 1 | 2 | 2 | 0 |

---

## 📝 Registro de Incidencias

| ID | Incidencia | Módulo | Prio | Estado | Fecha Detect. |
| :--- | :--- | :--- | :--- | :--- | :--- |
| #006 | Acciones rápidas sin vínculos / Estáticas | Dashboard | P3 | 🆕 Nuevo | 2026-05-14 |
| #005 | Mejora en Lector de Logs (laravel.log) | Admin/Logs | P2 | 🆕 Nuevo | 2026-05-14 |
| #004 | Revisar visualización de "Other Installs" | UI/UX | P3 | 🆕 Nuevo | 2026-05-14 |
| #003 | Filtro "Solo con Licencias" limitado a Siemens | Clientes | P2 | 🆕 Nuevo | 2026-05-14 |
| #002 | Error de sintaxis y CRLF en backup-db.sh | Infra/Scripts | P1 | 🆕 Nuevo | 2026-05-14 |
| #001 | [Ejemplo] Error de contraste en modo claro | UI/UX | P3 | 🆕 Nuevo | 2026-05-14 |





---

## 🔍 Detalle de Incidencias

### #002 — Error de sintaxis y CRLF en backup-db.sh
- **Síntoma**: El script de backup falla al ejecutarse en el contenedor PHP.
- **Error Logs**: 
  - `$'\r': command not found` (CRLF detectado).
  - `syntax error: unexpected end of file from 'if' command on line 27`.
- **Causa probable**: El archivo fue guardado con finales de línea Windows (CRLF) en lugar de Unix (LF), lo que rompe la interpretación de Bash.
- **Impacto**: Imposibilidad de generar copias de seguridad de la base de datos.
- **Acción**: Convertir a LF y verificar sintaxis de bloques `if`.


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

---

## 🛡️ Protocolo de Resolución

1. **Detección**: El desarrollador anota el error en este archivo.
2. **Triaje**: El agente asigna prioridad y analiza la causa.
3. **Fix**: Se crea una rama `fix/descripcion` para solucionar el problema.
4. **Verificación**: Se comprueba en el entorno Beta.
5. **Cierre**: Se marca como `✅ Resuelto` y se añade la fecha de cierre.

---
_Firmado por: **Antigravity (DX Agent)** 🦾_
