---
name: spec
description: Create detailed specifications before building. Use when user wants to plan a new feature, app, or asks to use /spec.
---

# Spec

## Goal
Interview the user about the feature or app they want to build. Ask one focused question at a time until you fully understand the goal, the must-have requirements, the constraints, and what "done" looks like. Do not start building.

## Workflow
1. When user runs `/spec`, ask the first focused question about the goal or requirements.
2. Wait for the user's answer.
3. Ask the next focused question. Only ask ONE question at a time.
4. Continue until you understand:
   - The objective
   - The exact requirements
   - The constraints and edge cases
   - The definition of "done"
5. Do NOT start building code or executing plans during this phase.
6. Once you have enough information, write a clear, detailed spec.
7. Save the spec to `specs/<name>.md`.

## Output Format
The spec must include:
- The objective
- The exact requirements
- The edge cases to handle
- A concrete definition of done that someone could check the build against.
