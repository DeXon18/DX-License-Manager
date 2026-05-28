---
trigger: always_on
---

# Catálogo de Errores — DX License Manager

Guía de diagnóstico rápido. Formato: Si ves X → la causa suele ser Y → fix Z.
El agente consulta este archivo antes de investigar un error desde cero.

---

## Errores de Autenticación

### HTTP 401 — "Las credenciales no coinciden con nuestros registros"

**Causa:** El archivo `backend/.env` se ha desincronizado o corrompido, apuntando a una base de datos SQLite vacía en lugar de MariaDB. Laravel no encuentra al usuario porque está buscando en el archivo equivocado.

**Fix:**
1. Sincronizar el `.env` con la versión de infraestructura (ver sección Infraestructura).
2. Si el usuario sigue sin existir, ejecutar el seeder de recuperación:
```bash
docker exec dx-php-beta php artisan db:seed --class=AdminUserSeeder
```

---

## Errores de Infraestructura

### 502 Bad Gateway en beta.dxpro.es o portal.dxpro.es

**Causa más probable:** Cloudflared no puede alcanzar nginx, o nginx no puede alcanzar php-fpm.

**Diagnóstico:**

```bash
# Verificar contenedores
docker compose --project-directory . -f infra/docker-compose.beta.yml ps

# Verificar cloudflared en LXC 600
systemctl status cloudflared

# Verificar nginx responde internamente
curl -I http://localhost:8002
```

**Fixes por orden de probabilidad:**

1. Contenedor caído → `docker compose up -d`
2. Cloudflared caído → `systemctl restart cloudflared`
3. Permisos de storage → `chmod -R 777 /rpool/webs/DX-License-Manager/backend/storage`
4. Reglas iptables perdidas → reiniciar Docker

**⚠️ NUNCA:** Mover las rutas de beta/portal al túnel del LXC 101 — no puede alcanzar el LXC 600.

---

### EPERM / Permission Denied en backend/ desde Samba

**Causa:** Los archivos fueron creados por php-fpm (`www-data`) y Samba no puede sobreescribirlos.

**Fix — desde el host Proxmox:**

```bash
chown -R nobody:nogroup /rpool/webs/DX-License-Manager
chmod -R 775 /rpool/webs/DX-License-Manager
```

**Fix alternativo — para storage de Laravel:**

```bash
chmod -R 777 /rpool/webs/DX-License-Manager/backend/storage
chmod -R 777 /rpool/webs/DX-License-Manager/backend/bootstrap/cache
```

**⚠️ NUNCA:** Intentar `chown` desde dentro del contenedor — falla en LXC unprivileged.

---

### Git — "dubious ownership" en el servidor

**Causa:** Los archivos pertenecen al usuario de Samba (Windows), no a root del servidor.

**Fix:**

```bash
git config --global --add safe.directory /opt/web-projects/DX-License-Manager
```

---

### Git — archivos de skills aparecen como "modified" siempre

**Causa:** `impeccable` y `ui-ux-pro-max` son submodules con cambios internos no commiteados.

**Fix permanente (ya aplicado):**

```bash
git config submodule.".agent/skills/impeccable".ignore dirty
git config submodule.".agent/skills/ui-ux-pro-max".ignore dirty
```

Si no está aplicado, ejecutar estos dos comandos.

---

### Docker — "could not resolve host: github.com"

**Causa:** El LXC no tiene DNS configurado.

**Fix:**

```bash
echo "nameserver 8.8.8.8" >> /etc/resolv.conf
echo "nameserver 1.1.1.1" >> /etc/resolv.conf
```

---

## Errores de Laravel

### HTTP 500 — "Failed to open stream: Permission denied" en storage/

**Causa:** php-fpm no tiene permisos de escritura en storage o bootstrap/cache.

**Fix:**

```bash
# Desde Proxmox host
chmod -R 777 /rpool/webs/DX-License-Manager/backend/storage
chmod -R 777 /rpool/webs/DX-License-Manager/backend/bootstrap/cache

# Dentro del contenedor — limpiar caché
docker exec dx-php-beta php artisan view:clear
docker exec dx-php-beta php artisan cache:clear
```

---

### HTTP 500 — "Class not found" tras deploy

**Causa:** Autoload de Composer no se actualizó tras añadir una clase nueva.

**Fix:**

```bash
docker exec dx-php-beta sh -c "cd /var/www/html && composer dump-autoload"
```

---

### HTTP 401 — `{"error":"Token not provided"}`

**Causa:** Acceso a una ruta protegida sin token JWT. Es el comportamiento correcto del middleware.

**No es un error** — significa que el middleware JWT está funcionando. Para acceder, hacer login primero en `/login`.

---

