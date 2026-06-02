# HANDOFF — DX License Manager
> Última actualización: 2026-06-02 16:10
> Sesión en: DEV (DX-License-Manager-DEV)
> Rama activa: dev

---

## Estado General

**Fase actual:** Preparación para Integración Siemens (Inventario)
**Stack beta:** ✅ running (en `/opt/web-projects/DX-License-Manager-DEV`)
**Stack prod:** ✅ running (en `/opt/web-projects/DX-License-Manager`)

---

## Qué se hizo en esta sesión

- **Aislamiento Físico de Entornos**: Completada la separación total de los entornos `prod` y `beta`. Ahora residen en carpetas físicas distintas en el host, con bases de datos y redes aisladas por Docker Compose.
- **GitOps y CI/CD**: Modificado `.github/workflows/deploy-beta.yml` para desplegar automáticamente en la nueva ruta `-DEV`.
- **Limpieza de Storage**: Eliminados montajes cruzados de `storage_beta` y `storage_prod` en los archivos `docker-compose`, y limpiadas las carpetas residuales físicas.
- **Documentación de Arquitectura**: Generado `management/ARCHITECTURE.md` para documentar la arquitectura de dos carpetas.
- **Planificación de Tarea**: Aprobado y generado el checklist de integración para el inventario de licencias Siemens (Paso 1, 2, 3, 4).

---

## Qué falta por hacer (próxima sesión)

### Tarea inmediata (empezar aquí)
Iniciar con el **Paso 1 del Plan Siemens**: 
- Crear el modelo `SiemensLicense` y su migración (`create_siemens_licenses_table`).
- Definir relaciones en el modelo `Client` (`clientMapping()`).

### Tareas siguientes (Plan Siemens)
1. Desarrollar `SiemensImportService` y `SiemensInventoryController` para procesar CSV.
2. Crear `SiemensReconciliationService` para cruzar datos reales (.lic) vs teóricos (CSV).
3. Implementar la UI "Perfil de Siemens" en la ficha del cliente con badges de color.

---

## Contexto técnico importante

- **REGLA DE ORO DE INFRAESTRUCTURA**: El repositorio base es el MISMO. Los archivos `.yml` trackeados por Git existen en ambas carpetas. NUNCA borrar `docker-compose.prod.yml` de la carpeta DEV, o se borrará en Producción tras el merge.
- La limpieza de secretos (`.env`) sí está permitida al estar ignorados por Git.
- Todos los comandos de entorno de desarrollo se ejecutan ahora sobre `Z:\DX-License-Manager-DEV\`.

---

## Bloqueos o problemas sin resolver

Ninguno. El entorno de desarrollo (Beta) está listo para empezar a picar código de forma 100% aislada.

---

## Estado de archivos clave

| Archivo | Estado |
|:---|:---|
| `infra/.env.beta` | ✅ configurado en DEV |
| `management/ARCHITECTURE.md` | ✅ nuevo doc permanente |
| `task.md` | ✅ plan de Siemens listo |

---

## Comandos útiles para la próxima sesión

```bash
# Crear el modelo y migración para Siemens en Beta
docker exec -it dx-php-beta php artisan make:model SiemensLicense -m
```
