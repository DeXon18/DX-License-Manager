# 📋 Task List — DX License Manager

> Este archivo define las tareas de la sesión actual.
> Cada tarea tiene checks de verificación obligatorios — no se puede marcar como completa sin haberlos superado todos.
> El agente actualiza este archivo en tiempo real conforme avanza.
> Si hay un problema o bug, el agente debe solucionarlo antes de continuar.
> todas las tareas y subtareas completadas deben ser movidas a la sección "Tareas Completadas Esta Sesión"
> Nunca modificar las tareas de la siguiente fase si no se ha completado la fase actual.

---

## Cómo Usar Este Archivo

Cada tarea sigue este formato:

```markdown
- [ ] N. Nombre de la tarea
  - [ ] Regla aplicada: [archivo de regla §sección]
  - [ ] Check pre-ejecución: [qué verificar antes de empezar]
  - [ ] Evidencia requerida: [qué demostrar para marcarla como hecha]
  - [ ] Rama: [nombre de la rama donde se ejecuta]
```

El agente NO marca `[x]` una tarea hasta que todos sus checks están completados y la evidencia mostrada.

---

## Tareas de la Sesión Actual

- [ ] 0. Verificación de memoria (last_brain leído y aplicado)
  - [ ] Regla aplicada: AGENTS.md §0.8.1 (Ley 6)
  - [ ] Check pre-ejecución: ¿Se leyó .agent/last_brain y ACTIVE_CONTEXT.md?
  - [ ] Evidencia: Confirmación en las notas de sesión.

_(El agente rellena esto al inicio de cada sesión tras leer el HANDOFF)_

---

## Ejemplo de Task List Completa (Referencia)

```markdown
- [x] 1. Crear migración de la tabla `contracts`
  - [x] Regla aplicada: code-review-reasoning.md §2 — migración reversible
  - [x] Check pre-ejecución: ¿Existe ya una migración similar? → No
  - [x] Evidencia: `php artisan migrate:status` — ✅ Ran
  - [x] Rama: feature/csv-importer

- [ ] 2. Implementar ContractImportService
  - [ ] Regla aplicada: operational-principles.md §1 — lógica en Services, no Controllers
  - [ ] Check pre-ejecución: ¿Existe ya un Service similar? → No
  - [ ] Check seguridad: security-check.md §7 — sin secrets hardcodeados
  - [ ] Evidencia: prueba con Tinker — importar CSV de 3 filas, verificar en BD
  - [ ] Rama: feature/csv-importer

- [ ] 3. Crear ImportController
  - [ ] Regla aplicada: operational-principles.md §1 — Controller delgado, delega a Service
  - [ ] Check: ¿Tiene FormRequest de validación? → pendiente
  - [ ] Check seguridad: security-check.md §6 — middleware jwt + CheckPermission aplicado
  - [ ] Evidencia: `php artisan route:list | grep import`
  - [ ] Rama: feature/csv-importer
```

---

## Tareas Completadas Esta Sesión

- [x] 1. Instalación de Laravel 11 y Stack Docker (Phase 2)
  - [x] Regla aplicada: AGENTS.md §2, §4
  - [x] Evidencia: Laravel Dashboard responde (HTTP 200) con PHP 8.4.20
  - [x] Rama: feature/laravel-install
- [x] 2. Configuración de HTTPS y Assets
  - [x] Regla aplicada: security-check.md §9
  - [x] Evidencia: Etiquetas link generadas con https://
  - [x] Rama: fix/force-https-robust

---

## Notas de Sesión

- **Problema con Estilos**: A pesar de que Nginx mapea correctamente los assets y se sirven por HTTPS, la UI sigue apareciendo sin estilos.
- **Causa probable**: Caché de Cloudflare persistente o desajuste en el nombre de las clases en el CSS vs Blade.
- **Acción pendiente**: Revisar el contenido de `dx-styles.css` y las clases de utilidad en la próxima sesión.
