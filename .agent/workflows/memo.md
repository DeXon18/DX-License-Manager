# Workflow: Generación de Memo de Aprendizaje

> Ruta: `.agent/workflows/memo.md`
> Trigger: Al terminar una tarea donde se tomó una decisión de diseño, arquitectura o preferencia del desarrollador — ejecutar `/memo`

---

## Objetivo

Capturar el conocimiento tácito que emerge de cada sesión — las preferencias del desarrollador, las decisiones de arquitectura tomadas y los patrones que funcionaron — en archivos pequeños y persistentes dentro de cada skill.

Este conocimiento no está en los manuales oficiales ni en las reglas generales. Surge de la conversación real. Sin los MEMOs se pierde entre sesiones. Con ellos el agente acumula contexto específico del proyecto.

---

## Cuándo Generar un MEMO

Generar un `/memo` cuando en la sesión ocurrió alguno de estos:

- El desarrollador corrigió al agente y explicó por qué
- Se tomó una decisión de arquitectura que no estaba en las reglas previas
- El desarrollador expresó preferencia explícita por un enfoque sobre otro
- Se descubrió un patrón específico del proyecto que debería repetirse
- Se encontró algo que NO funciona en este entorno concreto

**No generar MEMO** para cosas genéricas que ya están en las skills o en el AGENTS.md.

---

## Estructura del MEMO

Cada MEMO es un archivo pequeño — máximo 20 líneas. Un archivo por aprendizaje, no un archivo gigante con todo.

**Ruta:** `.agent/skills/[nombre-skill]/history/MEMO-[YYYY-MM-DD]-[tema-corto].md`

**Formato:**

```markdown
# MEMO — [Título descriptivo]

> Fecha: YYYY-MM-DD
> Skill: [nombre-skill]
> Contexto: [rama o feature donde surgió]

## Qué aprendí

[Descripción concisa — 2-4 líneas máximo]

## Regla derivada

[Formulado como instrucción directa para el agente]
Ejemplo: "En este proyecto, los controladores nunca contienen lógica de negocio.
Todo va en Services bajo app/Services/"

## Por qué (contexto del desarrollador)

[La razón que dio el desarrollador, si la dio]

## Referencia

[Archivo o commit donde se aplicó por primera vez]
```

---

## Ejemplos de MEMOs Reales para Este Proyecto

### Ejemplo 1 — Laravel Expert

```markdown
# MEMO — Verificación con Tinker antes de tests formales

> Fecha: 2026-04-29
> Skill: laravel-expert
> Contexto: feature/csv-importer

## Qué aprendí

El desarrollador prefiere verificar la lógica de negocio con Tinker
antes de escribir tests formales. Es más rápido para confirmar que
la lógica es correcta antes de invertir tiempo en el test.

## Regla derivada

Tras implementar un Service, verificar con Tinker en el contenedor
beta antes de proponer tests. Solo escribir tests cuando la lógica
esté confirmada como correcta.

## Por qué

"Prefiero ver que funciona antes de testear algo que puede estar mal"

## Referencia

ContractImportService.php — sesión 2026-04-29
```

### Ejemplo 2 — Docker Expert

```markdown
# MEMO — Permisos Samba vs Docker en backend/

> Fecha: 2026-04-29
> Skill: docker-expert
> Contexto: feature/laravel-base

## Qué aprendí

Los archivos creados por php-fpm (www-data, UID 33) no pueden ser
sobreescritos por Samba desde Windows. El workaround es ejecutar
chown desde el host Proxmox sobre /rpool/webs/.

## Regla derivada

Cuando Samba da EPERM en backend/, ejecutar desde Proxmox:
chown -R nobody:nogroup /rpool/webs/DX-License-Manager
chmod -R 775 /rpool/webs/DX-License-Manager
Nunca intentar chown desde dentro del contenedor — falla en LXC unprivileged.

## Por qué

LXC unprivileged no permite chown de archivos que no pertenecen al usuario del contenedor.

## Referencia

deploy.sh — fix aplicado sesión 2026-04-29
```

### Ejemplo 3 — UI/UX

```markdown
# MEMO — Diseño: fuente Inter + referencia HTMLs estáticos

> Fecha: 2026-04-30
> Skill: ui-ux-pro-max
> Contexto: rediseño UI

## Qué aprendí

El desarrollador eligió Inter como fuente principal del portal.
Los HTMLs estáticos aprobados están en infra/html/ y son la
referencia visual absoluta — el agente debe replicarlos en Blade
sin inventar estilos propios.

## Regla derivada

Antes de crear cualquier vista Blade, abrir el HTML estático
correspondiente en infra/html/ y replicar exactamente su estructura.
Fuente: Inter. IBM Plex Mono solo para datos técnicos (contratos, versiones).

## Por qué

"El agente ha ido haciendo en base a su criterio y no es el mío"

## Referencia

infra/html/01-login.html, 02-inicio.html, 03-herramientas.html, 04-admin.html
```

---

## Pasos del Workflow

### 1. Identificar el aprendizaje

Al final de la tarea, el agente identifica qué skill es la más relevante para el aprendizaje y formula la regla derivada en una sola frase imperativa.

### 2. Crear la carpeta history si no existe

```bash
mkdir -p .agent/skills/[nombre-skill]/history
touch .agent/skills/[nombre-skill]/history/.gitkeep
```

### 3. Escribir el MEMO

Crear el archivo siguiendo el formato definido arriba. Máximo 20 líneas. Concreto, sin relleno.

### 4. Actualizar INDEX.md de la skill (opcional)

Si la skill tiene un `INDEX.md` propio, añadir una línea referenciando el nuevo MEMO.

### 5. Commitear

```bash
git add .agent/skills/[nombre-skill]/history/
git commit -m "docs(memo): [título del aprendizaje]"
git push origin [rama-actual]
```

### 6. Confirmar al desarrollador

```
📝 MEMO generado
─────────────────────────────────
Skill:    [nombre-skill]
Archivo:  MEMO-YYYY-MM-DD-tema.md
Aprendizaje: [una línea]
─────────────────────────────────
```

---

## Cómo Usar los MEMOs en Sesiones Futuras

Al hacer `/start`, el agente lee el HANDOFF y el ROADMAP. Si la tarea toca una skill específica, debe también leer los MEMOs de esa skill:

```
Tarea: modificar ContractImportService
→ Cargar: laravel-expert
→ Leer también: .agent/skills/laravel-expert/history/
→ Aplicar los MEMOs antes de escribir código
```

Esto crea memoria acumulativa real — cada sesión el agente sabe más sobre las preferencias específicas del proyecto.
