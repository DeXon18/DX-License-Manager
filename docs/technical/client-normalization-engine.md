# Motor de Normalización de Clientes (Intelligence Engine)

Este documento describe el funcionamiento técnico del motor de normalización diseñado para resolver discrepancias de nombres entre diferentes fuentes de datos (CSV de contratos y archivos .lic de licencias).

## 🧩 Arquitectura

El motor se basa en el servicio `App\Services\Data\ClientNormalizationService`, el cual orquesta la búsqueda e identificación de clientes mediante tres niveles de validación.

### Niveles de Resolución

1.  **Nivel 1: Coincidencia Exacta**
    *   Busca el nombre (normalizado a Title Case) directamente en la tabla `clients`.
    *   Si existe, devuelve el ID de inmediato.
2.  **Nivel 2: Mapeo de Alias**
    *   Busca en la tabla `client_aliases`. Esta tabla permite vincular erratas conocidas o variaciones de nombre a un cliente principal.
    *   Ejemplo: *"Univ. Pontifica Comillas"* -> Alias de *"Universidad Pontificia Comillas"*.
3.  **Nivel 3: Filtro de Inteligencia (Fuzzy Matching)**
    *   Utiliza el algoritmo de **Levenshtein** para calcular la similitud entre el nombre entrante y todos los clientes existentes.
    *   **Umbral (Threshold)**: Por defecto establecido en **0.85 (85%)**.
    *   **Comportamiento**:
        *   Si la similitud es superior al 85%, el sistema marca el resultado como **`suspicion`** (Sospecha).
        *   Si la similitud es baja o nula, se considera un **`new`** (Nuevo Cliente).

## 🚀 Lógica de Decisión (Workflow)

Cuando el sistema recibe un nombre (ej. desde un CSV):

| Condición | Acción | Resultado |
| :--- | :--- | :--- |
| Match Exacto | Vinculación directa | `status: exact` |
| Match en Alias | Vinculación al cliente principal | `status: alias` |
| Similitud > 85% | Se crea cliente nuevo pero se registra aviso de sospecha | `status: suspicion` |
| Similitud < 85% | Se crea cliente nuevo automáticamente | `status: new` |

## 📊 Implementación en el Importador CSV

El `CsvImportService` ha sido actualizado para integrar este motor. Los resultados de la normalización se reflejan en el log de importación:

-   **Errors**: Fallos críticos de datos.
-   **Warnings**: Mensajes informativos sobre clientes creados automáticamente y, lo más importante, **avisos de sospecha de duplicado**.

### Ejemplo de Warning en Log
> *"Fila 45: El cliente 'Universidad Pontifica Comillas' se parece un 96.15% a 'Universidad Pontificia Comillas'. Se ha creado un nuevo cliente por precaución, revisar posibles duplicados."*

## 🛠️ Cómo Resolver Sospechas

Cuando el Dashboard o el Log alertan de una sospecha:
1.  Verificar si el nuevo cliente es realmente un duplicado.
2.  Si es un duplicado, añadir el nombre erróneo a la tabla `client_aliases` vinculado al cliente correcto.
3.  Eliminar el cliente duplicado.
4.  A partir de ese momento, el sistema lo procesará correctamente por el Nivel 2.

## 📝 Próximos Pasos (Hoja de Ruta)

- [ ] Interfaz de usuario para la resolución de sospechas (Bandeja de Mapeo).
- [ ] Integración del `Sold-To` como puente automático de alias para proveedores Siemens.
