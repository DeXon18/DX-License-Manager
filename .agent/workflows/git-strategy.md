# 🔷 GIT — ESTRATEGIA DE RAMAS & COMMITS

Este protocolo establece la convención de control de versiones y checkpoints obligatorios para las fases de desarrollo.

**Rama única de trabajo:** `{rama}`

## Convención de commits

Usar prefijo `{prefix}:` para todos los commits de esta fase:

```
{prefix}({fase}.0): descripción -- contexto
{prefix}({fase}.2): descripción -- contexto
{prefix}({fase}.5): descripción -- contexto
{prefix}({fase}-hardening): descripción
{prefix}({fase}-docs): descripción
```

## Checkpoints de commit en `{rama}`

No acumular trabajo sin commitear. Puntos de commit recomendados:

- [ ] **Checkpoint A** — tras subfases iniciales *(descripción breve)*
- [ ] **Checkpoint B** — tras subfases intermedias *(descripción breve)*
- [ ] **Checkpoint C** — tras subfases avanzadas *(descripción breve)*
- [ ] **Checkpoint D** — *(añadir o eliminar según alcance)*
- [ ] **Commit final** — tras hardening + documentación

## Backup de seguridad antes de iniciar

- [ ] Tag git en estado actual: `git tag backup/pre-{fase}`
- [ ] Push del tag al remoto: `git push origin backup/pre-{fase}`

---

## 🔷 RELEASES (tags en `{rama}`)

- [ ] **Tag v{version}-rc1** — tras Checkpoint intermedio *(parcial)*
  - [ ] `git tag v{version}-rc1 && git push origin v{version}-rc1`
- [ ] **Tag v{version}-rc2** — tras Checkpoint final *(completo, previo a hardening)*
  - [ ] `git tag v{version}-rc2 && git push origin v{version}-rc2`
- [ ] **Tag v{version}** — tras commit final + verificación de criterios de aceptación
  - [ ] `git tag v{version} && git push origin v{version}`
  - [ ] CHANGELOG actualizado con lista de subfases completadas.
  - [ ] Nota de release: *(criterios de aceptación específicos del proyecto)*

---

## Variables

| Variable | Ejemplo | Descripción |
|---|---|---|
| `{rama}` | `dev` | Rama principal de trabajo |
| `{prefix}` | `prefix` | Prefijo del tipo de cambio (ej: `ai`, `css`, `fix`) |
| `{fase}` | `22` | Número o nombre de la fase |
| `{version}` | `v2.22.0` | Versión semver del release |

> La cantidad de checkpoints y RCs es orientativa — se ajusta según el alcance real de la fase.
