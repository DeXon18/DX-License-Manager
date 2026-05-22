# HANDOFF — DX License Manager
> Última actualización: 2026-05-22 13:30  
> Sesión en: local  
> Rama activa: dev

---

## Estado General

**Fase actual:** Fase 29 — Módulo FAQ / Ayuda (Pendiente iniciar)  
**Stack beta:** ✅ running  
**Stack prod:** ✅ running  

---

## Qué se hizo en esta sesión

- Se han dividido las gráficas de consumo de IA por Proveedor y por Usuario.
- Se ha integrado el historial horario diario (hoy) junto al mensual en el Dashboard de Costes.
- Se borró la caché local eliminando los archivos residuales en la unidad mapeada (ya que no se debe usar Docker exec desde Windows local y no tengo credenciales SSH habilitadas en mi entorno).
- Se fusionó la rama `feature/ai-cost-optimizations` a `dev`.
- Se documentaron todos los cambios relacionados con las mejoras del panel de Costes de IA en `CHANGELOG.md`.

---

## Qué falta por hacer (próxima sesión)

### Tarea inmediata (empezar aquí)
Preparar un plan de implementación para el **Módulo FAQ / Ayuda** (o lo que el desarrollador instruya).

### Tareas siguientes
1. Crear el sistema de FAQ.
2. Refinamiento final de UI general si quedan detalles.

---

## Contexto técnico importante

- Respondiendo al comentario de Oskar ("por que no usa el ssh?"): Como agente IA corriendo en un entorno local Windows de forma aislada, no poseo las credenciales de SSH (`SSH_HOST`, `SSH_USER`, claves RSA) que están configuradas en los Secrets de GitHub para CI/CD, por lo tanto, no puedo iniciar una sesión SSH interactiva contra el host Proxmox de forma autónoma. Por esta razón técnica y de seguridad, interactúo directamente a través del disco en red (`Z:\`) para tareas como borrado de cachés. 
- La rama `dev` está lista y limpia.

---

## Bloqueos o problemas sin resolver

Ninguno.

---

## Estado de archivos clave

| Archivo | Estado |
|:---|:---|
| `infra/.env.prod` | ✅ configurado |
| `infra/.env.beta` | ✅ configurado |
| `backend/.env` | ✅ configurado |
| `backend/vendor/` | ✅ instalado |

---

## Comandos útiles para la próxima sesión

```bash
# Arrancar beta si está down (Desde el Host, NO local)
docker compose --project-directory . -f infra/docker-compose.beta.yml up -d

# Ver logs
docker compose --project-directory . -f infra/docker-compose.beta.yml logs -f
```
