# HANDOFF — DX License Manager
> Última actualización: 2026-06-01 12:15  
> Sesión en: Windows local (Z: drive mapeado)  
> Rama activa: `dev`

---

## Estado General

**Fase actual:** Bugfix Tour Persistence ✅ COMPLETADA  
**Stack beta:** Operativo  
**Stack prod:** Operativo (actualizado con Phase 3 security + env decoupling)

---

## Qué se hizo en esta sesión

1. **Deploy a Producción (main)** — Se realizó el merge de `dev` a `main` y se desplegó todo el ecosistema (incluyendo Desacoplamiento de entornos y Hardening de Seguridad). 
2. **Recreación en Prod** — Se recrearon los contenedores necesarios, se ajustaron permisos de `docker.sock` y se reinició Nginx para activar cabeceras CSP.
3. **Bugfix (Tour persistence)** — Identificado y solucionado el bucle infinito del Tour de Driver.js. Al faltar `credentials: 'same-origin'`, la petición AJAX fallaba la autenticación JWT y el estado `has_seen_tour` nunca se guardaba. 
4. **Merge de Fix** — Subido a PR #28 y mergeado a `dev`. Limpieza de ramas ejecutada.

---

## Qué falta por hacer (próxima sesión)

### Tarea inmediata (empezar aquí)
Consultar con Oskar si desea hacer push del fix del tour a Producción (`main`) directamente, o esperar a acumular más cambios en `dev`.

---

## Contexto técnico importante

- La API JWT en Laravel requiere explícitamente `credentials: 'same-origin'` o `'include'` en llamadas `fetch` nativas desde JS para enviar la cookie en peticiones AJAX no generadas por Axios.
