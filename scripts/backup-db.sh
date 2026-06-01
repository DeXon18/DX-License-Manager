#!/bin/bash
# =====================================================================
# BACKUP DB SCRIPT — mariadb-dump — cron diario
# Uso: ./scripts/backup-db.sh [beta|prod] [manual|system]
# =====================================================================

# Configuración
APP_ENV=$1
TYPE=${2:-manual} # Por defecto manual si no se especifica
BACKUP_DIR="/var/www/html/storage/app/backups/db"
DATE=$(date +%Y-%m-%d_%H-%M-%S)
FILENAME="${APP_ENV}_${TYPE}_${DATE}.sql"

# Cargar variables de entorno si no están presentes (necesario para cron en Docker)
if [ -z "$MYSQL_USER" ]; then
    if [ -f "/var/www/html/.env" ]; then
        echo "Cargando variables desde .env..."
        # Exportar variables ignorando comentarios y líneas vacías
        export $(grep -v '^#' /var/www/html/.env | grep -v '^$' | xargs)
    fi
fi

# Asegurar que el directorio existe
mkdir -p "$BACKUP_DIR"

echo "--- Iniciando backup de base de datos ($APP_ENV - $TYPE) ---"
echo "Destino: $BACKUP_DIR/$FILENAME"

# Ejecutar mariadb-dump (con --ssl=0 para evitar error 2026 en conexiones internas Docker)
mariadb-dump --ssl=0 -h "$DB_HOST" -u "$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE" > "$BACKUP_DIR/$FILENAME"

# Verificar éxito
if [ $? -eq 0 ]; then
    echo "✅ Backup completado con éxito."
    
    # Limpiar backups antiguos (más de 30 días)
    find "$BACKUP_DIR" -name "*.sql" -type f -mtime +30 -delete
    echo "Refrescando permisos..."
    chmod 777 "$BACKUP_DIR/$FILENAME"
    echo "--- Fin del proceso ---"
else
    echo "❌ Error al generar el backup."
    exit 1
fi
