# Integración de Canal Interactivo (Telegram / Teams Bot)

Este documento detalla la arquitectura técnica y el funcionamiento del canal interactivo de consulta para técnicos a través de bots de mensajería (Telegram y Teams).

---

## 1. Arquitectura General

La comunicación entre los clientes de mensajería y el portal se organiza en tres capas:

```
[ Técnico ] <---> [ Bot (Telegram / Teams) ] <---> [ n8n Workflow ] <---> [ Laravel API ]
```

1. **Cliente (Técnico)**: Envía comandos (`/cliente`, `/expiraciones`, `/soldto`) desde la aplicación de mensajería.
2. **Bot / Webhook**: Telegram/Teams notifican el evento al webhook de **n8n**.
3. **Orquestador (n8n)**: Captura el mensaje, extrae los parámetros y realiza una llamada HTTP POST segura al portal Laravel.
4. **Backend (Laravel)**: Procesa la consulta en `/api/bot/query`, realiza búsquedas complejas (fuzzy matches, cruzado de Sold-Tos, Carbon del inventario) y responde con un JSON estructurado de alta fidelidad.
5. **n8n (Respuesta)**: Recibe el JSON, formatea la información en Markdown elegante (utilizando emojis e identación profesional) y responde al chat del técnico.

---

## 2. Endpoint de Laravel: `/api/bot/query`

El portal expone un endpoint seguro y optimizado para el bot:
* **Método**: `POST`
* **Ruta**: `/api/bot/query`
* **Controlador**: `App\Http\Controllers\Api\BotQueryController`

### Seguridad y Autenticación
El acceso al endpoint está blindado mediante autenticación por token en la cabecera HTTP. Soporta de manera nativa y tolerante (limpieza defensiva de CRLF y espacios `trim()`):
1. **Bearer Token**: `Authorization: Bearer <TELEGRAM_BOT_TOKEN>`
2. **Custom Header**: `X-Bot-Token: <TELEGRAM_BOT_TOKEN>`

Los tokens autorizados se configuran en el entorno a través de las variables:
* `BOT_API_TOKEN`
* `TELEGRAM_BOT_TOKEN`
* `N8N_WEBHOOK_SECRET` (como fallback seguro de integración)

---

## 3. Comandos Soportados y Formatos

Las peticiones HTTP POST enviadas al endpoint deben incluir un cuerpo JSON con la estructura:

```json
{
  "command": "nombre_comando",
  "argument": "parámetro_opcional"
}
```

### A. `/cliente [Nombre]`
* **Propósito**: Recupera la ficha completa de inventarios, Sold-To y contratos de un cliente.
* **Inteligencia de Búsqueda**:
  1. Coincidencia exacta de nombre.
  2. Búsqueda por alias registrado en la tabla `client_aliases`.
  3. Algoritmo de similitud Levenshtein fuzzy match con umbral de coincidencia de confianza del **75%** (sin crear registros huérfanos).
* **Payload de entrada**:
  ```json
  {
    "command": "cliente",
    "argument": "Andaltec"
  }
  ```
* **Respuesta exitosa (JSON)**:
  ```json
  {
    "status": "success",
    "type": "client_info",
    "data": {
      "client_id": 142,
      "client_name": "Fundacion Andaltec I+D+I",
      "daemons": [
        {
          "daemon": "moldex3d",
          "sold_to": "1005998",
          "additional_sold_tos": [],
          "hostname": "AOOBRBDHVMRAE",
          "composite": "00-50-56-B3-E5-40",
          "vendor": "moldex",
          "products_count": 15,
          "products": [
            {
              "code": "STUDIO-2025",
              "qty": 3,
              "expiration": "2027-01-14",
              "days_left": 237,
              "status": "healthy"
            }
          ]
        }
      ],
      "contracts": []
    }
  }
  ```

---

### B. `/expiraciones`
* **Propósito**: Diagnóstico rápido de licencias y contratos que requieren renovación inmediata.
* **Cálculo Semántico**:
  * **Expirados**: Días restantes menores a 0.
  * **Críticos**: Días restantes entre 0 y 30 días.
* **Payload de entrada**:
  ```json
  {
    "command": "expiraciones"
  }
  ```
* **Respuesta exitosa (JSON)**:
  ```json
  {
    "status": "success",
    "type": "expirations",
    "data": {
      "expired_licenses": [
        {
          "client": "Akaba Sa",
          "daemon": "ugslmd",
          "sold_to": "1429810",
          "code": "NX11100",
          "expiration": "2026-05-18",
          "days_left": -3
        }
      ],
      "expiring_licenses": [],
      "expired_contracts": [],
      "expiring_contracts": []
    }
  }
  ```

---

### C. `/soldto [ID]`
* **Propósito**: Mapea un identificador Siemens PLM de facturación ("Sold-To") con el cliente dueño en el portal.
* **Búsqueda Bidireccional**:
  * Evalúa el Sold-To primario en la tabla de inventario.
  * Busca dentro de los arrays JSON de Sold-Tos adicionales (`additional_sold_tos`) indexando coincidencias consolidadas.
* **Payload de entrada**:
  ```json
  {
    "command": "soldto",
    "argument": "1005998"
  }
  ```
* **Respuesta exitosa (JSON)**:
  Retorna la misma estructura que `/cliente` filtrada específicamente por el daemon e inventario que coincida con el identificador buscado.

---

## 4. Guía de Configuración en n8n

Para implementar el flujo visual en n8n:

1. **Webhook Trigger (Telegram)**: Configurar trigger para escuchar comandos en el bot corporativo.
2. **Switch/Routing**: Enrutar según el comando `/cliente`, `/expiraciones` o `/soldto`.
3. **HTTP Request**:
   * **URL**: `https://beta.dxpro.es/api/bot/query` (o producción `https://portal.dxpro.es/api/bot/query`)
   * **Método**: `POST`
   * **Headers**:
     * `Accept: application/json`
     * `Content-Type: application/json`
     * `Authorization: Bearer {{ $env.TELEGRAM_BOT_TOKEN }}`
   * **Body**: JSON dinámico capturando el texto enviado por el técnico.
4. **Formatting (Markdown)**: Mapear la respuesta JSON y formatearla utilizando bloques como:
   * 🟢 **Verde** para licencias seguras (`status: healthy` / permanent).
   * 🟡 **Amarillo** para alertas de expiración ≤ 30 días (`status: warning`).
   * 🔴 **Rojo** para licencias caducadas (`status: expired`).
5. **Send Message**: Retornar el texto enriquecido de forma interactiva al técnico que originó la petición.
