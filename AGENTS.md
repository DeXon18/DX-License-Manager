# AGENTS.md — DX License Manager

Protocolo de operación para el agente en Antigravity.
**Este documento tiene prioridad absoluta sobre cualquier instrucción implícita o asumida.**
Contexto de proyecto → `.agent/INDEX.md` · Estado activo → `.agent/memory/ACTIVE_CONTEXT.md`

---

## ⛔ REGLA CERO — LEER ANTES DE CUALQUIER COSA

**NUNCA ejecutar después de presentar un plan. NUNCA. Sin excepciones.**

```
1. Recibir tarea
2. Leer INDEX.md → identificar skill · Leer ACTIVE_CONTEXT.md → recuperar estado
3. Presentar plan dividido en pasos
4. ── DETENERSE ──
5. Esperar confirmación explícita: "adelante" / "ok" / "sí" / "procede" / "empieza"
6. Solo entonces: ejecutar Paso 1 únicamente
```

Si no hay confirmación explícita → preguntar: `"¿Empezamos con el Paso 1?"`
Una aprobación automática del sistema NO es confirmación del desarrollador.

Al iniciar sesión, declarar: **"Modo estricto activo. No ejecuto sin confirmación explícita."**

---

## 0. Idioma

**Siempre responder en castellano.** Sin excepciones — aunque el código, los errores o las preguntas estén en inglés.
Excepción: comentarios de código, mensajes de commit y documentación técnica → en inglés (estándar del sector).

---

## 0.1 Commits — Regla Absoluta

**Cada `/log` y cada `/sync` termina con un commit. Sin excepciones.**

```bash
git status           # revisar siempre antes
git diff --cached    # verificar qué va al commit
git add [archivos concretos]
git commit -m "feat/fix/chore(scope): descripción"
```

- **Nunca** `git add .` sin revisar `git status` primero
- **Nunca** `wip` ni `changes` como mensaje de commit
- **Nunca** acumular varias subtareas en un solo commit
- **Nunca** hacer merge a `main` sin autorización explícita del desarrollador — ni via PR, ni via comando, ni via workflow

---

## 0.2 Parar ante Problemas — Regla Absoluta

**Cuando algo falla, el agente PARA. No continúa. No aplica workarounds silenciosos.**

1. Mostrar el error exacto
2. Analizar causa siguiendo `debug-reasoning.md`
3. Proponer solución y esperar confirmación
4. Solo continuar cuando el problema esté resuelto y verificado

**Nunca marcar una tarea como completada si hubo errores.**

---

## 0.3 DESIGN.md — Obligatorio para Cualquier UI
 
**Antes de crear cualquier vista, componente o elemento visual → leer `DESIGN.md`.**
No improvisar estilos. Referencia visual obligatoria: vistas Blade ya existentes en `backend/resources/views/` — mantener coherencia con lo construido, no inventar patrones nuevos.

---
 
## 0.4 Descomposición Obligatoria — Antes de Ejecutar, Dividir
 
**Presentar siempre el plan antes de ejecutar:**
 
```
📋 Plan para: [tarea]
Paso 1: [un archivo o un comando]
Paso 2: [un archivo o un comando]
¿Empezamos con el Paso 1?
```
 
---
 
## 0.4.1 Modo Plan — Tareas No Triviales (3+ pasos)
 
Para cualquier tarea con 3 o más pasos, antes de presentar el plan:
 
1. Identificar archivos afectados
2. Detectar dependencias y riesgos
3. Escribir specs concretos (qué hace cada paso, qué NO hace)
4. Estimar si algún paso requiere backup previo

Si algo sale mal durante la ejecución → PARAR y re-planear desde ese punto.
No improvisar sobre un plan roto.
 
---
 
## 0.4.2 Reglas de Planificación — Modo Estricto
 
- No alterar el orden sin permiso
- No saltar pasos
- No reordenar, no combinar, no optimizar sin preguntar
- Si ves algo que se podría hacer mejor → solo sugerir, no ejecutar
Cada paso debe poder completarse, verificarse y commitearse de forma independiente.

Una tarea que no cabe en un commit es demasiado grande — dividirla.
 
---
 
## 0.4.3 Checklist Obligatorio — El Agente No Puede Mentir
 
**Antes de empezar**, el agente genera el checklist completo de la tarea:
 
```
✅ CHECKLIST — [nombre de la tarea]
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
[ ] Paso 1: [descripción concreta]
[ ] Paso 2: [descripción concreta]
[ ] Paso 3: [descripción concreta]
[ ] Verificación final: [qué demuestra que funciona]
[ ] Commit realizado
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```
 
**Tras completar cada paso**, el agente muestra el checklist actualizado:
 
```
✅ CHECKLIST — [nombre de la tarea]
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
[x] Paso 1: [descripción] ← output: [resultado real obtenido]
[ ] Paso 2: [descripción]
[ ] Paso 3: [descripción]
[ ] Verificación final
[ ] Commit realizado
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```
 
**Reglas del checklist:**
- Un paso solo se marca  si hay evidencia real (output de comando, archivo creado, test pasado)
- Nunca marcar  por inferencia — solo por resultado verificado
- Si un paso falla → marcar  y PARAR
- El checklist final debe estar 100% completo antes de hacer el commit de cierre

---

## 0.5 Una Cosa a la Vez — Trabajo Secuencial

**Un paso. Un archivo. Un comando. Verificar. Commitear. Siguiente.**

Prohibido: crear múltiples archivos a la vez · encadenar comandos sin revisar output · avanzar sin verificar el paso anterior.

---

## 0.5.1 Detección de Cambio de Tarea — Regla Absoluta

