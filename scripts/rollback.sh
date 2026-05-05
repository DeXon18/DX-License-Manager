#!/bin/bash
# =====================================================================
# ROLLBACK SCRIPT — Volver al commit anterior en caso de fallo
# Uso: ./scripts/rollback.sh [beta|prod]
# =====================================================================

set -e

ENV=`${1:-beta}
PROJECT_DIR="/opt/web-projects/DX-License-Manager"
COMPOSE_FILE="infra/docker-compose.`${ENV}.yml"

echo "⏪ Iniciando rollback en entorno: `$ENV"
cd `$PROJECT_DIR

echo "📥 Volviendo al commit anterior..."
git revert HEAD --no-edit
git push origin `$([ "`$ENV" = "prod" ] && echo "main" || echo "dev")

echo "🐳 Reiniciando contenedores..."
docker compose -f `$COMPOSE_FILE up -d --build

echo "🗃️ Ejecutando migraciones..."
docker compose -f `$COMPOSE_FILE exec -T php-fpm-`${ENV} php artisan migrate --force

echo "✅ Rollback `$ENV completado"