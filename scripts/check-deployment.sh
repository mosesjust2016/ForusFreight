#!/usr/bin/env bash
set -euo pipefail

echo "Checking Laravel deployment prerequisites..."
command -v php >/dev/null || { echo "PHP is required" >&2; exit 1; }
php artisan --version

if [[ "${APP_ENV:-}" == "production" && "${APP_DEBUG:-false}" == "true" ]]; then
    echo "APP_DEBUG=true is not allowed in production." >&2
    exit 1
fi

php artisan route:list --no-ansi >/dev/null
php artisan test --no-ansi
echo "Deployment checks passed."
