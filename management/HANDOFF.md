# HANDOFF — DX License Manager

> Última actualización: 2026-05-05
> Rama activa: dev
> Fase actual: Fase 2 — Layouts Blade + Laravel ✅

---

## Estado General

| Elemento                         | Estado                        |
| :------------------------------- | :---------------------------- |
| Repo GitHub `DX-License-Manager` | ✅ Creado y vinculado         |
| Stack beta `beta.dxpro.es`       | ✅ Fix Assets & Layout        |
| Limpieza de Ramas                | ✅ Integradas y borradas      |
| Stack prod `portal.dxpro.es`     | ✅ Verificado (index.html)    |
| Deploy automático GitHub Actions | ✅ Configurado                |
| Laravel 11                       | ✅ Instalado y configurado    |
| Base de datos                    | ✅ Migraciones base (Fase 2)  |

---

## Qué se hizo en esta sesión

- **Fase 2 Finalizada**: Stack Laravel 11 operativo y layouts Blade base creados.
- **Refactor de Layout**: Migración completa de Tailwind a CSS Semántico oficial.
- **Fix Assets Beta**: Resolución de carga de CSS mediante servido nativo desde `public/`.
- **Cleanup**: Eliminadas ramas locales y remotas ya integradas (`feature/*`, `fix/*`).
- **Sincronización**: Actualizados documentos de gestión y lecciones aprendidas.

---

## Tarea Inmediata — Empezar Aquí

**Fase 3 — Autenticación y JWT**

1. Implementar `AuthController` con login básico.
2. Configurar `JwtService` para emisión y validación de tokens.
3. Aplicar middleware de RBAC (`admin`, `technician`, `viewer`).
4. **Verificación**: Asegurar que el login use el layout refactorizado.

---

## Contexto Técnico Importante

- **Ruta local**: `z:\DX-License-Manager`.
- **Docker**: Uso de `--project-directory .` obligatorio.
- **Assets**: Mapeados en `infra/nginx/beta.conf` hacia `backend/public/assets`.
- **HTTPS**: Forzado vía middleware/config en Laravel para evitar mixed content.

---

## Pendiente Sin Resolver

- Confirmación visual de `portal.dxpro.es` con el nuevo stack Laravel (requiere merge de `dev` a `main`).
- Iniciar flujo de login JWT.

---

## Estado de Archivos Clave

| Archivo                          | Estado                                    |
| :------------------------------- | :---------------------------------------- |
| `infra/.env.beta`                | ✅ Configurado (DB, Redis, App URL)       |
| `infra/.env.prod`                | ✅ Configurado                            |
| `.agent/secrets/identities.json` | ✅ Válido                                 |
| `backend/`                       | ✅ Estructura completa Laravel 11         |

---

## Comandos Útiles para la Próxima Sesión

```bash
# Limpiar caché de Laravel dentro del contenedor
docker exec dx-php-beta php artisan view:clear
docker exec dx-php-beta php artisan cache:clear
docker exec dx-php-beta php artisan config:clear
```

