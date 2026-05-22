# 📋 Plan de Implementación: Auditoría de Costes IA

Este documento describe la arquitectura y los pasos para implementar un sistema de telemetría de tokens consumidos por los motores de IA en el portal DX License Manager.

---

## 1. Contexto y Objetivos
Actualmente, el sistema consume IA mediante varias integraciones (Normalización de Clientes, Generador COD, y Auditoría de Licencias vía n8n). El objetivo es tener observabilidad técnica de cuántos tokens (prompt/completion) se envían a Gemini, DeepSeek y OpenRouter, permitiendo calcular costes y optimizar el sistema.

---

## 2. Cambios Propuestos

### Fase 1: Capa de Base de Datos
Crearemos una tabla para la persistencia transaccional del uso de la IA.

#### `database/migrations/YYYY_MM_DD_HHMMSS_create_ai_token_logs_table.php`
- `id` (bigint, autoincrement)
- `provider` (string): `gemini`, `deepseek`, `openrouter`, `local`, `n8n`.
- `action` (string): `normalization_pair`, `normalization_search`, `composite_parse`, `license_audit`.
- `prompt_tokens` (integer)
- `completion_tokens` (integer)
- `total_tokens` (integer)
- `user_id` (foreignId nullable): Para auditoría de quién disparó el coste.
- `created_at` / `updated_at` (timestamps)

#### `backend/app/Models/AiTokenLog.php`
- Definición de propiedades `$fillable` y relaciones (belongsTo User).

---

### Fase 2: Captura de Tokens en Capa Lógica

Modificaremos los servicios actuales para interceptar y leer la propiedad `usage` (o `usageMetadata`) de las APIs.

#### `ClientAiNormalizationService.php`
- **Gemini**: Extraer de `$resData['usageMetadata']` (`promptTokenCount`, `candidatesTokenCount`, `totalTokenCount`).
- **DeepSeek/OpenRouter**: Extraer de `$resData['usage']` (`prompt_tokens`, `completion_tokens`, `total_tokens`).
- Guardar en `AiTokenLog` tras cada llamada HTTP exitosa.

#### `CompositeParserService.php`
- Extraer de la respuesta de Gemini el `usageMetadata` y guardar en BD con acción `composite_parse`.

#### `AuditService.php` (Integración n8n)
- Se actualizará el endpoint que recibe el webhook de n8n para que, si el payload JSON trae los metadatos de tokens, los procese y guarde con la acción `license_audit`.
- *(Opcional en n8n)*: Añadir bloque en n8n que adjunte `$json.usage` al output del webhook.

---

### Fase 3: Capa de Visualización (UI/UX)
Crearemos una sección en el panel NOC Pro para mostrar estas métricas.

#### `AiAuditCostController.php`
- Recuperar la suma de tokens del mes actual y del total histórico.
- Recuperar el desglose por `provider` y `action`.

#### `backend/resources/views/admin/system/ai-costs.blade.php`
- Diseño basado en **Bento Grid** (`dx-v2-*`).
- **Bloque Superior**: Tres tarjetas estadísticas elevadas (`Tokens Prompt`, `Tokens Completion`, `Total Histórico`).
- **Bloque Central**: Desglose visual por motor IA (usando los gradientes de la marca, ej: azul eléctrico para DeepSeek, azul/violeta para Gemini).
- **Tabla Inferior**: Historial crudo de los últimos 100 registros con paginación, mostrando Fecha, Acción, Motor y Consumo.

#### Rutas y Menú
- En `routes/web.php` se registrará `/admin/system/ai-costs`.
- En `dashboard.blade.php` (el NOC Pro de Sistema), se agregará un botón/enlace a esta nueva sección.

---

## 3. Checklist de Ejecución

✅ **CHECKLIST — Auditoría de Costes IA**
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
- [ ] **Paso 1:** Crear migración `create_ai_token_logs_table` y modelo `AiTokenLog`.
- [ ] **Paso 2:** Extraer metadata de tokens en `ClientAiNormalizationService` y registrar.
- [ ] **Paso 3:** Extraer metadata de tokens en `CompositeParserService` y registrar.
- [ ] **Paso 4:** Actualizar `AuditService` para aceptar métricas del webhook n8n.
- [ ] **Paso 5:** Crear `AiAuditCostController` con lógica de sumatorias y agrupaciones.
- [ ] **Paso 6:** Maquetar `ai-costs.blade.php` siguiendo diseño industrial NOC Pro.
- [ ] **Paso 7:** Añadir rutas y anclajes visuales desde el Dashboard de Sistema.
- [ ] **Verificación final:** Ejecutar prueba y verificar que se guardan los consumos en la BD y se renderizan.
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
