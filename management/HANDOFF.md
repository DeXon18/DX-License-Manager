# HANDOFF — DX License Manager
> Última actualización: 2026-05-13 16:05  
> Sesión en: Planificador de Renovaciones y Blindaje de Dashboard  
> Rama activa: feature/renewal-planner

---

## Estado General

**Fase actual:** Fase 14 — Planificador Operativo (Versión Inicial) ⚠️ En curso  
**Stack beta:** ✅ running  
**Stack prod:** ✅ running  

---

## Qué se hizo en esta sesión

- **Seguridad UI (Dashboard)**:
    - Se ocultaron los enlaces de "Ver Inventario" e "Importar CSV" para usuarios que no tienen rol `admin`.
    - La visibilidad se controla mediante `@if(auth()->user()->isAdmin())`.
- **Fase 14 — Planificador de Renovaciones (Motor & UI)**:
    - **Infraestructura**: Creadas tablas `renewal_logs` (registro de acción) y `renewal_log_files` (múltiples adjuntos).
    - **Lógica de Filtrado**: Implementado filtrado cíclico por mes (ignorando año) sobre la fecha de fin de contrato (`end_date`).
    - **Soporte Multi-archivo**: Capacidad para subir NX, STAR-CCM+, HEEDS o combinaciones en un solo registro de renovación.
    - **Historial en Cliente**: Nueva pestaña "Renovaciones" en la ficha del cliente que muestra cronológicamente las licencias enviadas con descarga directa.
    - **Alta Densidad (NOC Pro)**: UI optimizada para mostrar Sold-Tos y estados corporativos (colores Siemens/Moldex).

---

## Qué falta por hacer (próxima sesión)

### Tarea inmediata (empezar aquí)
1. **Verificación de Flujo**: Probar el flujo completo: seleccionar mes -> elegir cliente -> adjuntar 2-3 archivos -> marcar enviado -> verificar en perfil de cliente.
2. **Merge a Dev**: Si la prueba es satisfactoria, mergear `feature/renewal-planner` a `dev`.

### Tareas siguientes
1. **Notificaciones**: Evaluar si el sistema debe mandar un aviso automático por Telegram al marcar como enviado.
2. **Fase 15 (Integraciones IA)**: Retomar el refinamiento de los proveedores (Gemini, Deepseek) para auditoría de archivos `.mac` de Moldex3D.

---

## Contexto técnico importante

- **Modelos**: `RenewalLog` (acción principal) -> hasMany -> `RenewalLogFile` (adjuntos).
- **Almacenamiento**: Los archivos se guardan en `storage/app/renewals/{client_id}/`.
- **Rutas**:
    - Index: `/planificador`
    - Descarga: `/planificador/download/{file_id}`
- **Permisos**: El Planificador es accesible para todos los usuarios autenticados (para que técnicos vean qué hay que hacer), pero la importación/normalización sigue siendo solo para `admin`.

---

## Bloqueos o problemas sin resolver

- **Permisos Samba**: Al crear archivos desde el contenedor (artisan make), estos pertenecen a `root`. Se ha aplicado el workaround de borrarlos y recrearlos desde Windows para mantener la editabilidad. **RECOMENDACIÓN**: Si el próximo agente crea archivos, recordar hacer `chown` o recrearlos.

---

## Estado de archivos clave

| Archivo | Estado |
|:---|:---|
| `backend/app/Models/RenewalLog.php` | ✅ Nuevo |
| `backend/app/Models/RenewalLogFile.php` | ✅ Nuevo |
| `backend/app/Http/Controllers/RenewalPlannerController.php` | ✅ Nuevo |
| `backend/resources/views/renewal-planner/index.blade.php` | ✅ Nuevo |
| `backend/resources/views/clients/show.blade.php` | ✅ Actualizado (Tab Renovaciones) |

---

## Comandos útiles para la próxima sesión

```bash
# Limpiar cachés tras el merge
docker exec dx-php-beta php artisan optimize:clear

# Ver registros de renovaciones creados hoy
docker exec dx-php-beta php artisan tinker --execute="print_r(App\Models\RenewalLog::all())"
```
