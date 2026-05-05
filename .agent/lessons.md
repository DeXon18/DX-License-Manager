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
