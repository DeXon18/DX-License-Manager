# 📚 Lessons Learned — DX License Manager

Registro de errores, correcciones y patrones detectados durante el desarrollo.
El agente debe revisar este archivo al inicio de cada sesión.

> **Regla:** Después de cualquier corrección significativa del desarrollador, añadir una entrada aquí.
> **Formato:** Fecha · Qué pasó · Por qué pasó · Regla nueva.

---

## [2026-05-05] — Bloqueo de Assets y Layout en Beta
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

---
Firmado por: **Antigravity (DX Agent)** 🦾
