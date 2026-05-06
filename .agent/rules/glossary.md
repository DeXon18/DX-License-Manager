---
trigger: always_on
---

# Glosario de Dominio — DX License Manager

Términos específicos del negocio de licencias Siemens PLM y Moldex3D.
El agente debe leer este archivo antes de trabajar con el modelo de datos de licencias.

---

## Siemens PLM

**Sold-To**
Identificador numérico único del cliente en el sistema de Siemens. Es el ID principal de una licencia. Un cliente puede tener uno o varios Sold-To. Ejemplo: `10303508`. No es un código de cifrado — es un ID de cliente en la BD de Siemens.

**Install**
Código que identifica el punto de instalación de la licencia. Va siempre junto al Sold-To. Formato: número similar al Sold-To.

**Contraheader / Contract Number**
Número de contrato en el sistema interno. Formato: `CONHxxxxxxx`. Es el identificador único en el CSV semanal y en la BD del portal. Ejemplo: `CONH1006420`. Nunca cambia — es la clave de upsert.

**COMPOSITE**
Identificador de hardware del servidor de licencias en sistemas modernos Siemens. Formato: `COMPOSITE=xxxxxxxxxxxx`. Se registra en el portal WebKey de Siemens para activar la licencia. Puede estar pendiente de registro (placeholder) en licencias temporales.

**HOST ID / ETHER**
Dirección MAC de la tarjeta de red. Se usa en licencias node-locked (bloqueadas a una máquina concreta). Formato: `d8bbc1a8e357`. Aparece en la línea `INCREMENT` del archivo `.lic`.

**Node-Locked**
Licencia bloqueada a una máquina específica por su MAC address. Un archivo `.lic` puede tener varias líneas INCREMENT node-locked con MACs distintas — cada una es un equipo diferente. Nunca agrupar INCREMENT con MACs distintas.

**Floating**
Licencia flotante — no está bloqueada a una máquina, puede usarla cualquier equipo de la red que se conecte al servidor de licencias.

**Vendor Daemon**
Proceso servidor que gestiona las licencias de un vendor específico. Cada daemon gestiona sus propios productos:

| Daemon   | Productos                                          |
| :------- | :------------------------------------------------- |
| `ugslmd` | NX, Designcenter, Teamcenter, Simcenter 3D, Amesim |
| `cdlmd`  | STAR-CCM+                                          |
| `RCTECH` | HEEDS                                              |
| `saltd`  | Simcenter otros — formato moderno Siemens          |

**INCREMENT**
Línea en un archivo `.lic` que define una licencia de un producto concreto. Contiene: nombre del producto, vendor daemon, versión, fecha de expiración, cantidad de asientos y opcionalmente HOSTID (node-locked). Un archivo `.lic` puede tener decenas o cientos de líneas INCREMENT.

**SERVER (línea)**
Primera línea de un archivo `.lic`. Define el servidor: hostname, host_id (COMPOSITE o MAC) y puerto. Ejemplo: `SERVER SRV-LIC-01 COMPOSITE=abc123 28000`

**VENDOR (línea)**
Segunda línea de un archivo `.lic`. Define el daemon que gestiona las licencias. Ejemplo: `VENDOR ugslmd`

**Licencia Temporal 7 días**
Licencia de prueba generada automáticamente. `SERVER YourHostname ANY` — sin COMPOSITE registrado. Solo válida 7 días desde la generación.

**Licencia Temporal 30 días**
Licencia en proceso de registro. COMPOSITE en trámite. Válida 30 días mientras se completa el registro.

**Licencia Contractual**
Licencia con fecha fija en las líneas INCREMENT. Resultado de un contrato firmado. La fecha de expiración es la clave de seguimiento en el portal.

**Licencia Permanente**
`permanent` en lugar de fecha en la línea INCREMENT. Sin caducidad.

**transformed_header**
Campo generado por el parser cuando se moderniza una licencia de `ugslmd` a `saltd`. Contiene el nuevo bloque SERVER + VENDOR con el daemon actualizado. Si la licencia ya usa `saltd`, este campo es `null`.

**Cost Center**
Centro de coste interno del cliente. Formato: `710-PDM`, `210-CAM`. Puede cambiar entre actualizaciones del CSV.

**Sales Quote ID / License Quote**
Identificadores de la oferta y la licencia en el sistema de Siemens. Aparecen en el header del archivo `.lic`.

---

## Moldex3D

**Machine ID**
Identificador único del equipo para licencias Moldex3D. Equivalente al COMPOSITE/MAC de Siemens pero con formato propio. Se extrae del archivo `.mac`.

**Archivo .mac**
Archivo de licencia de Moldex3D. Equivalente al `.lic` de Siemens pero con formato diferente. El nombre del archivo contiene metadatos: `AÑO_ID_[PAIS]CLIENTE__TIPO_FECHA`. Ejemplo: `2025_1005998_[ESP]FUNDACION ANDALTEC__Floating_20270114`

---

## Modelo de Datos — Relaciones Clave

```
CSV semanal
  └── contract_number (CONH...)     ← clave de upsert, nunca cambia
        └── client_name             ← normalizado a Title Case
        └── vendor                  ← Siemens / Moldex3D
        └── end_date                ← fecha de caducidad del contrato

Archivos .lic (subidos manualmente)
  └── Sold-To                       ← ID cliente Siemens
        └── vendor_daemon           ← ugslmd / cdlmd / RCTECH / saltd
        └── productos (INCREMENT)   ← product_code + cantidad + fecha + HOSTID
```

**Importante:** Los Contraheaders (CSV) y las Licencias (.lic) son independientes. Un cliente puede tener ambos o solo uno de los dos. No están relacionados directamente en la BD — se vinculan por el nombre del cliente.

---

## Política Solo Log — Regla Crítica

Los archivos `.lic` auditados por IA **nunca se guardan físicamente** en el servidor. El campo `file_path` en `ai_audit_results` es siempre `NULL`. Solo se guardan los metadatos extraídos (sold_to, productos, fechas). Esta es una decisión de privacidad y seguridad irrompible.
