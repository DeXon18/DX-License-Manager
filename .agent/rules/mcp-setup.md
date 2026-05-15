# MCP Setup Guide — DX Management Portal

> Ruta: `.agent/rules/mcp-setup.md`
> Esta guía documenta qué MCP hay, cómo instalarlo y qué pasos manuales requiere cada uno.

---

## Requisitos Previos

Tener instalado en el PC:

- Node.js 18+ → [nodejs.org](https://nodejs.org)
- Python + uv → `pip install uv` (para `mcp-server-git`)
- Una clave SSH en `C:\Users\[usuario]\.ssh\id_rsa` (la misma que usa GitHub Actions)

Verificar:

```bash
node --version    # debe ser 18+
npx --version
uvx --version
```

---

## 1. Filesystem MCP — Acceso al Proyecto

**Paquete:** `@modelcontextprotocol/server-filesystem` (oficial Anthropic)
**Para qué sirve:** Gemini puede leer y escribir cualquier archivo del proyecto directamente — PHP, Blade, configs, migrations, tests.

**Configuración:** Ya está en `.mcp.json` apuntando a `Z:\DX-License-Manager`.

**Instalación:** Ninguna. `npx` lo descarga automáticamente al primer uso.

**Verificar que funciona:** Pedir a Gemini que liste los archivos de `backend/app/`.

---

## 2. Memory MCP — Memoria Persistente

**Paquete:** `@modelcontextprotocol/server-memory` (oficial Anthropic)
**Para qué sirve:** Knowledge graph persistente entre sesiones. Gemini puede guardar entidades (clases, decisiones, patrones), relaciones entre ellas, y observaciones. Complementa el `HANDOFF.md` con memoria estructurada.

**Configuración:** Ya está en `.mcp.json`.

**Instalación:** Ninguna.

**Cómo usarlo:** Decirle a Gemini `"guarda en memoria que AuditService.php usa FallbackChain"` y lo recuperará en sesiones futuras sin leer el HANDOFF.

---

## 3. GitHub MCP — Gestión del Repo

**Paquete:** `@modelcontextprotocol/server-github` (oficial Anthropic)
**Para qué sirve:** Crear PRs, revisar issues, leer commits, crear/editar archivos en el repo, gestionar ramas — todo sin salir de Antigravity.

### Setup

1. Ir a GitHub → Settings → Developer settings → Personal access tokens → Fine-grained tokens
2. Crear token con permisos sobre `DeXon18/DX-License-Manager`:
   - `Contents` — Read and write
   - `Pull requests` — Read and write
   - `Issues` — Read and write
   - `Metadata` — Read only
3. Copiar el token
4. En `.mcp.json`, reemplazar `[GITHUB_PAT_TOKEN]` con el token generado

**Verificar:** Pedir a Gemini que liste las ramas del repo.

---

## 4. Git MCP — Operaciones Git Locales

**Paquete:** `mcp-server-git` (oficial Anthropic, via uvx/Python)
**Para qué sirve:** Leer historial de commits, buscar en el log, ver diffs, manipular el repo local directamente.

### Setup

```bash
pip install uv
```

**Configuración:** Ya está en `.mcp.json` apuntando a `Z:\DX-License-Manager`.

**Diferencia con GitHub MCP:** Git MCP opera en el repo local (leer `git log`, `git diff`). GitHub MCP opera en la API remota de GitHub (PRs, issues, branches remotas).

---

## 5. SSH MCP — Acceso al Servidor

**Paquete:** `ssh-mcp` (tufantunc)
**Para qué sirve:** Ejecutar comandos directamente en el LXC 600 — `docker compose ps`, `php artisan migrate`, `tail -f logs`, reiniciar stacks, etc. Gemini puede operar el servidor sin que tengas que hacer SSH manual.

### Setup — Clave SSH

La clave privada debe ser la misma que usa GitHub Actions (`SSH_PRIVATE_KEY`).

Si no tienes clave SSH en el PC:

```bash
ssh-keygen -t ed25519 -C "dxportal-mcp" -f C:\Users\[TU_USUARIO]\.ssh\id_rsa
```

Añadir la clave pública al servidor:

```bash
# En LXC 600
echo "[contenido de id_rsa.pub]" >> /root/.ssh/authorized_keys
chmod 600 /root/.ssh/authorized_keys
```

### Configuración en `.mcp.json`

Reemplazar:

- `[IP_PUBLICA_ROUTER]` — tu IP pública (la misma que usas para conectar por SSH)
- `[TU_USUARIO]` — tu usuario de Windows (ej: `DeXon`)

**Puerto:** 2222 ya está configurado (router → LXC 600:22).

**Verificar:**

```bash
# Probar conexión manual primero
ssh -p 2222 -i C:\Users\[TU_USUARIO]\.ssh\id_rsa root@[IP_PUBLICA]
```

**Comandos que Gemini podrá ejecutar via SSH:**

```bash
# Estado de los stacks
docker compose --project-directory . -f infra/docker-compose.beta.yml ps

# Artisan dentro del contenedor PHP
docker exec dx-php-beta php artisan migrate:status

# Logs en tiempo real
docker compose --project-directory . -f infra/docker-compose.beta.yml logs --tail=50
```

---

## 6. MariaDB MCP — Acceso a la Base de Datos

**Paquete:** `@benborla29/mcp-server-mysql`
**Para qué sirve:** Gemini puede hacer consultas SQL directamente — inspeccionar schemas, verificar migraciones, debuggear datos, buscar registros.

### Problema: MariaDB está dentro de Docker

El puerto 3306 de MariaDB no está expuesto al exterior actualmente. Hay dos opciones:

**Opción A — Exponer el puerto en docker-compose (recomendada para desarrollo)**

En `infra/docker-compose.beta.yml`, añadir bajo el servicio `mariadb-beta`:

```yaml
ports:
  - "127.0.0.1:3307:3306" # solo local en el servidor, no al exterior
```

Y en el router, abrir un puerto externo (ej: 3307) → `192.168.50.60:3307`.

**Opción B — Usar SSH MCP para hacer queries via docker exec**

Sin abrir puertos, usar el SSH MCP para ejecutar:

```bash
docker exec dx-mariadb-beta mariadb -u dxportal -p[PASSWORD] dxportal_beta -e "SELECT * FROM users LIMIT 10;"
```

**Recomendación:** Usar Opción B mientras estamos en desarrollo — más seguro, sin puertos extra abiertos. Configurar Opción A en la Fase 6 (MCP completo).

### Configuración `.mcp.json` (cuando uses Opción A)

Reemplazar en `.mcp.json`:

- `[IP_PUBLICA_ROUTER]` — tu IP pública
- `[PUERTO_MARIADB_BETA_EXPUESTO]` — el puerto externo del router (ej: `3307`)
- `[DB_PASSWORD_BETA]` — valor de `DB_PASSWORD` en `infra/.env.beta`

**Seguridad:** Prod está siempre en `read-only` (`ALLOW_INSERT/UPDATE/DELETE = false`). Beta también por defecto — activar escritura solo cuando sea explícitamente necesario y con confirmación del desarrollador.

---

## 7. n8n MCP — Automatización

**Paquete:** `n8n-mcp` (czlonkowski) — 16.6k ⭐, con soporte explícito para Antigravity
**Para qué sirve:** Documentación de 1,396 nodos n8n, búsqueda de templates (2,709 disponibles), validación y gestión completa de workflows. Sin API key solo funciona en modo documentación — con API key puede crear, editar y ejecutar workflows directamente.

**Referencia:** [github.com/czlonkowski/n8n-mcp](https://github.com/czlonkowski/n8n-mcp) — guía Antigravity en `docs/ANTIGRAVITY_SETUP.md`

### Setup

**⚠️ `MCP_MODE=stdio` es OBLIGATORIO.** Sin esta variable Antigravity recibe errores de JSON parsing.

### Obtener la N8N_API_KEY

1. Abrir tu instancia de n8n
2. Settings → API → Create API Key
3. Copiar el token generado

### Configurar `.mcp.json`

Reemplazar en la entrada `n8n`:

- `[URL_DE_TU_N8N]` — ej: `http://192.168.50.60:5678` o el dominio público
- `[N8N_API_KEY]` — el token generado en el paso anterior

### Modo solo documentación (sin n8n corriendo)

Si no tienes n8n todavía, funciona igualmente para consultar nodos y templates — simplemente deja las variables `N8N_API_URL` y `N8N_API_KEY` vacías o elimínalas del `.mcp.json`.

### Telemetría desactivada

`N8N_MCP_TELEMETRY_DISABLED=true` ya está en la config — no envía datos de uso.

### Lo que Gemini podrá hacer

```
# Buscar nodos de n8n
search_nodes({query: "telegram", includeExamples: true})

# Validar un workflow antes de desplegarlo
validate_workflow(workflowJson)

# Crear un workflow directamente en n8n
n8n_create_workflow(workflow)

# Buscar templates
search_templates({searchMode: "by_task", task: "webhook_processing"})
```

---

## Estado de Implementación

| MCP          | Estado           | Acción requerida                                    |
| :----------- | :--------------- | :-------------------------------------------------- |
| Filesystem   | ✅ Listo         | Ninguna                                             |
| Memory       | ✅ Listo         | Ninguna                                             |
| GitHub       | ⚠️ Falta token   | Crear PAT en GitHub → Settings → Developer settings |
| Git          | ⚠️ Falta uv      | `pip install uv`                                    |
| SSH          | ⚠️ Falta config  | Rellenar IP + ruta de clave SSH en `.mcp.json`      |
| n8n          | ⚠️ Falta API key | Obtener en n8n → Settings → API                     |
| MariaDB beta | ⏸️ Pendiente     | Fase 6 — por ahora usar SSH + docker exec           |
| MariaDB prod | ⏸️ Pendiente     | Fase 6                                              |

---

## Orden de Instalación Recomendado

1. `pip install uv` — desbloquea Git MCP
2. Crear GitHub PAT → actualizar `.mcp.json`
3. Verificar/crear clave SSH → actualizar `.mcp.json` con IP y ruta
4. Obtener n8n API key → actualizar `.mcp.json` con URL y key
5. Abrir Antigravity con el proyecto → los MCPs de `npx` se descargan solos al primer uso
6. Probar: pedir a Gemini que liste los archivos (Filesystem) y busque nodos n8n (n8n MCP)
7. MariaDB → Fase 6

---

## Setup en PC Adicional

El `.mcp.json` no se sincroniza por Git (está en `.gitignore`), así que cada PC tiene su propio archivo. Lo único que cambia entre PCs es el usuario de Windows y la ruta de la clave SSH.

**PCs configurados:**

| PC           | Usuario Windows | Clave SSH                                     |
| :----------- | :-------------- | :-------------------------------------------- |
| PC principal | Oskar           | `C:\Users\Oskar\.ssh\id_rsa` ✅               |
| PC DeXon     | DeXon           | `C:\Users\DeXon\.ssh\id_rsa` ⚠️ pendiente     |
| PC Oblazquez | Oblazquez       | `C:\Users\Oblazquez\.ssh\id_rsa` ⚠️ pendiente |

### Pasos para cada PC nuevo

**1 — Crear carpeta y generar clave SSH**

```powershell
mkdir C:\Users\[USUARIO]\.ssh
ssh-keygen -t ed25519 -C "dxportal-[NOMBRE-PC]" -f C:\Users\[USUARIO]\.ssh\id_rsa
```

Pulsar Enter dos veces cuando pregunte passphrase (sin contraseña).

**2 — Mostrar la clave pública**

```powershell
Get-Content C:\Users\[USUARIO]\.ssh\id_rsa.pub
```

Copiar el resultado (empieza por `ssh-ed25519 AAAA...`).

**3 — Añadir al servidor via consola Proxmox**

Proxmox web UI → LXC 600 → Console:

```bash
echo "PEGA_AQUI_LA_CLAVE_PUBLICA" >> /root/.ssh/authorized_keys
chmod 600 /root/.ssh/authorized_keys
```

**4 — Probar conexión**

```powershell
ssh -p 2222 -i C:\Users\[USUARIO]\.ssh\id_rsa root@81.0.53.128
```

Debe entrar sin pedir contraseña.

**5 — Crear el `.mcp.json` local**

Copiar el `.mcp.json` base y cambiar solo esta línea:

```jsonc
"--key=C:\\Users\\[USUARIO]\\.ssh\\id_rsa",
```

El resto del archivo es idéntico en todos los PCs.

**6 — Instalar dependencias**

```powershell
pip install uv
node --version   # verificar que es 18+
```

---

## Seguridad

- `.mcp.json` está en `.gitignore` — nunca se sube al repo con datos reales
- Cada PC tiene su propia clave SSH — si pierdes un PC, borra su línea de `authorized_keys` sin afectar a los demás
- El usuario `dxportal` en MariaDB tiene permisos mínimos — no puede borrar schemas
- El MariaDB MCP prod siempre es read-only
- El SSH MCP conecta con clave privada, no con contraseña
- `PasswordAuthentication no` en el servidor — solo se puede entrar con clave
- Rotar el GitHub PAT cada 90 días
