---
name: loop
description: Execute the autonomous development loop (/spec, /build, /review). Use when user asks to "run loop", "start loop", or automate development.
---

# EJECUTAR BUCLE

## 1. Definir (`/spec`)
Ejecutar `/spec`. Agente preguntar objetivo/requisito/restricción. Usuario responder. Agente guardar `specs/<nombre>.md` (contrato).

## 2. Bucle Automático
Para iniciar el ciclo, el usuario debe pegar este comando exacto:
> "Loop /build and /review: build from the spec, review the build against the spec, fix whatever fails, then repeat until the review passes clean. Keep going on your own until it passes."

Agente ejecutar autónomo:
1. `/build` (construir spec).
2. `/review` (comparar vs spec).
3. Fallo → aplicar fix → volver a `/build`.
4. Repetir hasta pase limpio. Cero intervención.

## CONSEJO: Alta Autonomía
Dar permiso agente para iteraciones múltiples. Evitar paradas entre `/build` + `/review`.

## RESUMEN
- `/spec` → Definir exacto.
- Bucle `/build` + `/review` → Agente construir, evaluar y corregir solo.
