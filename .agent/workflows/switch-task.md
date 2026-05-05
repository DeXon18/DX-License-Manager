# Workflow: Cambio de Tarea
> Ruta: `.agent/workflows/switch-task.md`
> Trigger: Cuando el desarrollador introduce una tarea que no pertenece a la rama activa — ejecutar `/switch`

---

## El Problema que Resuelve Este Workflow

El agente tiene una tendencia natural a continuar trabajando en la rama activa aunque la nueva petición del desarrollador no tenga nada que ver con ella. El resultado: código de dos funcionalidades distintas mezclado en la misma rama, PRs imposibles de revisar, y pérdida del principio "una rama por funcionalidad".

Este workflow es el cortafuegos entre tareas. Se ejecuta siempre que se detecte un cambio de contexto.

---

## Detección Proactiva — Antes de Empezar Cualquier Tarea Nueva

**Antes de ejecutar cualquier petición nueva, el agente se hace esta pregunta:**

```
¿El trabajo que me están pidiendo pertenece a la rama en la que estoy?
```

Si la respuesta es NO o NO ESTOY SEGURO → ejecutar este workflow antes de tocar nada.

### Señales de que hay un cambio de tarea

El agente debe detectar estas señales en el mensaje del desarrollador:

| Señal | Ejemplo |
|:---|:---|
| Cambio de módulo | Estaba en `feature/auth-login` y piden trabajar en Docker |
| Cambio de propósito | Estaba en `feature/migrations-base` y piden arreglar un bug de nginx |
| Nueva funcionalidad no relacionada | Estaba en `feature/csv-importer` y piden crear el modelo de licencias |
| Petición explícita de cambio | "Ahora vamos a...", "Cambia de tema", "Olvida eso, necesito..." |
| La rama activa no describe lo que se pide | `feature/auth-login` y la petición es sobre migraciones |

### Señales de que NO hay cambio de tarea

- La petición es un sub-paso de la tarea actual
- La petición corrige algo que se acaba de romper dentro de la misma feature
- La petición añade un test para código de la rama actual

---

## Pasos del Workflow

### 1. Detectar y Parar

Antes de escribir una sola línea de código o ejecutar un comando, parar y mostrar:

```
🔀 CAMBIO DE TAREA DETECTADO
──────────────────────────────────────────
Rama activa:    feature/[nombre-actual]
Nueva petición: [descripción breve de lo que pide el desarrollador]

Estas dos cosas no pertenecen a la misma rama.
Antes de continuar, hay que cerrar la rama actual correctamente.
¿Procedo con el cierre?
```

Esperar confirmación. No ejecutar nada hasta recibir "sí".

---

### 2. Cerrar la Rama Actual

#### 2a. Verificar estado del trabajo en curso

```bash
git status
git stash list
```

**Escenario A — Todo commiteado (lo ideal):**
```bash
# git status muestra: "nothing to commit, working tree clean"
# Continuar al paso 2b directamente
```

**Escenario B — Hay cambios sin commitear:**

Preguntar al desarrollador:
```
⚠️ Hay cambios sin commitear en la rama actual:
[lista de archivos de git status]

Opciones:
1. Commitearlos ahora (recomendado si el trabajo está completo)
2. Guardarlos en stash y recuperarlos después
3. Descartarlos (⚠️ se perderán)

¿Qué hacemos?
```

Si elige commitear:
```bash
git add [archivos concretos]
git commit -m "chore(wip): work in progress before task switch"
```

Si elige stash:
```bash
git stash push -m "WIP [nombre-rama]: [descripción breve]"
```

Si elige descartar → confirmar con formato de acción destructiva (ver AGENTS.md 0.6).

#### 2b. Hacer push de la rama actual

```bash
git push origin [nombre-rama-actual]
```

#### 2c. Actualizar el CHANGELOG con lo que quedó hecho

Añadir una entrada `/log` mínima documentando el estado en que queda la tarea:
- Si está completa → marcarla como completada
- Si está a medias → documentar exactamente hasta dónde llegó y qué falta

```bash
git add management/CHANGELOG.md
git commit -m "docs(log): [rama-actual] — estado al cambiar de tarea"
git push origin [nombre-rama-actual]
```

---

### 3. Volver a dev

```bash
git checkout dev
git pull origin dev
```

Verificar que `dev` está limpio y al día antes de continuar.

---

### 4. Crear la Nueva Rama

Determinar el nombre correcto para la nueva tarea siguiendo la convención:

```
feature/nombre-corto    ← nueva funcionalidad
fix/nombre-corto        ← corrección de bug
chore/nombre-corto      ← mantenimiento, config, docs
```

```bash
git checkout -b [nueva-rama]
```

---

### 5. Cargar las Skills Correctas para la Nueva Tarea

Antes de empezar, revisar la tabla de skills del AGENTS.md sección 10 y cargar las que corresponden a la nueva tarea. No asumir que las skills cargadas para la tarea anterior siguen siendo válidas.

---

### 6. Confirmar el Cambio al Desarrollador

```
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
🔀 CAMBIO DE TAREA COMPLETADO
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Rama cerrada:  feature/[nombre-anterior]  ✅ pusheada
Rama nueva:    feature/[nombre-nuevo]     ✅ creada desde dev

Estado anterior: [completo / WIP commiteado / WIP en stash]
Skills cargadas: [lista de skills para la nueva tarea]

Listo para empezar: [descripción breve de la nueva tarea]
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```

---

## Casos Especiales

### La tarea anterior estaba bloqueada (no hay nada que commitear)

Si se cambió de tarea porque la anterior estaba bloqueada por un problema sin resolver:

1. Documentar el bloqueo en el CHANGELOG con `### Pending`
2. Hacer push de la rama aunque esté incompleta
3. Anotar en el HANDOFF que esa rama tiene trabajo pendiente y por qué
4. Crear la nueva rama normalmente

### El desarrollador quiere volver a la tarea anterior después

```bash
git checkout feature/[nombre-anterior]
git pull origin feature/[nombre-anterior]
# Si había stash:
git stash list
git stash pop stash@{0}  # o el número correcto
```

Este escenario se convierte en un nuevo `/switch` ejecutado al revés.

### Cambio de tarea al final del día (antes de `/end`)

Si el cambio de tarea ocurre justo antes de cerrar sesión, ejecutar `/switch` igualmente — no omitirlo "porque total ya termina la sesión". El HANDOFF necesita reflejar en qué rama quedó el trabajo y en qué estado.

---

## Lo Que Nunca Debe Pasar

- ❌ Empezar código de una nueva funcionalidad sin haber creado una rama nueva
- ❌ Hacer commits de dos features distintas en la misma rama
- ❌ Ignorar que la rama activa no corresponde a la nueva tarea
- ❌ Asumir que "es solo un cambio pequeño, no necesita rama nueva"
- ❌ Cambiar de rama sin pushear el trabajo actual primero
