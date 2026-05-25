# HANDOFF — DX License Manager
> Última actualización: 2026-05-25 17:00
> Sesión en: Indeterminado
> Rama activa: feature/ai-routing-hub

---

## Estado General

**Fase actual:** Fase 29 — Telemetría IA & Routing
**Stack beta:** ✅ Indeterminado
**Stack prod:** ✅ Indeterminado

---

## Qué se hizo en esta sesión

1. **AI Routing Hub**: Construido el panel de control centralizado (`admin/system/ai-routing`) para orquestar la IA.
2. **Telemetría de Cuotas**: Añadido `weekly_tokens_limit` a la tabla `ai_models`. Actualizada la vista de costes para mostrar el límite dinámico en barras de progreso.
3. **Catálogo Top Gratis**: Actualizado el seeder con los 11 mejores modelos gratuitos actuales (OpenRouter/Owl-Alpha, Nemotron, Laguna, DeepSeek V4).
4. **Refactorización UI**: Eliminado el layout tipo sidebar en la vista de Routing para maximizar el ancho del catálogo; el formulario de "Añadir Modelo" ahora está en su propia pestaña. Unificación visual con NOC Pro de las métricas numéricas y estado "Ilimitado / ∞".
5. **Migraciones & Seeder**: Ejecutados exitosamente a través de un endpoint temporal (ya eliminado).

---

## Qué falta por hacer (próxima sesión)

### Tarea inmediata (empezar aquí)
1. **Evaluar Merge**: Revisar si la rama `feature/ai-routing-hub` está lista para ser fusionada hacia `dev` y realizar el Pull Request o Merge correspondiente, asegurando que no queden rutas temporales olvidadas.

### Tareas siguientes
1. Continuar con la Fase 29 integrando perfiles de administración o seguir documentando métricas.

---

## Contexto técnico importante

- Los límites de tokens ahora se formatean en Tera, Giga o Mega (T/B/M). Si un modelo no tiene un límite establecido, se muestra con un estado estético `∞` y una barra verde del 0%.
- El seeder usa `updateOrCreate`, lo que significa que los modelos viejos pueden seguir existiendo en BD con límite "null". Todos ellos se renderizarán visualmente bien gracias a la corrección de diseño.

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

# Ver logs en tiempo real
docker compose --project-directory . -f infra/docker-compose.beta.yml logs -f dx-php-beta
```
