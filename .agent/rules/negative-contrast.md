# Técnica: Validación por Contraste Negativo
> Ruta: `.agent/rules/negative-contrast.md`
> Uso: Opcional — activar con el prompt maestro cuando se quiere máxima calidad en una tarea crítica

---

## Qué Es Esta Técnica

Antes de escribir código, el agente genera primero el **anti-patrón** — cómo lo haría mal — basándose en lo que las skills y reglas del proyecto prohíben explícitamente.

Esto obliga al agente a procesar las reglas en profundidad en lugar de simplemente completar texto. El resultado es código que realmente sigue los estándares del proyecto, no código genérico de internet.

---

## El Prompt Maestro

Usar este prompt cuando la tarea es crítica — nueva feature importante, código de seguridad, UI que debe seguir el design system:

```
Antes de escribir código, aplica la técnica de Contraste Negativo:

PASO 1 — ANTI-PATRÓN:
Muéstrame cómo lo haría mal un desarrollador que no conoce las reglas 
de este proyecto. Basa el ejemplo en lo que prohíben explícitamente 
las skills cargadas y el AGENTS.md.

PASO 2 — DIAGNÓSTICO:
Explícame qué regla concreta del proyecto viola cada parte del anti-patrón.
Cita el archivo y la sección exacta (ej: "operational-principles.md §5 — 
prohibido crear múltiples archivos a la vez").

PASO 3 — SOLUCIÓN:
Solo ahora, escribe la implementación correcta siguiendo las reglas del proyecto.
```

---

## Cuándo Aplicarlo

| Tarea | Usar contraste negativo |
|:---|:---|
| Nueva vista Blade importante | ✅ Sí — verifica que sigue el design system |
| Middleware de autenticación | ✅ Sí — crítico para seguridad |
| Nuevo Service de negocio | ✅ Sí — verifica arquitectura |
| Fix de bug menor | ❌ No necesario |
| Actualizar un comentario | ❌ No necesario |
| Migración de BD sencilla | ❌ No necesario |

---

## Ejemplo Aplicado a Este Proyecto

**Tarea:** Crear el `LicenseFileController`

**PASO 1 — Anti-patrón que generaría el agente:**

```php
// ❌ CÓMO NO HACERLO
class LicenseFileController extends Controller
{
    public function download(Request $request)
    {
        // Ruta física expuesta en la URL — viola security-check.md §2
        $path = $request->get('path');
        return response()->download(storage_path($path));
    }

    public function store(Request $request)
    {
        // Sin validación de rol — viola AGENTS.md §7
        // Sin FormRequest — viola code-review-reasoning.md §2
        $file = $request->file('license');
        // Guardando con nombre original — path traversal risk
        $file->store($file->getClientOriginalName());

        // Lógica de negocio en el controlador — viola operational-principles.md
        $content = file_get_contents($file->getRealPath());
        // Enviando el .lic completo a la IA — viola security-check.md §3
        $aiResult = Http::post('https://api.gemini.com', ['content' => $content]);
    }
}
```

**PASO 2 — Diagnóstico:**

- `?path=storage/licenses/...` → `security-check.md §2`: "Prohibido pasar rutas en URL. Solo IDs de BD."
- Sin `CheckPermission` → `AGENTS.md §7`: "Middleware JwtAuth + CheckPermission en todas las rutas protegidas."
- Sin `FormRequest` → `code-review-reasoning.md §2`: "Las validaciones de Laravel deben usar FormRequest."
- Lógica en controlador → `operational-principles.md §5`: "La lógica de negocio va en Services, no en Controllers."
- `.lic` completo a la IA → `security-check.md §3`: "No enviar el archivo completo. Solo metadatos extraídos."

**PASO 3 — Implementación correcta:**

```php
// ✅ CÓMO HACERLO BIEN
class LicenseFileController extends Controller
{
    public function __construct(private LicenseFileService $service) {}

    public function download(Request $request)
    {
        // Solo ID de BD — nunca ruta física
        $file = LicenseFile::findOrFail($request->get('id'));
        // Verificar que pertenece al usuario/cliente autorizado
        abort_if(!auth()->user()->canAccess($file), 403);
        return Storage::disk('licenses')->download($file->stored_path);
    }

    public function store(StoreLicenseFileRequest $request)
    {
        // Lógica delegada al Service
        $result = $this->service->store($request->validated());
        return response()->json($result);
    }
}
```

---

## Beneficio Secundario — Detectar Lagunas en las Reglas

Si el agente no puede generar un anti-patrón claro basado en las reglas, significa que esa área no está bien cubierta en la documentación. Es una señal para mejorar ese archivo de rules o esa skill.

Anotar en `.agent/lessons.md` si se detecta una laguna.

---

## Integración con los MEMOs

Cuando el contraste negativo revela un patrón recurrente que no estaba documentado, generar un `/memo` con el aprendizaje para que no se repita en futuras sesiones.

```
Contraste negativo revela laguna
  → Corregir la regla en el archivo correspondiente
  → Generar /memo con el aprendizaje
  → Commit ambos juntos
```
