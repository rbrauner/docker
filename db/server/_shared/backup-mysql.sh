#!/usr/bin/env bash
# Dump every non-system database to /backup (MySQL, Percona, MariaDB).
# Mounted as /bin/backup.sh; credentials come from the container env.
set -euo pipefail

BACKUP_DIR="/backup"
RETENTION_DAYS="${BACKUP_RETENTION_DAYS:-7}"
DATE=$(date '+%Y-%m-%d_%H-%M-%S')
DB_USER="root"
DB_PASS="${MARIADB_ROOT_PASSWORD:-${MYSQL_ROOT_PASSWORD:?MYSQL_ROOT_PASSWORD is not set}}"

# MariaDB 11+ ships only mariadb/mariadb-dump binaries
if command -v mariadb > /dev/null; then
    CLIENT="mariadb"
    DUMP="mariadb-dump"
else
    CLIENT="mysql"
    DUMP="mysqldump"
fi

mkdir -p "$BACKUP_DIR"

DATABASES=$("$CLIENT" -u"$DB_USER" -p"$DB_PASS" -N -e "SHOW DATABASES;" | grep -Ev "^(information_schema|performance_schema|mysql|sys)$")

for DB in $DATABASES; do
    "$DUMP" -u"$DB_USER" -p"$DB_PASS" --databases "$DB" | gzip > "$BACKUP_DIR/dump-${DB}-${DATE}.sql.gz"
done

find "$BACKUP_DIR" -type f -name "dump-*.sql.gz" -mtime +"$RETENTION_DAYS" -delete
