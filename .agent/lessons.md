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
