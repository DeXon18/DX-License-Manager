#!/bin/bash
# =====================================================================
# BACKUP DB SCRIPT — mysqldump + gpg — cron diario
# Uso: ./scripts/backup-db.sh [beta|prod]
# =====================================================================

set -e

ENV=`${1:-prod}
PROJECT_DIR="/opt/web-projects/DX-License-Manager"
COMPOSE_FILE="infra/docker-compose.`${ENV}.yml"
BACKUP_DIR="`$PROJECT_DIR/storage/backups/db"
DATE=`$(date +%Y-%m-%d_%H-%M-%S)
BACKUP_FILE="`$BACKUP_DIR/`${ENV}_`${DATE}.sql"

echo "💾 Iniciando backup de `$ENV..."
cd `$PROJECT_DIR

# Crear backup
docker compose -f `$COMPOSE_FILE exec -T mariadb-`${ENV} \
    mysqldump -u dxportal -p`${DB_PASSWORD} dxportal_`${ENV} > `$BACKUP_FILE

# Cifrar con GPG
gpg --recipient `$BACKUP_GPG_KEY_ID --encrypt `$BACKUP_FILE
rm `$BACKUP_FILE

echo "✅ Backup guardado: `${BACKUP_FILE}.gpg"

# Limpiar backups de más de 30 días
find `$BACKUP_DIR -name "*.gpg" -mtime +30 -delete
echo "🧹 Backups antiguos eliminados"