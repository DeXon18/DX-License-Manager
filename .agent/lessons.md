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

## [2026-05-06] — Rotura Total de UI por uso de Tailwind
- **Qué pasó:** La página de `/herramientas` y `nx_suite` se vio totalmente rota (iconos gigantes, sin layout).
- **Por qué pasó:** 
  1. El agente usó clases de **Tailwind CSS** por inercia, ignorando que el proyecto usa **CSS Vanilla** (`dx-styles.css`).
  2. Al no existir un build step de Tailwind, las clases no hacían nada y los elementos (especialmente SVGs) perdieron sus dimensiones.
  3. Desobediencia directa de `AGENTS.md` (Regla 0.3) y `DESIGN.md`.
- **Reglas nuevas:**
  1. **Tailwind Prohibido**: No usar NUNCA clases de Tailwind. Usar solo componentes definidos en `dx-styles.css` (`card`, `btn-primary`, `vendor-section`).
  2. **Dimensiones SVG**: Siempre definir `width` y `height` inline o mediante clases nativas existentes.
  3. **Revisión de Layout**: Antes de crear una vista, revisar `layouts/app.blade.php` para confirmar qué assets están disponibles.
