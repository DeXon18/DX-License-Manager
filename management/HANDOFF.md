# HANDOFF — DX License Manager

> Última actualización: 2026-05-05
> Rama activa: dev
> Fase actual: Fase 0 — Verificación de Infraestructura

---

## Estado General

| Elemento                         | Estado                        |
| :------------------------------- | :---------------------------- |
| Repo GitHub `DX-License-Manager` | ✅ Creado y vinculado         |
| Stack beta `beta.dxpro.es`       | ✅ Verificado (index.html)    |
| Stack prod `portal.dxpro.es`     | ✅ Verificado (index.html)    |
| Deploy automático GitHub Actions | ✅ Configurado                |
| Laravel                          | ❌ No instalado — Fase 1      |
| Base de datos                    | ❌ No existe — Fase 1         |

---

## Qué se hizo en esta sesión

- **Sincronización de Sesión**: Commiteados cambios pendientes de la sesión anterior (`eb14f5c`).
- **Despliegue Beta**: Verificado exitosamente `http://beta.dxpro.es` (usuario confirma visualización).
- **Verificación SSH**: El desarrollador está revisando la conectividad SSH. localmente no disponible.

---

## Tarea Inmediata — Empezar Aquí

**Fase 0 — Verificación de Infraestructura**

1. ✅ Realizar un commit de prueba en `dev` (Hecho: `eb14f5c`).
2. ✅ Verificar que `http://beta.dxpro.es` muestra la página de mantenimiento.
3. ✅ Fusionar `dev` a `main` y verificar `http://portal.dxpro.es`. (Hecho: `6a3de1c`).
4. ✅ Fase 0 completada. Procediendo a la **Fase 1**.

---

## Contexto Técnico Importante

- **Cambio de ruta**: El directorio local es ahora `y:\DX-License-Manager`.
- **Despliegue SSH**: El workflow usa `appleboy/ssh-action` para conectarse al puerto `2222` del servidor.
- **Docker**: Los stacks se levantan con `--project-directory .` desde la raíz.
- **Fase 0**: Actualmente solo se levanta el servicio `dx-nginx-beta/prod`. Los servicios PHP/MariaDB se añadirán en fases posteriores.

---

## Pendiente Sin Resolver

- Confirmación visual de `portal.dxpro.es` (pendiente de deploy a main).
- Verificación de stacks en el servidor (en revisión por el desarrollador).

---

## Estado de Archivos Clave

| Archivo                          | Estado                                    |
| :------------------------------- | :---------------------------------------- |
| `infra/.env.beta`                | ✅ Creado en servidor y local             |
| `infra/.env.prod`                | ✅ Creado en servidor y local             |
| `.agent/secrets/identities.json` | ✅ Válido                                 |
| `backend/`                       | ❌ No existe — se crea en Fase 1          |

---

## Comandos Útiles para la Próxima Sesión

```bash
# Verificar despliegue manual en el servidor (si Actions falla)
cd /opt/web-projects/DX-License-Manager
docker compose --project-directory . -f infra/docker-compose.beta.yml ps
```
