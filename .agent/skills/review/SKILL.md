---
name: review
description: Compare current build against a spec file requirement by requirement. Use when user asks to review a build or run /review.
---

# Review

## Goal
Compare the current build against `specs/<name>.md`. Go requirement by requirement and list every gap, bug, or missing piece, naming the exact spec item each one fails. If anything fails, write the specific fixes needed and hand them back so `/build` can address them. Only pass the build when every requirement in the spec is fully met.

## Workflow
1. When user runs `/review <name>`, read the spec file in `specs/<name>.md`.
2. Compare the current implementation against the spec.
3. Check EACH requirement one by one.
4. For every gap, bug, or missing piece, list it and name the exact spec item it fails.
5. If anything fails, write specific fixes needed and output them for the `/build` step.
6. Do not pass the build until every requirement is fully met.
