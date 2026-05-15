# 📚 Lessons Learned — DX License Manager

Registro de errores, correcciones y patrones detectados durante el desarrollo.
El agente debe revisar este archivo al inicio de cada sesión.

> **Regla:** Después de cualquier corrección significativa del desarrollador, añadir una entrada aquí.
> **Formato:** Fecha · Qué pasó · Por qué pasó · Regla nueva.

---

## [2026-05-15] — Vaciado Accidental de Base de Datos Beta
- **Qué pasó:** La ejecución de tests de integración desde el contenedor vació todas las tablas de la base de datos MariaDB Beta.
- **Por qué pasó:** Fallo en la configuración de aislamiento del entorno de test. El contenedor ejecutó limpiezas sobre la base de datos real en lugar de SQLite en memoria por una desincronización de variables de entorno.
- **Regla nueva:** **PROHIBIDO** ejecutar tests en el servidor sin un backup previo verificado de la base de datos (`./scripts/backup-db.sh beta`).

## [2026-05-15] — Desincronización de Archivos .env
- **Qué pasó:** Cambios realizados en `backend/.env` no se reflejaban en la aplicación.
- **Por qué pasó:** El contenedor Docker usa un bind mount desde `infra/.env.beta` ignorando el archivo de la raíz de Laravel.
- **Regla nueva:** Editar siempre `infra/.env.beta` para cambios en Beta. Añadido aviso ⚠️ en `backend/.env`.

- **Qué pasó:** El entorno de Beta no cargaba CSS y el layout se veía roto.
- **Por qué pasó:** 
  1. Uso de Nginx `alias` para assets externos (conflicto de rutas).
  2. Nginx intentaba arrancar antes que `php-fpm-beta` (error de upstream).
  3. Uso de clases Tailwind en Blade cuando el proyecto usa CSS Semántico (`dx-styles.css`).
  4. Error de permisos en `storage/framework/views`.
- **Reglas nuevas:**
  1. **Assets**: Siempre en `backend/public/assets/` para que Laravel los sirva nativamente. No usar `alias` en Nginx.
  2. **Docker**: Añadir `depends_on: [php-service]` en Nginx para asegurar disponibilidad del upstream.
  3. **Aesthetics**: Seguir estrictamente `dx-styles.css` y las clases semánticas del prototipo. No improvisar Tailwind sin build step.
  4. **Permisos**: Verificar `chmod 777` en `storage` tras cambios de infraestructura.

## [2026-05-06] — Rotura Total de UI y Funcionalidad en Herramientas
- **Qué pasó:** La página de `/herramientas` (finalizada en Fase 7) y la nueva `nx_suite` se rompieron totalmente, quedando inutilizables.
- **Por qué pasó:** 
  1. **Aesthetics**: El agente usó clases de **Tailwind CSS** por inercia, ignorando que el proyecto usa **CSS Vanilla** (`dx-styles.css`).
  2. **Funcionalidad**: El agente "re-inventó" el Hub de herramientas que ya estaba validado en la Fase 7, rompiendo la lógica dinámica basada en `identities.json` y Feature Flags.
  3. **Rutas**: Se cambiaron las rutas `/herramientas` por `/tools` sin motivo, rompiendo la navegación establecida y la coherencia del portal en castellano.
  4. **Desobediencia**: Se ignoraron las reglas 0.3 de `AGENTS.md` y las especificaciones de `DESIGN.md`.
- **Reglas nuevas:**
  1. **Tailwind Prohibido**: No usar NUNCA clases de Tailwind. Usar solo componentes definidos en `dx-styles.css`.
  2. **Respetar lo Validado**: NUNCA sobreescribir o re-imaginar una funcionalidad ya validada por Oskar (como el Hub dinámico) sin permiso explícito.
  3. **Rutas e Idioma**: Mantener la nomenclatura de rutas en castellano si así se ha definido en fases previas.
  4. **Análisis de Layout**: Antes de crear una vista, revisar `layouts/app.blade.php` y las vistas previas de ese módulo para asegurar consistencia.

## [2026-05-07] — Error 413 al Subir Archivos (Límites PHP/Nginx y Docker env_file)
- **Qué pasó:** Archivos de más de 1MB daban "413 Request Entity Too Large" a pesar de tener configurado `client_max_body_size 100M` en Nginx y `local.ini` en PHP.
- **Por qué pasó:** 
  1. El archivo `local.ini` con las directivas de PHP no se estaba montando en el contenedor `php-fpm-beta`.
  2. El montaje no funcionaba porque el archivo `docker-compose.beta.yml` intentaba cargar `env_file: ./.env.beta`. Al ejecutarse Docker Compose con `--project-directory .` (desde la raíz del repo), buscaba el `.env.beta` en la raíz en lugar de `infra/.env.beta`, fallando silenciosamente en algunos contextos y deteniendo recreaciones completas.
  3. Los cambios en el `beta.conf` de Nginx requieren `nginx -s reload` (o reiniciar contenedor) para aplicarse, y los volúmenes nuevos en Docker requieren hacer `docker compose up -d` para recrear el contenedor, un simple `docker restart` no monta volúmenes nuevos.
