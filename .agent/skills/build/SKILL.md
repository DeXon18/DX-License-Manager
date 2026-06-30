---
name: build
description: Build exactly what is specified in a spec file. Use when user wants to implement a planned feature or asks to use /build.
---

# Build

## Goal
Read the spec in `specs/<name>.md` and build exactly what it describes. Do not add features, refactor unrelated code, or invent requirements that aren't in the spec.

## Workflow
1. When user runs `/build <name>`, read the corresponding spec file in `specs/<name>.md`.
2. Follow the specifications strictly. Implement the exact requirements.
3. Do NOT add extra features.
4. Do NOT refactor unrelated code.
5. Do NOT invent new requirements.
6. Build and verify the implementation according to the spec's definition of done.

## Output Format
When finished, list the specific requirements from the spec that were covered. This list will be used by the `/review` step to verify them one by one.
