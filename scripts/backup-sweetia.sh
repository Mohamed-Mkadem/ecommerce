#!/usr/bin/env bash

set -Eeuo pipefail

APP_DIR="${SWEETIA_APP_DIR:-/var/www/sweetia}"
BACKUP_DIR="${SWEETIA_BACKUP_DIR:-/var/backups/sweetia}"
MYSQL_DEFAULTS_FILE="${SWEETIA_MYSQL_DEFAULTS_FILE:-/home/deploy/.my.cnf}"
RETENTION_DAYS="${SWEETIA_BACKUP_RETENTION_DAYS:-14}"
OFFSITE_REMOTE="${SWEETIA_OFFSITE_REMOTE:-sweetia-offsite:}"
TIMESTAMP="$(date -u +%Y%m%dT%H%M%SZ)"

if [[ ! -d "$APP_DIR/storage/app/public" ]]; then
    echo "Uploaded files directory not found: $APP_DIR/storage/app/public" >&2
    exit 1
fi

if [[ ! -r "$MYSQL_DEFAULTS_FILE" ]]; then
    echo "MySQL client credentials file is missing or unreadable: $MYSQL_DEFAULTS_FILE" >&2
    exit 1
fi

if ! [[ "$RETENTION_DAYS" =~ ^[1-9][0-9]*$ ]]; then
    echo "Backup retention must be a positive whole number of days." >&2
    exit 1
fi

if ! command -v rclone >/dev/null 2>&1; then
    echo "rclone is required to upload offsite backups." >&2
    exit 1
fi

umask 077
mkdir -p "$BACKUP_DIR"
TEMP_DIR="$(mktemp -d "$BACKUP_DIR/.sweetia-backup.XXXXXX")"
cleanup() {
    rm -rf -- "$TEMP_DIR"
}
trap cleanup EXIT

DB_BACKUP="$TEMP_DIR/sweetia-database-$TIMESTAMP.sql.gz"
FILES_BACKUP="$TEMP_DIR/sweetia-uploads-$TIMESTAMP.tar.gz"

mysqldump \
    --defaults-extra-file="$MYSQL_DEFAULTS_FILE" \
    --no-tablespaces \
    --single-transaction \
    --host=127.0.0.1 \
    sweetia \
    | gzip -c > "$DB_BACKUP"

tar -C "$APP_DIR/storage/app" -czf "$FILES_BACKUP" public
gzip -t "$DB_BACKUP"
tar -tzf "$FILES_BACKUP" >/dev/null

install -m 600 "$DB_BACKUP" "$BACKUP_DIR/$(basename "$DB_BACKUP")"
install -m 600 "$FILES_BACKUP" "$BACKUP_DIR/$(basename "$FILES_BACKUP")"

rclone copyto \
    "$BACKUP_DIR/$(basename "$DB_BACKUP")" \
    "$OFFSITE_REMOTE/$(basename "$DB_BACKUP")"
rclone copyto \
    "$BACKUP_DIR/$(basename "$FILES_BACKUP")" \
    "$OFFSITE_REMOTE/$(basename "$FILES_BACKUP")"

find "$BACKUP_DIR" \
    -maxdepth 1 \
    -type f \
    \( -name 'sweetia-database-*.sql.gz' -o -name 'sweetia-uploads-*.tar.gz' \) \
    -mtime "+$RETENTION_DAYS" \
    -delete

rclone delete "$OFFSITE_REMOTE" --min-age "${RETENTION_DAYS}d"

echo "Sweetia database and uploads backed up locally and offsite at $TIMESTAMP (UTC)."
