#!/bin/sh
set -e

cd /var/www/html

# Ensure required directories exist (they may be missing in a fresh container)
mkdir -p storage/framework/cache/data \
         storage/framework/sessions \
         storage/framework/views \
         storage/logs \
         storage/app/public \
         storage/app/public/livewire-tmp \
         bootstrap/cache

chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# nginx on Alpine creates its tmp dirs owned by "nginx" user, but our nginx.conf
# runs workers as "www-data" — fix so uploads can write body buffers.
chown -R www-data:www-data /var/lib/nginx /var/log/nginx 2>/dev/null || true

# Run artisan commands that need .env (only when APP_KEY is set)
if [ -n "$APP_KEY" ]; then
    echo "Running Laravel setup commands..."

    rm -rf public/storage
    php artisan storage:link --force
    php artisan migrate --force

    # Sync role/permission catalog — idempotent (firstOrCreate for every entry),
    # so safe to run on every deploy. Adds new permissions as the catalog grows.
    php artisan db:seed --class=RolePermissionSeeder --force || true

    # Baseline expense categories for the Accounts module — idempotent
    php artisan db:seed --class=ExpenseCategorySeeder --force || true

    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan event:cache
else
    echo "WARNING: APP_KEY not set — skipping artisan commands."
fi

# Create supervisor log dir
mkdir -p /var/log/supervisor

echo "Starting services..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
