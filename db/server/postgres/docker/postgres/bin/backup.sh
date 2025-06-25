#!/usr/bin/env bash

# Konfiguracja
DB_USER="root"
DB_PASS="secret"
BACKUP_DIR="/backup"
DATE=$(date '+%Y-%m-%d_%H-%M-%S')
RETENTION_DAYS=7

# Upewnij się, że katalog backupów istnieje
mkdir -p "$BACKUP_DIR"

# Pobranie listy wszystkich baz danych (pomijając systemowe)
DATABASES=$(psql -U "$DB_USER" -d postgres -t -c "SELECT datname FROM pg_database WHERE datistemplate = false;" | awk '{$1=$1};1')

# Dump każdej bazy osobno
for DB in $DATABASES; do
    FILE_NAME="dump-${DB}-${DATE}.sql.gz"
    PGPASSWORD="$DB_PASS" pg_dump -U "$DB_USER" -d "$DB" | gzip > "$BACKUP_DIR/$FILE_NAME"
done

# Usunięcie backupów starszych niż 7 dni
find "$BACKUP_DIR" -type f -name "dump-*.sql.gz" -mtime +$RETENTION_DAYS -exec rm {} \;