### HTTP 419 — CSRF token mismatch

**Causa:** Formulario sin `@csrf` o sesión expirada.

**Fix:** Añadir `@csrf` en el formulario Blade o recargar la página para regenerar el token.

---

### `php artisan migrate` — "SQLSTATE: Base table already exists"

**Causa:** La migración se ejecutó parcialmente y dejó la tabla a medias.

**Fix:**

```bash
# Ver estado real
docker exec dx-php-beta php artisan migrate:status

# Si es entorno beta sin datos importantes
docker exec dx-php-beta php artisan migrate:fresh --seed

# Si hay datos que conservar — borrar solo la tabla problemática y reintentar
```

---

### `php artisan migrate` — "Access denied for user"

**Causa:** Las credenciales en `backend/.env` no coinciden con las del contenedor MariaDB.

**Fix:** Verificar que `DB_HOST=mariadb-beta`, `DB_USERNAME`, `DB_PASSWORD` en `backend/.env` coinciden con `MYSQL_USER` y `MYSQL_PASSWORD` en `infra/.env.beta`.

---

### Restauración de Backup — "ERROR 2026 (HY000): TLS/SSL error: SSL is required"

**Causa:** El cliente de MariaDB/MySQL exige una conexión cifrada por defecto, pero los contenedores internos en Docker no usan SSL interno.

**Fix:** Al ejecutar cualquier comando de `mariadb` o al usar el botón de Restaurar, es imprescindible inyectar el parámetro `--skip-ssl`:
```bash
mariadb --skip-ssl -h mariadb-beta -u dxportal -p... dxportal_beta < backup.sql
```

---

## Errores de GitHub Actions

### Deploy falla — "Connection refused" al hacer SSH

**Causa:** Intento de conexión usando el puerto `2222`.

**Fix:** Usar siempre el puerto `22`. El puerto `2222` está configurado en algunos archivos antiguos o plantillas pero no tiene acceso real desde este entorno.

---

### Deploy falla — "Connection refused" al hacer SSH (GitHub Actions)

**Causa:** El secret `SSH_HOST` tiene la IP local (192.168.x.x) en lugar de la IP pública del router.

**Fix:** Actualizar el secret `SSH_HOST` en GitHub con la IP pública. Verificar en [whatismyip.com](https://www.whatismyip.com).

---

### Deploy falla — "refusing to allow a PAT to create workflow"

**Causa:** El Personal Access Token no tiene scope `workflow`.

**Fix:** Ir a GitHub → Settings → Developer settings → Tokens → editar el token → marcar `workflow`.

---

### Deploy falla — "src refspec feature/X does not match any"

**Causa:** La rama no existe en el remoto — no se hizo push antes de intentar el PR.

**Fix:**

```bash
git push origin feature/nombre-rama
```

---

## Errores de n8n / Auditoría IA

### TLS handshake timeout al hacer pull de imagen Docker

**Causa:** DNS del LXC no resuelve dominios externos.

**Fix:**

```bash
# Añadir DNS en el contenedor
echo '{"dns": ["8.8.8.8", "1.1.1.1"]}' > /etc/docker/daemon.json
systemctl restart docker
```

---

### n8n — `parse_error: true` en el resultado de DeepSeek

**Causa:** DeepSeek devolvió el JSON envuelto en bloques markdown ` ```json ``` `.

**Fix:** Ya está manejado en el nodo "Parse & Merge" del workflow n8n con `.replace(/```json\s*/gi, '')`. Si persiste, revisar el prompt del LLM Chain.

---

## Referencia Rápida de Comandos

```bash
# Estado de stacks
docker compose --project-directory . -f infra/docker-compose.beta.yml ps
docker compose --project-directory . -f infra/docker-compose.prod.yml ps

# Logs en tiempo real
docker compose --project-directory . -f infra/docker-compose.beta.yml logs -f

# Entrar al contenedor PHP
docker exec -it dx-php-beta sh

# Artisan dentro del contenedor
php artisan migrate:status
php artisan route:list
php artisan config:clear && php artisan cache:clear && php artisan view:clear

# Estado cloudflared
systemctl status cloudflared
systemctl restart cloudflared

# Fix permisos Samba (desde Proxmox host)
chown -R nobody:nogroup /rpool/webs/DX-License-Manager
chmod -R 775 /rpool/webs/DX-License-Manager
```
## Infraestructura Crítica — No Tocar
## ⚠️ Cloudflare — NO TOCAR
El túnel `dxportal` en LXC 600 es infraestructura crítica independiente del proyecto.
- NO ejecutar: systemctl stop/disable cloudflared
- NO borrar: /etc/cloudflared
- El túnel gestiona: beta.dxpro.es y portal.dxpro.es