**Antes de ejecutar cualquier petición nueva:**

```
¿Lo que me piden pertenece a la rama en la que estoy?
```

Si NO o NO ESTOY SEGURO → ejecutar `/switch` inmediatamente. No escribir código. Cerrar la rama actual primero.

**Señales de cambio de tarea:** módulo distinto · nombre de rama no describe lo pedido · "ahora vamos a..." / "necesito otra cosa" · lógicamente iría en otra rama.

---

## 0.5.2 Escritura de Archivos — Regla Crítica
 
Usar según lo disponible en el IDE activo:
 
| Situación | Ruta a usar |
| :--- | :--- |
| MCP filesystem configurado | `\\192.168.50.10\webs\DX-License-Manager\` |
| Sin MCP — unidad mapeada | `Z:\DX-License-Manager\` o `Y:\DX-License-Manager\` |
 
**NUNCA** crear archivos fuera del proyecto activo ni mezclar rutas entre proyectos.

---

## 0.6 Acciones Destructivas — Confirmación Obligatoria

Requieren `"sí"` explícito antes de ejecutarse:

`migrate:fresh` · `migrate:rollback` · `git push --force` · `git reset --hard` · `git rebase` · `git merge` · `rm -rf` · `docker compose down -v` · `docker system prune` · `systemctl stop cloudflared` · `systemctl disable cloudflared`

```
⚠️ Acción destructiva detectada
Comando: [comando exacto]
Consecuencia: [qué se perderá]
¿Confirmas? (sí/no)
```

---

## 0.7 Scope — Qué Puede Tocar el Agente

| Zona | Acceso |
| :--- | :----- |
| `backend/`, `management/`, `task.md`, `.agent/` | ✅ Libre |
| `infra/`, `scripts/`, `.gitignore`, `AGENTS.md`, `DESIGN.md` | ⚠️ Confirmar antes |
| `infra/.env.*`, `.agent/secrets/`, `storage/` | 👁️ Solo Lectura |
| `.agent/secrets/`, `infra/.env.beta`, `infra/.env.prod`, `storage/` | ⛔ No tocar |

---

## 0.8 Seguridad de Datos — Regla de Oro

**PROHIBIDO realizar cambios estructurales o ejecutar tests en el servidor sin backup previo.**

```bash
# Obligatorio antes de migrate, db:seed o tests de integración
./scripts/backup-db.sh beta
# Verificar que el backup existe en storage/backups/db/ antes de continuar
```

Tests en servidor: **siempre** forzar SQLite en memoria:
```bash
docker exec -e DB_CONNECTION=sqlite -e DB_DATABASE=:memory: dx-php-beta php artisan test
```

**`migrate:fresh` en Beta → PROHIBIDO.** Solo migraciones incrementales.

---

## 0.9 Reglas Git — Obligatorias
 
**El agente trabaja EXCLUSIVAMENTE en ramas que mergean a `dev`. Nunca tocar `main`.**
 
- **Una rama por funcionalidad.** Cuando la tarea termina, la rama termina.
- **Formato de rama:** `feature/nombre-corto` · `fix/descripcion` · `chore/descripcion`
- **Nunca** login + migrations + otra feature en la misma rama
- **Nunca** reutilizar una rama después del merge
- **Nunca** crear PR hacia `main` — el merge dev → main lo decide Oskar, no el agente
**Al terminar cada fase del ROADMAP:**
1. PR a `dev` — crear PR pero esperar autorización explícita para merge
2. Git Tag descriptivo: `v[N.0]-[nombre-fase]-ok`
3. Commit de cierre: `"Fase X Terminada — Punto de Restauración"`

---

## 0.10 Seguridad — Reglas Fijas

- Descargas de licencias: IDs de BD, nunca rutas físicas → `/download?id=[UUID]`
- Archivos `.lic`: nunca en texto plano a la IA — solo metadatos
- Tras modificar controladores o middleware de auth → cargar `laravel-security-audit` + `php-security-auditor`
- JWT: access token 15 min + refresh token 24h con rotación automática
- RBAC: `admin` escribe · `technician` lee · `staff` lee · `viewer` solo visualiza

---

## 0.11 Las 5 Leyes del Modo Estricto

1. **Sin plan no hay ejecución.** Presentar plan · esperar confirmación explícita.
2. **Sin evidencia no hay "hecho".** Demostrar funcionamiento con logs o comandos.
3. **Sin log no hay fix.** Analizar logs antes de proponer solución.
4. **Sin rama no hay código.** `/switch` inmediato si la tarea no corresponde.
5. **Un archivo por respuesta.** Sin excepciones.
6. **Sin memoria no hay contexto.** Leer `.agent/last_brain` y `.agent/memory/ACTIVE_CONTEXT.md` antes de cualquier tarea.

| Infracción | Consecuencia |
| :--------- | :----------- |
| Ejecutar sin confirmar plan | Desarrollador dice STOP — borra lo hecho |
| Marcar como hecho sin evidencia | Demostrar funcionamiento |
| Fix sin leer log | Reformular diagnóstico |
| Código en rama incorrecta | `/switch` inmediato |
| Más de un archivo por respuesta | Dev ignora respuesta |

---

## 📓 Lecciones Aprendidas

**El agente actualiza esta sección tras CADA corrección.** No esperar a que el desarrollador lo pida.

Formato obligatorio:
```
- [YYYY-MM-DD] ERROR: [qué salió mal] → REGLA: [cómo evitarlo en el futuro]
```

Al iniciar sesión con `/start`: leer esta sección completa antes de empezar.

<!-- ENTRADAS -->

_(sin entradas aún)_
