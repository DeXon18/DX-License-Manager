# HANDOFF — DX License Manager

> Última actualización: 2026-05-04
> Rama activa: main
> Fase actual: Fase 0 — Verificación de Infraestructura

---

## Estado General

| Elemento                         | Estado                        |
| :------------------------------- | :---------------------------- |
| Repo GitHub `DX-License-Manager` | ❌ Por crear                  |
| Stack beta `beta.dxpro.es`       | ❌ Por levantar               |
| Stack prod `portal.dxpro.es`     | ❌ Por levantar               |
| Deploy automático GitHub Actions | ❌ Por configurar             |
| Laravel                          | ❌ No instalado — Fase 1      |
| Base de datos                    | ❌ No existe — Fase 1         |

---

## Qué se hizo en esta sesión

- Proyecto iniciado. Documentación base generada y reseteada al estado real.
- Ningún trabajo de infraestructura realizado aún.

---

## Tarea Inmediata — Empezar Aquí

**Fase 0 — Verificación de Infraestructura**

1. Crear repo nuevo en GitHub: `DeXon18/DX-License-Manager`
2. Configurar ramas `main` y `dev`
3. Copiar estructura base de carpetas y archivos al repo
4. Configurar GitHub Secrets (`SSH_HOST`, `SSH_USER`, `SSH_PRIVATE_KEY`, `SSH_PORT`)
5. Preparar el servidor LXC 600
6. Levantar stack beta con nginx sirviendo HTML estático
7. Levantar stack prod con nginx sirviendo HTML estático
8. Verificar deploy automático desde GitHub Actions

---

## Tareas Siguientes (orden estricto)

- **Fase 0** — Infraestructura base
Base técnica inicial: repositorio, ramas, estructura, Docker beta/prod, nginx y despliegue automático.

- **Fase 1** — CSS + Assets
Integración de estilos, fuentes, variables light/dark mode y componentes base.

- **Fase 2** — Layouts Blade + Laravel
Instalación de Laravel, Tailwind, Alpine.js, layout principal y nginx sirviendo Laravel.

- **Fase 3** — Login
Sistema de autenticación con JWT, roles, permisos, migraciones, seeders y tests.

- **Fase 4** — Importación CSV
Importación de contratos desde CSV, upsert, normalización, bajas, logs e historial.

- **Fase 5** — Inicio
Dashboard principal con métricas reales, caducidades próximas y accesos rápidos.

- **Fase 6** — Clientes
Gestión completa de clientes: contratos, licencias, contactos y certificados de cese firmados.

- **Fase 7** — Hub de Herramientas
Vista central de herramientas agrupadas por vendor, controladas por feature flags.

- **Fase 8** — Siemens
Herramientas Siemens: NX Suite, STAR-CCM+, HEEDS, COD y recursos internos/oficiales.

- **Fase 9** — Moldex3D
Herramienta Moldex3D para archivos `.mac`, Machine ID, auditoría IA y recursos.

- **Fase 10** — Dashboard del Sistema
Panel con métricas de PHP, nginx, MariaDB, Redis, almacenamiento, IA y Telegram.

- **Fase 11** — Usuarios y Acceso
CRUD de usuarios, roles, permisos y activación/desactivación de cuentas.

- **Fase 12** — Repositorio de Licencias
Archivo semanal de licencias procesadas, descarga ZIP e historial de archivado.

- **Fase 13** — Alertas y Notificaciones
Sistema de alertas por caducidad, destinatarios, historial de envíos y SMTP.

- **Fase 14** — Backups
Backups manuales y automáticos, historial y verificación de restauración.

- **Fase 15** — Integraciones IA
Configuración de Gemini, Deepseek, OpenRouter, Telegram Bot y FallbackChain.

- **Fase 16** — Logs y Auditoría
Logs de usuarios, errores del sistema y auditoría IA con filtros visibles en beta.

---

## Contexto Técnico Importante

- **El PC del desarrollador NO tiene PHP, Composer ni ningún runtime.** Todo corre en Docker dentro del LXC 600.
- **SSH al servidor:**
  - Desde casa: `ssh -p 22 root@192.168.50.60`
  - Desde fuera: `ssh -p 2222 root@81.0.53.128`
- **Docker siempre se ejecuta desde la raíz del proyecto** usando `--project-directory .`
- **Un archivo por respuesta** — no tocar más de un archivo a la vez.
- **Los docker-compose iniciales son solo nginx.**
- **php-fpm se añade en Fase 2**, cuando Laravel empieza a servirse desde nginx.
- **MariaDB se añade antes de las migraciones de autenticación/datos**, idealmente en Fase 2 o al inicio de Fase 3.
- **Redis se añade cuando sea necesario para sesiones, caché, colas o servicios auxiliares.**
- **Refactorizaciones:** antes de cualquier refactor importante, crear un tag en GitHub con el estado previo y documentar el motivo del cambio.

---

## Pendiente Sin Resolver

- Todo está pendiente — el proyecto parte desde cero.

---

## Estado de Archivos Clave

| Archivo                          | Estado                                    |
| :------------------------------- | :---------------------------------------- |
| `infra/.env.beta`                | ❌ Por crear en servidor (nunca en Git)   |
| `infra/.env.prod`                | ❌ Por crear en servidor (nunca en Git)   |
| `.agent/secrets/identities.json` | ⚠️ Pendiente de actualizar con datos reales |
| `backend/`                       | ❌ No existe — se crea en Fase 1          |

---

## Comandos Útiles para la Próxima Sesión

```bash
# SSH al servidor
ssh -p 22 root@192.168.50.60

# Navegar al proyecto (una vez clonado)
cd /opt/web-projects/DX-License-Manager

# Estado de los stacks (una vez levantados)
docker compose --project-directory . -f infra/docker-compose.beta.yml ps
docker compose --project-directory . -f infra/docker-compose.prod.yml ps
```
