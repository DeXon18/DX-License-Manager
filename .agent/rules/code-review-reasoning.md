# 🔎 Code Review — DX Management Portal

Metodología obligatoria para revisar cualquier código antes de hacer merge.
Idioma de feedback: **castellano siempre**.

---

## Proceso de Revisión

### 1. Contexto del Cambio

Antes de revisar una línea de código:
- ¿Es una feature, fix, refactor o chore?
- ¿Qué problema resuelve?
- ¿Qué fase del proyecto corresponde?
- ¿Tiene tests?

### 2. Corrección Lógica

- ¿El código hace lo que se supone que debe hacer?
- ¿Los casos de error están manejados? (try/catch, validaciones, null checks)
- ¿Las validaciones de Laravel (`FormRequest`) cubren todos los campos?
- ¿Las migraciones son reversibles (`down()` correcto)?

### 3. Seguridad — Aplicar `security-check.md`

Verificar siempre contra las reglas de `.agents/rules/security-check.md`:

- ✅ Sin rutas de archivo en URLs — solo IDs (`/download?id=UUID`)
- ✅ Middleware `JwtAuth` + `CheckPermission` en todas las rutas protegidas
- ✅ Sin secrets hardcodeados — solo `config()` o `env()`
- ✅ Input validado con `FormRequest` antes de llegar al controlador
- ✅ Queries Eloquent — sin concatenación de strings con input externo (SQL injection)
- ✅ XSS — Blade escapa automáticamente con `{{ }}`, verificar que no se use `{!! !!}` sin justificación

### 4. Rendimiento

- ¿Hay N+1 queries? (usar `with()` para eager loading en Eloquent)
- ¿Las queries pesadas están cacheadas en Redis?
- ¿Los jobs de auditoría IA van a la cola Redis y no bloquean el hilo principal?
- ¿Los listados tienen paginación?

```php
// ❌ N+1
$licenses = License::all();
foreach ($licenses as $license) {
    echo $license->vendor->name;
}

// ✅ Eager loading
$licenses = License::with('vendor')->paginate(20);
```

### 5. Arquitectura Laravel

- ¿La lógica de negocio está en Services, no en Controllers?
- ¿Los Controllers son delgados — solo reciben, delegan y responden?
- ¿Los Jobs solo orquestan — la lógica real está en Services?
- ¿Los Models no tienen lógica de negocio — solo relaciones y scopes?
- ¿Se siguen los patrones del proyecto (FallbackChain, ProcessAuditJob)?

### 6. Calidad de Código

- ¿Los nombres de variables, métodos y clases son descriptivos en inglés?
- ¿No hay código muerto o comentado sin justificación?
- ¿Las funciones hacen una sola cosa (Single Responsibility)?
- ¿Hay complejidad innecesaria que se pueda simplificar?

### 7. Tests

- ¿Hay tests Feature para los endpoints nuevos?
- ¿Hay tests Unit para los Services?
- ¿Los casos de error están cubiertos (401, 403, 404, 422)?

```bash
# Ejecutar tests dentro del contenedor
docker exec -it dx-php-beta sh
php artisan test --filter=NombreDelTest
```

### 8. Docker y Entorno

Si el cambio toca `infra/`:
- ¿Los healthchecks siguen funcionando?
- ¿Las nuevas variables de entorno están en `.env.example` (sin valores reales)?
- ¿El Dockerfile sigue compilando sin errores?

---

## Formato de Feedback

Toda observación usa este formato:

```
[SEVERIDAD] Archivo.php · línea XX
Problema:    descripción clara del problema
Por qué:     explicación del impacto
Sugerencia:  recomendación concreta (con ejemplo si aplica)
```

**Niveles de severidad:**

| Icono | Nivel | Acción requerida |
|:---|:---|:---|
| 🔴 | Crítico | Bloquea el merge — debe corregirse |
| 🟠 | Importante | Debe corregirse antes del merge |
| 🟡 | Sugerencia | Recomendable pero no bloquea |
| 💡 | Detalle | Mejora de calidad, opcional |

---

## Cierre de Review

Al terminar, resumir:

```
✅ CODE REVIEW — [nombre del PR/rama]
─────────────────────────────────────
🔴 Críticos:    X
🟠 Importantes: X
🟡 Sugerencias: X
💡 Detalles:    X

Veredicto: [APROBADO / CAMBIOS REQUERIDOS]
Próximo paso: [merge a dev / corregir items críticos]
─────────────────────────────────────
```

El tono siempre es constructivo. Explicar el **por qué** de cada observación.
Reconocer explícitamente las buenas prácticas encontradas.
