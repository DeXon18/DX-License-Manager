#!/bin/bash
# =====================================================================
# BACKUP DB SCRIPT — mysqldump + gpg — cron diario
# Uso: ./scripts/backup-db.sh [beta|prod]
# =====================================================================

set -e

ENV=${1:-beta}
PROJECT_DIR="/opt/web-projects/DX-License-Manager"
COMPOSE_FILE="infra/docker-compose.${ENV}.yml"
BACKUP_DIR="${PROJECT_DIR}/backend/storage/backups/db"
DATE=$(date +%Y-%m-%d_%H-%M-%S)
BACKUP_FILE="${BACKUP_DIR}/${ENV}_${DATE}.sql"

# Asegurar que el directorio existe
mkdir -p "${BACKUP_DIR}"

echo "💾 Iniciando backup de ${ENV}..."
cd "${PROJECT_DIR}"

# Extraer contraseña del archivo .env correspondiente (limpiando posibles retornos de carro)
DB_PASSWORD=$(grep "^DB_PASSWORD=" "infra/.env.${ENV}" | cut -d'=' -f2 | tr -d '\r')
DB_NAME=$(grep "^DB_DATABASE=" "infra/.env.${ENV}" | cut -d'=' -f2 | tr -d '\r')
DB_USER=$(grep "^DB_USERNAME=" "infra/.env.${ENV}" | cut -d'=' -f2 | tr -d '\r')

# Crear backup usando MYSQL_PWD para evitar problemas con caracteres especiales
docker compose --project-directory . -f "${COMPOSE_FILE}" exec -T "mariadb-${ENV}" \
    sh -c "MYSQL_PWD='${DB_PASSWORD}' mysqldump -u ${DB_USER} ${DB_NAME}" > "${BACKUP_FILE}"

# Cifrar con GPG (solo si la llave está configurada)
if [ ! -z "$BACKUP_GPG_KEY_ID" ]; then
    gpg --recipient "$BACKUP_GPG_KEY_ID" --encrypt "$BACKUP_FILE"
    rm "$BACKUP_FILE"
    echo "✅ Backup cifrado guardado: ${BACKUP_FILE}.gpg"
else
    echo "⚠️ Backup guardado sin cifrar: ${BACKUP_FILE}"
fi

# Limpiar backups de más de 30 días
find "${BACKUP_DIR}" -name "*.sql" -o -name "*.gpg" -mtime +30 -delete
echo "🧹 Limpieza de backups antiguos completada"

# Asegurar permisos para gestión web
chmod 777 "${BACKUP_DIR}"/*
