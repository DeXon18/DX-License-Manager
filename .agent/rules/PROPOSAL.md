# Propuesta de Mejora de Reglas
> Ruta: `.agent/rules/PROPOSAL.md`
> Uso: El agente añade aquí propuestas cuando detecta que una regla está obsoleta, causa conflictos o puede optimizarse.
> El desarrollador revisa y aprueba antes de aplicar cualquier cambio.

---

## Instrucciones para el Agente

Cuando durante una tarea detectes que una regla existente:
- Causa un conflicto con otra regla
- Está desactualizada respecto al estado actual del proyecto
- Podría formularse mejor para ser más clara
- Falta una regla que habría evitado un error real

→ Añade una entrada aquí. **No modifiques la regla directamente.** El desarrollador decide si aplicarla.

---

## Formato de Propuesta

```markdown
### [FECHA] — [Título de la propuesta]
**Regla afectada:** [ruta del archivo y sección]
**Problema detectado:** [qué causa el problema o la ambigüedad]
**Propuesta:** [cómo quedaría la regla mejorada]
**Contexto:** [en qué tarea surgió este problema]
**Estado:** Pendiente de revisión
```

---

## Propuestas Pendientes de Revisión

*(vacío — se irá llenando con el uso)*

---

## Propuestas Aprobadas y Aplicadas

*(vacío — el desarrollador mueve aquí las que aprueba tras aplicarlas)*

---

## Propuestas Rechazadas

*(vacío — el desarrollador mueve aquí las que rechaza con una nota del motivo)*
