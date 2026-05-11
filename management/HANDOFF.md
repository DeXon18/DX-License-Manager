He completado la evolución del Dashboard del Sistema a la versión **NOC Pro**.

### Cambios Principales:
1.  **Telemetría de Bajo Nivel**: Implementada lectura directa de `eth0` para tráfico de red (RX/TX) y monitorización profunda de MariaDB (conexiones activas y consultas lentas).
2.  **Panel de Acciones Rápidas (NOC Control)**: Panel interactivo con Alpine.js que permite ejecutar tareas críticas:
    *   Limpiar caché y vistas.
    *   Reiniciar workers de colas.
    *   Generar backups manuales de MariaDB.
    *   Conmutar el Modo Mantenimiento.
    *   Probar alertas de Telegram.
3.  **Audit Live Feed**: Nueva sección que muestra los últimos 10 eventos de auditoría administrativa en tiempo real.
4.  **Integración Git**: Detección automática del hash de commit y fecha del último despliegue en la cabecera.
5.  **Seguridad**: Todas las acciones administrativas quedan registradas en la base de datos vinculadas al usuario.

### Verificación:
- Las métricas se actualizan correctamente al recargar.
- Las acciones muestran confirmación antes de ejecutarse.
- El modo mantenimiento visualiza un indicador pulsante en la cabecera.

Los cambios han sido commiteados y subidos a la rama `feature/system-noc-pro`.
