# HANDOFF — DX License Manager

> Última actualización: 2026-05-05
> Rama activa: dev
> Fase actual: Fase 0 — Verificación de Infraestructura

---

## Estado General

| Elemento                         | Estado                        |
| :------------------------------- | :---------------------------- |
| Repo GitHub `DX-License-Manager` | ✅ Creado y vinculado         |
| Stack beta `beta.dxpro.es`       | ❌ Pendiente de primer deploy |
| Stack prod `portal.dxpro.es`     | ❌ Pendiente de primer deploy |
| Deploy automático GitHub Actions | ✅ Configurado                |
| Laravel                          | ❌ No instalado — Fase 1      |
| Base de datos                    | ❌ No existe — Fase 1         |

---

## Qué se hizo en esta sesión

- **Inicialización de Git**: Repositorio local vinculado con GitHub. Ramas `main` y `dev` creadas y sincronizadas.
- **Workflows**: Configurados pipelines de CI y despliegue automático vía SSH para Beta y Prod.
- **Preparación de Servidor**: Repositorio clonado en el servidor LXC 600 y archivos `.env` locales subidos manualmente por el desarrollador.
- **Secretos**: Configurados los secretos de GitHub Actions para el acceso SSH.

---

## Tarea Inmediata — Empezar Aquí

**Fase 0 — Verificación de Infraestructura**

1. Realizar un commit de prueba en `dev` y verificar que el workflow "Deploy Beta" se ejecuta correctamente.
2. Comprobar que `http://beta.dxpro.es:8002` muestra la página de mantenimiento estática.
3. Repetir el proceso en `main` para Producción (`http://portal.dxpro.es:8001`).
4. Una vez verificado el despliegue automático en ambos entornos, proceder a la **Fase 1**.

---

## Contexto Técnico Importante

- **Cambio de ruta**: El directorio local es ahora `y:\DX-License-Manager`.
- **Despliegue SSH**: El workflow usa `appleboy/ssh-action` para conectarse al puerto `2222` del servidor.
- **Docker**: Los stacks se levantan con `--project-directory .` desde la raíz.
- **Fase 0**: Actualmente solo se levanta el servicio `dx-nginx-beta/prod`. Los servicios PHP/MariaDB se añadirán en fases posteriores.

---

## Pendiente Sin Resolver

- Primer despliegue exitoso vía GitHub Actions (pendiente de trigger).

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
