#!/bin/bash
# =====================================================================
# DEPLOY SCRIPT — Llamado por GitHub Actions via SSH
# Uso: ./scripts/deploy.sh [beta|prod]
# =====================================================================

set -e

ENV=${1:-beta}
PROJECT_DIR="/opt/web-projects/DX-License-Manager"
COMPOSE_FILE="infra/docker-compose.${ENV}.yml"

echo "🚀 Desplegando entorno: $ENV"
cd $PROJECT_DIR

echo "📥 Actualizando código..."
git pull origin $([ "$ENV" = "prod" ] && echo "main" || echo "dev")

echo "🐳 Levantando contenedores..."
docker compose --project-directory . -f $COMPOSE_FILE up -d --build

echo "🗃️ Ejecutando migraciones..."
if docker compose --project-directory . -f $COMPOSE_FILE ps | grep -q "php-fpm-${ENV}"; then
    docker compose --project-directory . -f $COMPOSE_FILE exec -T php-fpm-${ENV} php artisan migrate --force
    echo "🔧 Limpiando caché..."
    docker compose --project-directory . -f $COMPOSE_FILE exec -T php-fpm-${ENV} php artisan config:cache
    docker compose --project-directory . -f $COMPOSE_FILE exec -T php-fpm-${ENV} php artisan route:cache
    docker compose --project-directory . -f $COMPOSE_FILE exec -T php-fpm-${ENV} php artisan view:cache
else
    echo "⚠️ Servicio php-fpm-${ENV} no detectado. Saltando tareas de Laravel (Fase 0)."
fi

echo "✅ Deploy $ENV completado"