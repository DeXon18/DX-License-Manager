# 🔍 Debug Sistemático — DX Management Portal

Metodología obligatoria para cualquier bug, error o comportamiento inesperado.
Idioma de respuesta: **castellano siempre**.

---

## Proceso (en orden)

### 1. Entender el Problema

Antes de tocar nada, responder:
- ¿Qué está pasando exactamente vs. qué debería pasar?
- ¿Se puede reproducir de forma consistente?
- ¿Afecta a beta, prod o ambos?
- ¿Cuándo empezó? ¿Tras qué commit?

```bash
git log --oneline -10          # últimos commits
git diff HEAD~1 HEAD           # qué cambió en el último commit
```

### 2. Revisar los Logs Primero

Siempre antes de hacer hipótesis:

```bash
# Logs del stack completo
docker compose --project-directory . -f infra/docker-compose.beta.yml logs --tail=100

# Solo nginx (errores HTTP)
docker compose --project-directory . -f infra/docker-compose.beta.yml logs nginx-beta

# Solo PHP (errores de aplicación)
docker compose --project-directory . -f infra/docker-compose.beta.yml logs php-beta

# Laravel logs (dentro del contenedor)
docker exec -it dx-php-beta sh
tail -f storage/logs/laravel.log
```

### 3. Generar Hipótesis (de más a menos probable)

| Probabilidad | Categoría |
|:---|:---|
| 🔴 Alta | Cambio reciente en el área afectada |
| 🟠 Media | Problema de configuración (.env, config/) |
| 🟠 Media | Estado de datos inconsistente en BD o Redis |
| 🟡 Baja | Problema de Docker (volúmenes, red interna) |
| 🟡 Baja | Dependencia externa (proveedor IA, Cloudflare) |

No asumir la causa más obvia — verificar cada hipótesis con evidencia.

### 4. Investigación Sistemática (Bisección)

Reducir el espacio del problema a la mitad en cada paso:

1. ¿Es un problema de red/nginx o de la aplicación PHP?
   → Testear directamente el puerto interno del contenedor PHP
2. ¿Es un problema de la lógica o de los datos?
   → Probar con datos conocidos/controlados
3. ¿Es reproducible en beta pero no en prod (o viceversa)?
   → Comparar `.env.beta` vs `.env.prod`

### 5. Los 5 Porqués

Para cada bug, preguntar "¿por qué?" hasta llegar a la causa raíz real, no al síntoma:

```
Síntoma: La página da 500
¿Por qué? → Error en AuditService.php línea 45
¿Por qué? → $provider es null
¿Por qué? → GEMINI_API_KEY no está en .env.prod
¿Por qué? → El .env.prod no se actualizó al añadir la variable
Causa raíz: El proceso de deploy no incluye verificación de variables de entorno
```

### 6. Fix y Verificación

- El fix debe resolver la **causa raíz**, no el síntoma.
- Antes de presentarlo: ¿tiene efectos secundarios? ¿rompe algo más?
- Verificar con los pasos exactos de reproducción del bug.
- Añadir test para prevenir regresión.

```bash
# Verificar que el fix no rompe nada
docker exec -it dx-php-beta sh
php artisan test
```

---

## Checklist Obligatoria

- [ ] ¿Revisé los logs antes de hacer hipótesis?
- [ ] ¿Identifiqué en qué commit empezó?
- [ ] ¿Verifiqué mis suposiciones con evidencia?
- [ ] ¿El fix ataca la causa raíz y no el síntoma?
- [ ] ¿Hay tests que prevengan la regresión?
- [ ] ¿Documenté el bug y la solución en CHANGELOG.md?

---

## Formato de Reporte

```
🐛 BUG REPORT
─────────────────────────────────
Síntoma:      [qué está fallando]
Entorno:      [beta / prod / ambos]
Desde cuándo: [commit o fecha aproximada]
Causa raíz:   [explicación técnica concreta]
Fix aplicado: [archivo, línea, qué se cambió]
Verificado:   [cómo se confirmó que está resuelto]
Regresión:    [test añadido / por qué no aplica]
─────────────────────────────────
```
