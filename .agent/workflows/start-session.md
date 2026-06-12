---
description:
---

# Workflow: Inicio de Sesión

> Ruta: `.agent/workflows/start-session.md`
> Trigger: Al abrir el proyecto en Antigravity — ejecutar `/start`

---

## ⚠️ Arquitectura — Leer antes de ejecutar nada

**⚠️ REGLA CRÍTICA DE CARPETAS:**
Existen DOS carpetas físicas en el servidor:
- `/opt/web-projects/DX-License-Manager-DEV` (Desarrollo / Beta) -> Aquí trabajas SIEMPRE (rama `dev`).
- `/opt/web-projects/DX-License-Manager` (Producción) -> Intocable (rama `main`).
**NUNCA** trabajes ni modifiques archivos en la carpeta de Producción. Todos tus comandos y herramientas deben apuntar a la carpeta `-DEV`.

**El PC del desarrollador NO tiene PHP, Composer, Node, ni ningún runtime del proyecto instalado.**

Todo corre dentro de Docker en el servidor LXC 600 (`192.168.50.60`).

| Qué           | Dónde está                              | Cómo acceder                           |
| :------------ | :-------------------------------------- | :------------------------------------- |
| PHP 8.2       | Contenedor `dx-php-beta` en LXC 600     | SSH → `docker exec -it dx-php-beta sh` |
| MariaDB       | Contenedor `dx-mariadb-beta` en LXC 600 | SSH → `docker exec`                    |
| Redis         | Contenedor `dx-redis-beta` en LXC 600   | SSH → `docker exec`                    |
| Código fuente | `Z:\DX-License-Manager\` via Samba      | Editar directamente en Antigravity     |

**Nunca ejecutar en el PC local:** `php`, `composer`, `artisan`, `mysql`, `npm`.
**Siempre ejecutar en el servidor:** via SSH MCP o pidiendo al desarrollador que ejecute en el LXC.

---

## Objetivo

Sincronizar al agente con el estado real del proyecto antes de tocar cualquier archivo.
Sin este paso, el agente trabaja con contexto desactualizado.

---

> ⚠️ Este checklist se ejecuta al inicio de cada sesión para garantizar que la memoria y el flujo de inicio sean consistentes y trazables. **No ejecutar ninguna acción durante este paso.** Para el checklist consolidado de entrega, ver `management/CHECKLIST.md`.

---

## Pasos — Seguir en orden estricto

### 1. Leer el estado actual

Leer en este orden exacto. **No saltarse ningún archivo**, ni paso total 11 puntos.

1. `.agent/last_brain` — párrafo técnico denso del estado mental del agente anterior. Leer primero — da el contexto inmediato en segundos.
2. `.agent/memory/ACTIVE_CONTEXT.md` — estado activo, decisiones técnicas, handover.
3. `.agent/INDEX.md` — mapa de skills y reglas disponibles.
4. `.agent/secrets/identities.json` — **OBLIGATORIO.** infraestructura y URLs.
5. `management/HANDOFF.md` — qué se hizo en la última sesión y qué queda pendiente.
6. `management/ROADMAP.md` — fase actual y siguientes pasos.
7. `management/BACKLOG.md` — tareas en progreso.
8. `DESIGN.md` — **OBLIGATORIO.** Sistema de diseño del proyecto. Cualquier vista, componente o elemento visual debe seguir este documento. No crear nada visual sin haberlo leído.
9. `AGENTS.md` — **OBLIGATORIO.** reglas de operación activas. Confirmar que la Regla Cero está activa.
10. `.agent/lessons.md` — lecciones aprendidas de sesiones anteriores. Aplicar desde el primer momento.
11. `management/ARCHITECTURE.md` — **OBLIGATORIO.** Reglas de arquitectura y separación física entre los entornos de Desarrollo y Producción.

---

### 2. Verificar el entorno Git

```bash
git branch --show-current
git status
git log --oneline -5
```

Confirmar:

- Que no estás en `main` ni en `dev` directamente
- Que no hay cambios sin commitear de una sesión anterior
- Que la rama activa corresponde a la tarea del HANDOFF

⚠️ **Si hay cambios sin commitear → PARAR.** Preguntar al desarrollador qué hacer con ellos antes de continuar. No asumir, no descartar, no commitear sin preguntar.

---

### 3. Verificar los stacks Docker (solo si se va a trabajar con backend)

Ejecutar via SSH MCP en el LXC 600:

```bash
# Stack Beta (Entorno de Trabajo)
cd /opt/web-projects/DX-License-Manager-DEV
docker compose --project-directory . -f infra/docker-compose.beta.yml ps

# Stack Producción (Intocable)
cd /opt/web-projects/DX-License-Manager
docker compose --project-directory . -f infra/docker-compose.prod.yml ps
```

⚠️ **Si algún stack está caído → PARAR.** Informar al desarrollador antes de continuar. No intentar levantar contenedores sin autorización explícita.

---

### 4. Generar resumen de inicio

```
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
🟢 SESIÓN INICIADA — DX License Manager
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

📖 Lectura completada:
[x] last_brain          — contexto anterior cargado
[x] ACTIVE_CONTEXT.md   — estado activo recuperado
[x] INDEX.md            — routing de skills cargado
[x] identities.json     — infraestructura cargada
[x] HANDOFF.md          — handover leído
[x] ROADMAP.md          — fase actual identificada
[x] BACKLOG.md          — tareas en progreso revisadas
[x] DESIGN.md           — sistema de diseño activo
[x] AGENTS.md           — reglas de operación activas
[x] lessons.md          — [N lecciones aplicadas / "sin entradas aún"]
[x] ARCHITECTURE.md     — reglas de infraestructura cargadas

📍 Rama activa:       [nombre de la rama]
📦 Fase actual:       [fase X — nombre]
🔧 Última sesión:     [resumen en 1 línea del HANDOFF]
▶️  Siguiente tarea:  [primera tarea pendiente del HANDOFF]

⚠️  Pendiente sin resolver: [si hay algo, listarlo. Si no, "Ninguno"]

Stack beta:  [✅ running / ❌ down / ⚠️ sin verificar]
Stack prod:  [✅ running / ❌ down / ⚠️ sin verificar]
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
🔒 Modo estricto activo. No ejecuto sin confirmación explícita.
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```

---

### 5. Esperar instrucción del desarrollador

No iniciar ningún cambio hasta recibir confirmación explícita de la tarea a realizar.

---

## ⚠️ Si el contexto se llena a mitad de sesión

Señales de que el contexto está al límite:

- Respuestas muy lentas
- El modelo corta respuestas a mitad
- Errores de "maximum output token limit"
- Intentar actualizar varios archivos a la vez falla

**Qué hacer:**

1. Hacer `/log` inmediato con lo que haya en ese momento
2. Commit de todo lo que esté sin commitear
3. Cerrar la sesión
4. Abrir sesión nueva → `/start`

```bash
# Verificar logs de beta
ssh root@192.168.50.60 -p 22 "docker compose --project-directory /opt/web-projects/DX-License-Manager-DEV -f /opt/web-projects/DX-License-Manager-DEV/infra/docker-compose.beta.yml logs -f"
```

El HANDOFF y el CHANGELOG garantizan que no se pierde nada.
