#!/usr/bin/env bash

set -Eeuo pipefail

if [[ "$#" -ne 0 ]]; then
    echo "This command does not accept arguments." >&2
    exit 2
fi

/usr/bin/systemctl restart php8.4-fpm nginx sweetia-reverb
