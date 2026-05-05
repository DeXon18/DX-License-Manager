# PROJECT MAP — DX License Manager

Flujos de datos planificados del sistema.
**Estado actual: ningún flujo implementado — Fase 0 pendiente.**

Cuando un agente va a implementar algo, debe leer este archivo para entender dependencias y impacto colateral.

---

## Flujo 1 — Importación CSV (Semanal)
> 📋 Planificado — Fase 2

```
Archivo CSV (viernes)
  → Admin sube via panel /admin/import
  → ImportController → StoreLicenseFileRequest (validación)
  → ContractImportService
      → Normalizar client_name a Title Case
      → Upsert por contract_number:
          - Existe → actualizar: cost_center, type_product, end_date, status, comment
          - No existe → crear Client (si nuevo) + crear Contract
          - Desaparece → marcar status = 'Baja' (nunca borrar)
      → Registrar en import_logs (quién, cuándo, resultado)
  → BD: clients + contracts actualizados
  → Vista /clients muestra badges de caducidad actualizados
```

**Impacto colateral:** Si cambias la lógica de upsert, afecta los badges de caducidad en la vista de clientes y la vista de caducidades globales.

---

## Flujo 2 — Vista de Caducidades
> 📋 Planificado — Fase 3

```
BD: contracts.end_date
  → ClientController@expirations
      → Filtra por umbral:
          - Caducado: fecha pasada → rojo
          - Crítico: 0-7 días → rojo anaranjado
          - Próximo: 8-30 días → amarillo
          - Vigente: +30 días → verde
  → Vista expirations.index
  → Dashboard inicio: widget "Próximas caducidades"
```

**Impacto colateral:** Si cambias los umbrales, cambian los colores en tres sitios: vista de caducidades, listado de clientes y dashboard de inicio.

---

## Flujo 3 — Autenticación y Control de Acceso
> 📋 Planificado — Fase 1

```
Request HTTP
  → Middleware JwtAuth
      → Verifica Bearer token en header Authorization
      → Token válido → inyecta usuario en Request
      → Token expirado → intenta refresh automático (24h)
      → Sin token → 401
  → Middleware CheckPermission
      → Verifica role_id del usuario contra roles permitidos en la ruta
      → Sin permiso → 403
  → Controller recibe Request con usuario autenticado y rol verificado
```

**Impacto colateral:** Cualquier ruta nueva sin estos dos middlewares queda desprotegida. Verificar siempre con `php artisan route:list`.

---

## Flujo 4 — Feature Flags (Herramientas)
> 📋 Planificado — Fase 1

```
Admin activa/desactiva flag en /admin/feature-flags
  → FeatureFlagController@toggle → BD: feature_flags.enabled
  → Invalida caché Redis (1 hora) → Feature::enabled('key') fresco
  → Hub de herramientas /tools:
      - Flag activo + rol permitido → card clickable
      - Flag inactivo → card con badge "Próximamente" + botón deshabilitado
      - Sin rol → card no visible
```

**Impacto colateral:** Si añades una herramienta nueva, necesita: entrada en `feature_flags` BD + seeder + entrada en `identities.json`. Sin los tres, no aparece en el hub.

---

## Flujo 5 — Auditoría IA de Archivos .lic
> 📋 Planificado — Fase 6

```
Usuario sube .lic en la herramienta (ej: NX Converter)
  → PHP parsea localmente:
      - Líneas # del header (Sold-To, Customer Name, WebKey)
      - Línea SERVER (hostname, host_id, puerto)
      - Línea VENDOR (vendor daemon)
      - INCREMENTs resumidos (código + cantidad + fecha + HOSTID)
      - php_detected_host_ids
  → PHP envía SOLO el extracto limpio a n8n webhook
  → n8n → FallbackChain:
      Gemini (30s timeout)
        → si falla → DeepSeek
          → si falla → OpenRouter
            → si falla → notificación Telegram + reintento Redis
  → n8n Parse & Merge → limpia JSON resultado
  → Portal Callback POST /api/audit/callback
  → Laravel guarda en ai_audit_results:
      - file_path = NULL siempre (política Solo Log)
      - sold_to, productos, proveedor_usado, timestamp
  → UI muestra resultado al usuario
```

**Impacto colateral:** Si el archivo `.lic` se envía completo a n8n (no el extracto), consume tokens masivos y puede timeout. El parser PHP local es obligatorio.

---

## Flujo 6 — Deploy Automático
> 📋 Planificado — Fase 0

```
git push origin feature/X
  → PR a dev en GitHub
  → GitHub Actions ci.yml:
      - Valida docker-compose files
      - PHP syntax check (cuando exista backend/)
  → Si CI verde → merge a dev
  → GitHub Actions deploy-beta.yml:
      - SSH a LXC 600 (puerto 2222, clave id_rsa_deploy)
      - git pull origin dev
      - git submodule update --init --recursive
      - docker compose up -d --build
  → beta.dxpro.es actualizado
  → Validación manual en beta
  → PR dev → main → deploy-prod.yml → portal.dxpro.es
```

**Impacto colateral:** Si el deploy falla, revisar primero los logs de GitHub Actions antes de tocar nada en el servidor.

---

## Flujo 7 — Cloudflare → nginx
> 📋 Planificado — Fase 0

```
Usuario accede a beta.dxpro.es
  → Cloudflare DNS → túnel dxportal (LXC 600)
  → cloudflared en LXC 600 → localhost:8002
  → nginx-beta → (php-fpm-beta → Laravel, desde Fase 1)
```

**⚠️ CRÍTICO:** El túnel `dxportal` corre en LXC 600, NO en LXC 101.
Nunca mover las rutas de beta/portal al túnel del LXC 101 — no puede alcanzar el LXC 600.

---

## Mapa de Dependencias entre Modelos
> 📋 Planificado — Fases 1-3

```
Vendor (1)
  └── Contract (N) ←─── client_id ──→ Client (1)
                                            └── LicenseFile (N)
                                                    └── LicenseProduct (N)

Role (1)
  └── User (N)
        └── ImportLog (N)

FeatureFlag (standalone)
  └── Helper Feature::enabled('key') ← caché Redis 1h
```

---

## Archivos Críticos — Tocar con Cuidado
> (cuando existan)

| Archivo                                   | Por qué es crítico                                    |
| :---------------------------------------- | :---------------------------------------------------- |
