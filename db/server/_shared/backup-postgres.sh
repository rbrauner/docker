#!/usr/bin/env bash
# Dump every non-template database to /backup (Postgres, pgvector).
# Mounted as /bin/backup.sh; credentials come from the container env.
set -euo pipefail

BACKUP_DIR="/backup"
RETENTION_DAYS="${BACKUP_RETENTION_DAYS:-7}"
DATE=$(date '+%Y-%m-%d_%H-%M-%S')
DB_USER="${POSTGRES_USER:-postgres}"
export PGPASSWORD="${POSTGRES_PASSWORD:?POSTGRES_PASSWORD is not set}"

mkdir -p "$BACKUP_DIR"

DATABASES=$(psql -U "$DB_USER" -d "${POSTGRES_DB:-$DB_USER}" -At -c "SELECT datname FROM pg_database WHERE datistemplate = false;")

for DB in $DATABASES; do
    pg_dump -U "$DB_USER" -d "$DB" | gzip > "$BACKUP_DIR/dump-${DB}-${DATE}.sql.gz"
done

find "$BACKUP_DIR" -type f -name "dump-*.sql.gz" -mtime +"$RETENTION_DAYS" -delete
