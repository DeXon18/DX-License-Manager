#!/bin/bash
# =====================================================================
# BACKUP DB SCRIPT — mysqldump + gpg — cron diario
# Uso: ./scripts/backup-db.sh [beta|prod]
# =====================================================================

# Configuración
APP_ENV=$1
BACKUP_DIR="/var/www/html/storage/app/backups/db"
DATE=$(date +%Y-%m-%d_%H-%M-%S)
FILENAME="${APP_ENV}_${DATE}.sql"

# Asegurar que el directorio existe
mkdir -p $BACKUP_DIR

echo "--- Iniciando backup de base de datos ($APP_ENV) ---"
echo "Destino: $BACKUP_DIR/$FILENAME"

# Ejecutar mysqldump
# Usamos las variables de entorno inyectadas por Docker
if [ "$APP_ENV" == "prod" ]; then
    mysqldump -h mariadb-prod -u $MYSQL_USER -p$MYSQL_PASSWORD $MYSQL_DATABASE > $BACKUP_DIR/$FILENAME
else
    mysqldump -h mariadb-beta -u $MYSQL_USER -p$MYSQL_PASSWORD $MYSQL_DATABASE > $BACKUP_DIR/$FILENAME
fi

# Verificar éxito
if [ $? -eq 0 ]; then
    echo "✅ Backup completado con éxito."
    
    # Limpiar backups antiguos (más de 30 días)
    find $BACKUP_DIR -name "*.sql" -type f -mtime +30 -delete
    echo "Refrescando permisos..."
    chmod 777 $BACKUP_DIR/$FILENAME
    echo "--- Fin del proceso ---"
else
    echo "❌ Error al generar el backup."
    exit 1
fi
