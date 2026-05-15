# ✅ Checklist de Entrega — DX License Manager

El agente completa este checklist antes de cada `/log`, `/merge` o `/end`.
Su último mensaje antes de cualquiera de estos comandos debe incluir: **"Checklist verificado ✅"**

---

## Checklist Pre-`/log` (tras cada subtarea)

```
- [ ] git status — sin archivos extra o inesperados
- [ ] git diff --cached — el diff muestra exactamente lo que debe commitear
- [ ] Sin secrets en el diff (API keys, contraseñas, tokens)
- [ ] CHANGELOG.md actualizado con la entrada correspondiente
- [ ] La tarea está verificada con evidencia (curl, migrate:status, log, Tinker)
```

---

## Checklist Pre-`/merge` (antes de hacer el PR)

```
- [ ] CI en verde — verificado en GitHub Actions
- [ ] La feature funciona en beta.dxpro.es — verificado manualmente
- [ ] Sin archivos de debug, console.log o dd() en el código
- [ ] Sin secrets hardcodeados en ningún archivo del diff
- [ ] Rutas nuevas tienen middleware JwtAuth + CheckPermission
- [ ] Vistas nuevas siguen los HTMLs estáticos de infra/html/
- [ ] CHANGELOG.md tiene la entrada de todo lo implementado
- [ ] BACKLOG.md — tarea movida a Completado
- [ ] git status limpio — "nothing to commit, working tree clean"
```

---

## Checklist Pre-`/end` (antes de cerrar sesión)

```
- [ ] Todo está commiteado — git status limpio
- [ ] Push a la rama activa — sin trabajo local sin subir
- [ ] last_brain actualizado con el estado mental actual
- [ ] ACTIVE_CONTEXT.md actualizado con decisiones y handover
- [ ] HANDOFF.md actualizado con la tarea inmediata para la próxima sesión
- [ ] Si hay /merge pendiente — ejecutarlo antes de cerrar
- [ ] Los stacks están operativos — docker compose ps
```

---

## Checklist de Seguridad (en cualquier momento)

```
- [ ] Ninguna ruta sirve archivos .lic por ruta física — solo por ID de BD
- [ ] file_path en ai_audit_results es NULL — política Solo Log
- [ ] Los .env reales no están en Git — verificar .gitignore
- [ ] Ningún secret hardcodeado en el código — usar config() o env()
- [ ] Todas las rutas protegidas tienen jwt + CheckPermission
```

---

## Señales de Alerta — Parar y Verificar

Si el agente detecta alguna de estas situaciones, debe parar y preguntar antes de continuar:

- La tarea requiere tocar más de un archivo en la misma respuesta
- Hay un error en los logs que no se entiende completamente
- El plan requiere `migrate:fresh` o cualquier acción destructiva
- La rama activa no corresponde a la tarea que se está ejecutando
- El contexto se está llenando (respuestas lentas, cortes)
- Han pasado más de 3 pasos sin que el desarrollador haya confirmado nada
