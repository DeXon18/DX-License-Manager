# HANDOFF — DX License Manager
> Última actualización: 2026-05-15 13:55  
> Sesión en: PC Desarrollo (srv-dxportal remote)  
> Rama activa: dev

---

## Estado General

**Fase actual:** Mantenimiento y Estabilización (Incidencias)  
**Stack beta:** ✅ running  
**Stack prod:** ✅ running  

---

## Qué se hizo en esta sesión

- **Estabilización JWT (#014)**:
  - Implementada rotación inteligente (intervalo de 5 min) para evitar spam de tokens.
  - Aumentada la ventana de gracia a 120s en Redis para manejar peticiones asíncronas y múltiples pestañas.
  - Ampliado el tiempo de inactividad de la sesión a 60 minutos.
  - Añadida telemetría de logs para detectar motivos de expulsión.
- **Dashboard Operativo (#006)**:
  - Implementado Buscador Global Express (Sold-To, Machine ID, Cliente).
  - Vinculación de Acciones Rápidas (Favoritos) a herramientas reales.
  - Implementado contador dinámico de renovaciones mensuales.
- **Limpieza de Sistema (#009)**:
  - Unificados volúmenes de storage en `backend/storage`.
  - Eliminados residuos de DB y archivos `.sql` huérfanos.
  - Configurado Git para ignorar dirty submodules de diseño/skills.
- **Fixes de Base de Datos (#007)**:
  - Parcheadas tablas `ai_audit_results` y `normalization_decisions` con columnas faltantes.

---

## Qué falta por hacer (próxima sesión)

### Tarea inmediata (empezar aquí)
**Normalización Semántica con IA (#007).**
1. Integrar motor Gemini/DeepSeek para sugerir unificaciones automáticas de clientes con nombres similares.
2. Refinar el umbral de `ClientNormalizationService`.

### Tareas siguientes
1. Unificación de estilos CSS globales (#008).
2. UI Multi-Sold-To (Mejora visual de Other Installs #004).

---

## Contexto técnico importante

- **JWT Hardening**: Si el usuario sigue siendo expulsado, revisar `laravel.log` buscando `JWT: Sesión revocada`. El sistema ahora indica el desfase de tiempo exacto.
- **Storage**: NO usar nunca más la carpeta `storage` en la raíz. Todo vive en `backend/storage`.
- **Docker**: Se requiere ejecutar `docker compose up -d` en el servidor para aplicar el cambio de volúmenes del storage.

---

## Bloqueos o problemas sin resolver

- **Ninguno**: El sistema es funcional y la sesión se ha estabilizado.

---

## Estado de archivos clave

| Archivo | Estado |
|:---|:---|
| `backend/app/Http/Middleware/JwtAuth.php` | ✅ Blindado (120s gracia, 5 min cool-off) |
| `backend/resources/views/dashboard.blade.php` | ✅ UI Operativa con Buscador |
| `infra/docker-compose.beta.yml` | ✅ Volumen storage normalizado |
| `management/ERRORS.md` | ✅ Incidencias #014, #006, #009 cerradas |

---

## Comandos útiles para la próxima sesión

```bash
# Verificar migraciones en el servidor
docker exec dx-php-beta php artisan migrate

# Aplicar cambios de volumen (Storage)
docker compose --project-directory . -f infra/docker-compose.beta.yml up -d

# Limpiar caché de Laravel tras la limpieza
docker exec dx-php-beta php artisan optimize:clear
```
