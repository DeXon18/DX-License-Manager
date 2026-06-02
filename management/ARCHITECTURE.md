# Arquitectura Física y Lógica: Aislamiento Prod vs Dev

Este documento documenta la separación absoluta de los entornos de Producción y Desarrollo del DX License Manager, garantizando que los cambios en desarrollo nunca afecten al entorno en vivo.

## 1. Aislamiento Físico y de Código (Git)

| Componente | Entorno de Producción | Entorno de Desarrollo (Beta) |
| :--- | :--- | :--- |
| **Ruta en Servidor** | `/opt/web-projects/DX-License-Manager` | `/opt/web-projects/DX-License-Manager-DEV` |
| **Rama Git Activa** | `main` | `dev` (o `feature/*`) |
| **Punto de Montaje** | Monta el código de la rama `main` en crudo. | Monta el código de la rama `dev` en crudo. |

**Riesgo Eliminado:** Editar código en caliente en el IDE solo afecta a Desarrollo. Producción permanece intocable en su propia carpeta física.

---

## 2. Aislamiento de Docker y Puertos

| Servicio | Producción (`docker-compose.prod.yml`) | Desarrollo (`docker-compose.beta.yml`) |
| :--- | :--- | :--- |
| **Nginx (Puerto)** | `8001` | `8002` |
| **Contenedores** | `dx-php-prod`, `dx-mariadb-prod`... | `dx-php-beta`, `dx-mariadb-beta`... |
| **Red** | Red interna Docker de Prod. | Red interna Docker de Dev. |

**Riesgo Eliminado:** Si se satura el servidor web de Desarrollo o un proceso PHP entra en bucle, no afectará a los recursos asignados del contenedor de Producción.

---

## 3. Aislamiento de Datos (Bases de Datos y Storage)

Este es el punto más crítico para la seguridad y la integridad de los datos de los clientes.

| Componente | Producción | Desarrollo (Beta) |
| :--- | :--- | :--- |
| **Volumen MariaDB** | `dx-license-manager_mariadb_prod_data` | `dx-license-manager-dev_mariadb_beta_data` |
| **Volumen Redis** | `dx-license-manager_redis_prod_data` | `dx-license-manager-dev_redis_beta_data` |
| **Archivos (Storage)** | `./backend/storage_prod` (En la carpeta original) | `./backend/storage_beta` (En la carpeta `-DEV`) |
| **Credenciales** | Carga `infra/.env.prod` | Carga `infra/.env.beta` |

**Riesgos Eliminados:**
1. Al usar carpetas distintas, Docker Compose crea **volúmenes de base de datos independientes**. Un comando `migrate:fresh` en Desarrollo jamás borrará datos de Producción.
2. Los archivos físicos subidos (`storage`) se guardan en carpetas aisladas, sin montajes cruzados.

---

## 4. Aislamiento de Despliegues (GitHub Actions)

| Workflow | Rama | Destino SSH | Acción |
| :--- | :--- | :--- | :--- |
| `deploy-prod.yml` | Push a `main` | `/opt/web-projects/DX-License-Manager` | Pull de `main` y up de `prod.yml` |
| `deploy-beta.yml` | Push a `dev` | `/opt/web-projects/DX-License-Manager-DEV` | Pull de `dev` y up de `beta.yml` |

**Riesgo Eliminado:** El pipeline respeta la estructura física. Un push a `dev` jamás sobreescribirá la carpeta de Producción ni cruzará ramas. Los pases a Producción requieren un merge explícito a `main`.

---

## Resumen

El sistema opera como **dos servidores virtuales completamente independientes** corriendo bajo el mismo host Docker físico, compartiendo exclusivamente el historial de Git. Ninguna operación destructiva en `-DEV` puede alcanzar la carpeta de Producción.
