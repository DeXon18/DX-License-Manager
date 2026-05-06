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

- [ ] 1. Fase 6.4 — Certificados de cese firmados
  - [ ] Regla aplicada: AGENTS.md §7 (Seguridad de descargas)
  - [ ] Check pre-ejecución: Definir estructura de almacenamiento en `storage/app/certificates/`.
  - [ ] Evidencia: Subida de PDF/DOCX con asociación a contrato y descarga segura vía ID.
  - [ ] Rama: feature/clients-base

---

## Tareas Completadas Esta Sesión

- [x] 0. Verificación de memoria (last_brain leído y aplicado)
  - [x] Regla aplicada: AGENTS.md §0.8.1 (Ley 6)
  - [x] Check pre-ejecución: ¿Se leyó .agent/last_brain y ACTIVE_CONTEXT.md?
  - [x] Evidencia: Confirmación en las notas de sesión.

- [x] 1. Modelado de Contactos (Fase 6.3)
  - [x] Regla aplicada: clean-code §naming-semantico
  - [x] Check pre-ejecución: Eliminar redundancia del campo "empresa".
  - [x] Evidencia: Tabla `contacts` operativa en base de datos.
  - [x] Rama: feature/clients-base

- [x] 2. Gestión de Contactos (Backend + Frontend)
  - [x] Regla aplicada: impeccable §minimalismo-funcional
  - [x] Check pre-ejecución: Implementar persistencia de pestañas en `localStorage`.
  - [x] Evidencia: CRUD de contactos funcional con tabla compacta e iconos horizontales.
  - [x] Rama: feature/clients-base

- [x] 3. Datos de Prueba (DEMO)
  - [x] Regla aplicada: AGENTS.md §0.8.2
  - [x] Evidencia: `DemoContactSeeder` creado y ejecutado (vía comando manual por el usuario tras limpieza de `known_hosts`).
  - [x] Rama: feature/clients-base

---

## Notas de Sesión

- **Fase 6.3 Finalizada**: Se ha logrado una interfaz muy compacta y técnica para la gestión de contactos.
- **Persistencia de Navegación**: El sistema de pestañas ahora es "stateful" gracias a `localStorage`.
- **Infraestructura**: Se ha resuelto el problema de seguridad SSH `known_hosts` en el PC del desarrollador.
- **UX**: Optimizada la tabla de contactos para evitar solapamientos visuales y mejorar la legibilidad.
