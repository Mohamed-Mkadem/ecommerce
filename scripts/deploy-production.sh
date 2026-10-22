#!/usr/bin/env bash

set -Eeuo pipefail

APP_DIR="${SWEETIA_APP_DIR:-/var/www/sweetia}"
PHP_BIN="${SWEETIA_PHP_BIN:-/usr/bin/php8.4}"
COMPOSER_BIN="${SWEETIA_COMPOSER_BIN:-/usr/bin/composer}"
ROLLUP_LINUX_PACKAGE="@rollup/rollup-linux-x64-gnu"

if [[ "$(id -un)" != deploy ]]; then
    echo "Run this deployment script as the deploy user." >&2
    exit 1
fi

cd "$APP_DIR"

run_as_deploy() {
    "$@"
}

run_as_deploy bash ./scripts/backup-sweetia.sh
run_as_deploy "$PHP_BIN" "$COMPOSER_BIN" install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction

run_as_deploy npm ci --include=optional

# npm can omit Rollup's Linux optional package from a lockfile generated on Windows.
# Install the exact version requested by the locked Rollup package before building.
ROLLUP_VERSION="$(run_as_deploy node -e "process.stdout.write(require('./node_modules/rollup/package.json').optionalDependencies['${ROLLUP_LINUX_PACKAGE}'])")"
if [[ -z "$ROLLUP_VERSION" ]]; then
    echo "Could not determine the Linux Rollup package version." >&2
    exit 1
fi
run_as_deploy npm install --no-save --package-lock=false "${ROLLUP_LINUX_PACKAGE}@${ROLLUP_VERSION}"
run_as_deploy npm run build

run_as_deploy "$PHP_BIN" artisan down --retry=60
maintenance_mode=true
restore_site() {
    if [[ "${maintenance_mode:-false}" == true ]]; then
        run_as_deploy "$PHP_BIN" artisan up || true
    fi
}
trap restore_site EXIT

run_as_deploy "$PHP_BIN" artisan migrate --force
run_as_deploy "$PHP_BIN" artisan optimize:clear
run_as_deploy "$PHP_BIN" artisan config:cache
run_as_deploy "$PHP_BIN" artisan view:cache
run_as_deploy "$PHP_BIN" artisan queue:restart

run_as_deploy "$PHP_BIN" artisan up
maintenance_mode=false
trap - EXIT

sudo -n /usr/local/sbin/sweetia-restart-services

echo "Sweetia production deployment completed."
