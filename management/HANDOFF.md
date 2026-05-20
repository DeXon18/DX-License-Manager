# HANDOFF — DX License Manager
> Última actualización: 2026-05-20 11:25  
> Sesión en: Proxmox Beta Environment  
> Rama activa: feature/ai-normalization-force

---

## Estado General

**Fase actual:** Fase 23 — Normalización de Identidades con IA  
**Stack beta:** ✅ running  
**Stack prod:** ✅ running  

---

## Qué se hizo en esta sesión

1. **Flujo de Normalización Cognitivo (IA)**:
   * Diseñado e implementado `ClientAiNormalizationService.php` con pre-filtrado local por tokens (`LIKE`) y llamadas de validación cognitiva en cadena de fallback: **Gemini -> DeepSeek -> OpenRouter**.
   * Integrado el fallback en `ClientNormalizationService.php` como Nivel 3.5 para desviar sospechas de similitud media/baja (< 85%) con confianza >= 80% al estado `suspicion`.
   * Enriquecida la interfaz de la Bandeja de Normalización con badges de IA NOC Pro, indicación del proveedor utilizado y razones explicativas del modelo.

2. **Unificación Forzada Manual (Incidencia Urovesa)**:
   * Desarrollada una característica de unificación forzada para entradas de tipo `NUEVA IDENTIDAD`.
   * Implementado un buscador autocomplete predictivo nativo `<datalist>` HTML5 que renderiza dinámicamente todos los clientes del sistema en orden alfabético.
   * Añadido el botón de acción **FORZAR** que unifica atómicamente contratos, licencias, demonios, contactos y auditorías, asocia el alias y elimina el duplicado de la base de datos de manera limpia.

3. **Pruebas y Hardening**:
   * Creados tests robustos y mocks para simular llamadas IA exitosas, de baja confianza y fallos en `ClientNormalizationTest.php`.
   * Limpieza total de estilos locales Blade y migración al archivo modular unificado `modules/dx-v2-import.css` conforme a `DESIGN.md`.
   * Git Checkpoints A, B, C, D, E completados con tags `v1.23.0-rc1`, `v1.23.0-rc2` y release final `v1.23.0`.

---

## Qué falta por hacer (próxima sesión)

### Tarea inmediata (empezar aquí)
1. **Aprobar Pull Request y Merge**:
   * Revisar los cambios de la rama `feature/ai-normalization-force` en GitHub.
   * Fusionala sobre la rama `dev` tras la revisión visual interactiva de Oskar.

### Tareas siguientes
1. Realizar pruebas con importaciones reales de archivos `.lic` y ficheros CSV semanales.
2. Continuar con el backlog de mantenimiento visual y técnico del panel administrativo.

---

## Contexto técnico importante

* Las API keys para Gemini, DeepSeek y OpenRouter están configuradas de forma segura en `backend/.env`.
* Las llamadas a la IA tienen un timeout de 10-12 segundos y están envueltas en `try-catch` con degradación natural a `new client` en caso de fallo, garantizando alta tolerancia a caídas de red o fallos de tokens.
* Los estilos de normalización de la IA se encuentran al final del archivo `modules/dx-v2-import.css` respetando el estándar modular de diseño del proyecto.

---

## Bloqueos o problemas sin resolver

* Ninguno. Todos los tests de normalización en SQLite corren y pasan al 100%.

---

## Estado de archivos clave

| Archivo | Estado |
|:---|:---|
| `infra/.env.prod` | ✅ configurado |
| `infra/.env.beta` | ✅ configurado |
| `backend/.env` | ✅ configurado |
| `backend/vendor/` | ✅ instalado |

---

## Comandos útiles para la próxima sesión

```bash
# Cambiar a la rama de la funcionalidad
git checkout feature/ai-normalization-force

# Entrar al contenedor PHP de Beta
docker exec -it dx-php-beta sh

# Ejecutar tests forzando SQLite en memoria
docker exec -e DB_CONNECTION=sqlite -e DB_DATABASE=:memory: dx-php-beta php artisan test --filter=ClientNormalizationTest
```
