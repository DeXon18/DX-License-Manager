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
- [ ] 1. Fase 7 — Hub de Herramientas (Planificación)
  - [ ] Regla aplicada: AGENTS.md §0.4 (Descomposición)
  - [ ] Check pre-ejecución: Revisar prototipo `03-herramientas.html`.
  - [ ] Evidencia: `implementation_plan.md` presentado y aprobado.
  - [ ] Rama: feature/tools-hub

---

## Tareas Completadas Esta Sesión

- [x] 0. Verificación de infraestructura y permisos de almacenamiento
- [x] 1. Implementación de lógica de nomenclatura Siemens NX
- [x] 2. Configuración de límites Nginx/PHP (100MB)
- [x] 3. Normalización de Hostname y Cliente a MAYÚSCULAS
- [x] 4. Registro en CHANGELOG y ROADMAP
- [x] 5. Fix de enlaces históricos COD (UUID)
- [x] 6. Implementación de subida de COD Firmado
- [x] 7. Implementación de borrado de certificados (BD + Archivos)
- [x] 8. Optimización visual de iconos (Horizontal Compacto)
- [x] 9. Ejecución de migración via SSH al LXC 600
- [x] 10. Diferenciación de Vendors y Refinamiento UI Moldex3D (Logo + Badge removal)
- [x] 11. Mejora de visibilidad de Versión en inventario (Badge técnico v2025)

---

## Notas de Sesión

- **Mecanismo Siemens**: La Parte 1 está cerrada. El sistema ya transforma y guarda archivos correctamente.
- **Límites de Subida**: Nginx y PHP ahora aceptan hasta 100MB, resolviendo el error 413.
- **Hostname**: Se ha implementado la regla de mayúsculas estrictas para los nombres de archivo.
- **Filesystem**: Se corrigieron los permisos de la carpeta `private` que causaban bloqueos de I/O.
