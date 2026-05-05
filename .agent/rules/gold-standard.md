# Implementaciones de Referencia — DX License Manager

Estos son los archivos "Gold Standard" del proyecto.
Ante cualquier duda de arquitectura o estilo, el agente abre estos archivos y copia su estructura.

---

## Controller de Referencia

**Archivo:** `app/Http/Controllers/Admin/FeatureFlagController.php`

Por qué es el gold standard:

- Controller delgado — solo recibe, delega y responde
- Usa dependency injection en el constructor
- Sin lógica de negocio — delega al modelo y al helper
- Maneja correctamente la invalidación de caché
- Rutas protegidas con middleware de rol

**Patrón a replicar:**

```php
class XxxController extends Controller
{
    public function __construct(private XxxService $service) {}

    public function index(): View
    {
        $data = $this->service->getAll();
        return view('xxx.index', compact('data'));
    }

    public function store(StoreXxxRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());
        return redirect()->route('xxx.index')->with('success', 'Creado correctamente');
    }
}
```

---

## Service de Referencia

**Archivo:** `app/Services/ContractImportService.php`

Por qué es el gold standard:

- Toda la lógica de negocio está aquí, no en el controller
- Usa `DB::transaction()` para operaciones críticas
- Usa `updateOrCreate()` para upsert limpio
- Normalización de datos antes de guardar
- Registro de auditoría en `import_logs`
- Manejo de errores con try/catch y registro en el log

---

## Model de Referencia

**Archivo:** `app/Models/Contract.php`

Por qué es el gold standard:

- `$fillable` explícito — sin mass assignment abierto
- Relaciones Eloquent correctamente definidas
- Casts para fechas y booleanos
- Sin lógica de negocio — solo relaciones y scopes
- Scopes para filtros frecuentes

---

## Middleware de Referencia

**Archivo:** `app/Http/Middleware/CheckPermission.php`

Por qué es el gold standard:

- Verifica rol explícitamente — no asume permisos
- Devuelve 403 con mensaje claro si no tiene acceso
- Compatible con el sistema de roles del proyecto

---

## FormRequest de Referencia

**Archivo:** `app/Http/Requests/StoreLicenseFileRequest.php`

Por qué es el gold standard:

- Validación completa antes de llegar al controller
- Mensajes de error en castellano
- Reglas de autorización por rol

---

## Vista Blade de Referencia

**Archivo:** `infra/html/03-herramientas.html` (HTML estático aprobado)

Por qué es el gold standard visual:

- Fuente Inter
- Colores por vendor (Siemens: #009999, Moldex3D: #ed1c24)
- Cards con borde superior de color de acento
- Badges con font IBM Plex Mono
- Dark mode con variables CSS

**Regla:** Cualquier vista nueva en Blade debe replicar este HTML. Sin creatividad propia.
