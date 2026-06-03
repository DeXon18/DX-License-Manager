# Post-Mortem: Integración de Inventario Siemens y Webhook n8n

**Fecha:** 3 de Junio de 2026
**Contexto:** Se ha revertido y eliminado la rama `feature/siemens-inventory` para reevaluar la estrategia de integración del inventario de Siemens.

Este documento recopila los problemas técnicos y conceptuales encontrados, sirviendo como base para replantear la solución con el equipo.

---

## 1. Problemas de Diseño de Dominio (Lógica de Negocio)

### Confusión entre Inventario Físico y Contractual
*   **El Problema:** Se intentó unificar en la misma vista y lógica el **inventario físico** (archivos `.lic` que se suben al servidor) con el **inventario contractual** (archivo CSV semanal provisto por Siemens).
*   **Consecuencia:** La interfaz de usuario (`clients.index`) y los controladores terminaron mezclando datos de fuentes distintas, lo que desvirtuó el propósito original de la plataforma y generó confusión operativa. Son dominios que deben tratarse de forma independiente.

### Pérdida de Datos en Normalización
*   **El Problema:** En el `NormalizationController`, la acción de unificar clientes duplicados eliminaba al cliente secundario sin migrar previamente sus relaciones de `ClientMapping`.
*   **Consecuencia:** Al normalizar, el sistema destruía silenciosamente los vínculos entre los IDs de instalación del CSV y el cliente, provocando la desaparición de inventario contractual de la plataforma.

### Relaciones de Eloquent Incorrectas
*   **El Problema:** Al calcular contadores para la UI, se modificó el `ClientController` invocando el método `$client->clientMappings()`. 
*   **Consecuencia:** Error crítico `BadMethodCallException`, ya que el método definido en el modelo `Client` se llama `mappings()`. Faltó revisión del modelo antes de implementar la llamada.

---

## 2. Problemas de Seguridad e Integración con N8N (Webhook IA)

Al intentar asegurar la comunicación del webhook desde n8n hacia Laravel (Fase 3 de Hardening), nos encontramos con tres bloqueos críticos de configuración:

### Error `DECODER routines::unsupported`
*   **El Problema:** El nodo Crypto de n8n se configuró con la acción **"Sign"** (Firma Asimétrica), intentando pasarle una clave secreta de texto simple (`N8N_WEBHOOK_SECRET`). 
*   **El Porqué:** La acción "Sign" requiere una clave privada real en formato `.pem` (como RSA). Para firmar usando una contraseña o secreto compartido (como se hace en nuestra API), se debe usar la acción **"HMAC"**.

### Error de Sintaxis n8n (Header Inválido)
*   **El Problema:** Laravel recibía la firma HMAC con un signo igual sobrante al inicio (ej. `x-n8n-signature: =91ec1f...`), provocando que la validación `hash_equals` fallara siempre.
*   **El Porqué:** En el nodo HTTP Request de n8n, al configurar el header se escribió `=={{ $json.signature }}` en lugar de `={{ $json.signature }}`. En n8n, el primer `=` indica el inicio de una expresión, y el segundo se renderizó como texto literal.

### Mismatched Payload (Discrepancia de Body)
*   **El Problema:** El log de error del webhook mostraba que el payload que recibía Laravel estaba vacío: `"body": {}`.
*   **El Porqué:** El flujo generaba la firma HMAC basada en el JSON completo (`rawPayload`), pero el nodo HTTP Request final enviaba un cuerpo vacío o un JSON serializado de otra forma. Para que el HMAC coincida, **el Body enviado por HTTP debe ser byte a byte idéntico a la cadena que se firmó**. Requiere configurar el nodo HTTP Request para enviar el Body en formato "Raw" e inyectarle el `rawPayload`.

---

## 3. Recomendaciones para el Nuevo Planteamiento

1.  **Aislamiento de Dominios:** Separar drásticamente la gestión del "CSV Contractual" de los "Archivos .LIC Físicos" en la UI y la base de datos.
2.  **Validación de Nodos n8n:** Asegurarse de que el flujo de N8N usa **Crypto > HMAC** y **HTTP Request > Raw Body**, pasando exactamente la misma variable (`rawPayload`) a ambos nodos.
3.  **Tests de Normalización:** Antes de avanzar con futuras ramas, escribir test que garanticen que la unificación de clientes transfiere correctamente los `ClientMapping` (y futuras tablas).
