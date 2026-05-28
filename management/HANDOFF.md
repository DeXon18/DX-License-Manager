# HANDOFF — DX License Manager
> Última actualización: 2026-05-28 08:30  
> Sesión en: local  
> Rama activa: dev

---

## Estado General

**Fase actual:** Mantenimiento y Fixes en Producción (IA, Cron, DB)
**Stack beta:** ✅ running  
**Stack prod:** ✅ running (Recién recreada - DB limpia)

---

## Qué se hizo en esta sesión

1. **Fix Timeout IA**: Aumentado timeout de OpenRouter a 30s en `ClientAiNormalizationService`.
2. **Fallback IA Robustecido**: Implementado fallback automático nativo (hacia Gemini) ante errores `cURL 28` (timeout) y no solo para HTTP 429.
3. **HTTP-Referer IA**: Cambiado `HTTP-Referer` hardcodeado en peticiones de OpenRouter por `config('app.url')`.
4. **Hard Reset de Producción**: Recreada base de datos de producción desde cero (`migrate:fresh --seed --force`) para corregir inconsistencias (previo backup manual).
5. **Backups Producción Automatizados**: Añadida y configurada tarea cron en el servidor de producción (LXC 600) para ejecutar `backup-db.sh prod system` todos los días a las 03:00.
6. Fusionado `fix/ai-timeout` a `dev`.

---

## Qué falta por hacer (próxima sesión)

### Tarea inmediata (empezar aquí)
Confirmar despliegue de los cambios de `dev` a `main` (producción).

### Tareas siguientes
1. Esperar nuevas instrucciones.

---

## Contexto técnico importante

- Arquitectura de Docker: Tanto el stack Beta como Prod están montando el directorio actual local (`./backend`) en lugar de ramas clonadas separadas en servidor, lo que implica que el servidor refleja instantáneamente cualquier checkout y commit realizado en Windows para ambas webs. El desarrollador tiene este conocimiento mapeado.

---

## Bloqueos o problemas sin resolver

Ninguno

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
