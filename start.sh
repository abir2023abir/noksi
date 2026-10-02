#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")"

mkdir -p storage/framework/{cache/data,sessions,views} storage/logs bootstrap/cache
chmod -R 775 storage bootstrap/cache || true

# Generate key if missing (Railway should set APP_KEY — this is a fallback)
if [ -z "${APP_KEY:-}" ]; then
  export APP_KEY="$(php -r "echo 'base64:'.base64_encode(random_bytes(32));")"
  echo "Warning: APP_KEY was empty; generated a temporary key for this boot."
fi

php artisan config:clear || true
php artisan storage:link || true

# Wait briefly for DB, then migrate + seed once
php artisan migrate --force --no-interaction
# Seed only if no products yet (safe-ish for first boot)
php -r "
require 'vendor/autoload.php';
\$app = require 'bootstrap/app.php';
\$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
if (App\Models\Product::count() === 0) {
  passthru('php artisan db:seed --force');
}
" || true

php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

PORT="${PORT:-8000}"
exec php artisan serve --host=0.0.0.0 --port="$PORT"