- **Reglas nuevas:**
  1. **Rutas en Docker Compose:** Cuando se usa `--project-directory .`, TODAS las rutas dentro del `docker-compose.yml` (`env_file`, `volumes`, `build.context`) se resuelven relativas a la **raíz del proyecto**, no relativas a la carpeta `infra/`.
  2. **Aplicar Cambios en Contenedores:** 
     - Cambios en config Nginx -> `docker exec dx-nginx-beta nginx -s reload` o reiniciar el contenedor.
     - Añadir/modificar volúmenes o variables -> `docker compose up -d` para recrear el contenedor, NUNCA usar solo `docker restart`.

## [2026-05-08] — Archivos Invisibles en Samba y Mapeo de Discos Privados
- **Qué pasó:** Los archivos COD generados se descargaban desde la web pero no aparecían en la unidad `Z:\` del desarrollador.
- **Por qué pasó:** 
  1. El disco `private` de Laravel estaba configurado para usar `storage_path('private')`, que apunta a `storage/private` fuera de la carpeta `app`. El volumen de Docker solo mapeaba `./storage` -> `storage/app`. Todo lo guardado en `private` se quedaba "atrapado" dentro del contenedor.
  2. Los archivos creados por Docker (root) heredaban permisos restrictivos (`700`), impidiendo que el servicio Samba (usuario normal) los viera o listara en Windows.
- **Reglas nuevas:**
  1. **Alineación de Discos**: Cualquier disco de Laravel que deba ser visible en el host (Windows) debe colgar de `storage_path('app/...')` para coincidir con el mapeo del volumen Docker.
  2. **Permisos Samba**: Tras crear carpetas críticas o mover archivos masivamente en el servidor, ejecutar `chown -R 82:82` y `chmod -R 777` en el host para asegurar visibilidad en la unidad `Z:\`.
  3. **Robustez UI**: Ante fallos persistentes de renderizado de modales (JS/Caché), el enfoque "Direct Link/Form" es siempre superior para procesos críticos de subida de archivos.

### 2026-05-08 — Infraestructura y UI Dinámica
65. **Montaje de Gestión**: Para que el backend lea archivos de gestión (CHANGELOG/BACKLOG), montarlos como volúmenes `:ro` (read-only) en el `docker-compose`.
66. **Parseo de Changelog**: Al parsear Markdown para la web, usar arrays secuenciales. Usar la fecha como clave sobrescribe entradas si hay varias en el mismo día.
67. **Sanitización de Clases CSS**: Al generar clases dinámicas desde texto (ej: categorías), sanitizar eliminando espacios y caracteres especiales para evitar selectores inválidos.
68. **Localización Dompdf**: Para documentos legales en castellano, asegurar que los nombres de los meses se traduzcan correctamente usando `translatedFormat()` de Carbon.

## [2026-05-11] — Desastre en Base de Datos Beta y Sincronización de Esquema
- **Qué pasó:** Borrado accidental de la base de datos MariaDB Beta durante la ejecución de tests de integración y errores de "Columna no encontrada" tras la restauración.
- **Por qué pasó:** 
  1. **Aislamiento de Tests**: El uso de `RefreshDatabase` en el entorno Beta sin forzar SQLite explícitamente en el comando provocó la limpieza de la base de datos real.
  2. **Inconsistencia de Backup**: El backup restaurado (`backup_pre_normalization.sql`) no contenía las últimas columnas añadidas (`warnings`, `detected_name`, etc.), causando errores 500 en las vistas de administración.
  3. **Script de Backup Roto**: El script de backup original fallaba con caracteres especiales (`!`) en las contraseñas al no usar comillas o `MYSQL_PWD`.
- **Reglas nuevas:**
  1. **Backup Preventivo Obligatorio**: NUNCA ejecutar cambios estructurales o tests en el servidor sin realizar un backup previo verificado.
  2. **Aislamiento Total de Tests**: Los tests en el servidor DEBEN forzar SQLite en memoria (`-e DB_CONNECTION=sqlite -e DB_DATABASE=:memory:`) para evitar tocar MariaDB.
  3. **Robustez de Scripts**: Usar siempre `MYSQL_PWD` en scripts de shell para pasar contraseñas de base de datos de forma segura.
  4. **Verificación post-restauración**: Tras restaurar un backup, es imperativo revisar el `DESCRIBE` de las tablas críticas frente a los modelos de Laravel para detectar columnas faltantes.

## [2026-05-11] — Métricas de Git Invisibles (N/A) y Permisos en Docker
- **Qué pasó:** El Dashboard mostraba `N/A` en el Hash y Fecha de despliegue, a pesar de tener la carpeta `.git` montada.
- **Por qué pasó:** 
  1. **Configuración Global vs System**: Se configuró `safe.directory` como `--global` en el Dockerfile, lo que solo afectaba al usuario `root`. El servidor web (PHP-FPM) corre como `www-data`, por lo que Git bloqueaba el acceso por "dubious ownership".
  2. **Localización**: La fecha de Git se extraía directamente en inglés, rompiendo la estética del portal en castellano.
- **Reglas nuevas:**
  1. **Git en Docker**: Al usar Git dentro de un contenedor con volúmenes montados, configurar siempre `git config --system --add safe.directory /path/to/repo` para que afecte a todos los usuarios (especialmente a `www-data`).
  2. **Métricas de Git**: Extraer siempre el *timestamp* (`%ct`) y formatearlo con `Carbon` en Laravel para garantizar una localización correcta y dinámica (`diffForHumans`).

---
_Firmado por: **Antigravity (DX Agent)** 🦾_
