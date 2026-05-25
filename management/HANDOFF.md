# HANDOFF — DX License Manager
> Última actualización: 2026-05-25 09:55  
> Rama activa: dev

---

## Estado General

**Fase actual:** Mejoras Telemetría IA y Costes (Modelos Free & UI NOC Pro) Completada
**Stack beta:** ✅ operativo
**Stack prod:** ✅ operativo

---

## Qué se hizo en esta sesión

1. **Tabla y Modelo**: Se añadió la columna `model` a la tabla `ai_token_logs` para permitir métricas por modelo y precios dinámicos según `config/ai.php` (especialmente coste $0 para los `:free` de OpenRouter).
2. **UI NOC Pro**: Se refactorizó la vista `ai-costs.blade.php` migrando el panel de estadísticas al diseño industrial puro (`dx-v2-sys-dash-sec-layout`), resolviendo márgenes anidados, unificando tipografía y forzando asimetría matemática (`min-height`) en el bottom-layout para alinear perfectamente los listados.
3. **Mantenimiento**: Se borró la rama `feature/ai-cost-openrouter-free` local y remotamente, además de purgar 12 ramas antiguas/huérfanas que habían quedado colgadas de sesiones pasadas.
4. **Merge a dev**: Se completó el ciclo y se integró todo en `dev` tras documentar en `BACKLOG.md` y `CHANGELOG.md`.

---

## Qué falta por hacer (próxima sesión)

### Tarea inmediata (empezar aquí)
Consultar el `BACKLOG.md` o preguntar a Oskar por el siguiente requerimiento, dado que la fase actual ha quedado finalizada y documentada.

---

## Contexto técnico importante

- Los listados en la vista de costes de IA usan una arquitectura estricta en CSS (`dx-v2-sys-dash-sec-layout`). No volver a inyectar clases de tabla ni wrappers sin consultar la matriz de clases 6-layer.
- Se ha hecho limpieza a fondo de ramas (`git branch` limpio).
- Estamos en la rama `dev`.

---

## Bloqueos o problemas sin resolver

Ninguno.

---

## Estado de archivos clave

| Archivo | Estado |
|:---|:---|
| `infra/.env.prod` | ✅ configurado |
| `infra/.env.beta` | ✅ configurado |
| `backend/.env` | ✅ configurado |
| `backend/vendor/` | ✅ instalado |

---

## Comandos útiles para la próxima sesión

```bash
# Arrancar beta si está down
docker compose --project-directory . -f infra/docker-compose.beta.yml up -d

# Entrar al contenedor PHP
docker exec -it dx-php-beta sh

# Ver logs en tiempo real
docker compose --project-directory . -f infra/docker-compose.beta.yml logs -f nginx-beta
```
