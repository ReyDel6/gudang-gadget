#!/bin/sh
set -e

# nginx listen sesuai PORT dari Render
mkdir -p /etc/nginx/conf.d
envsubst '${PORT}' < /etc/nginx/conf.d/default.conf.template > /etc/nginx/conf.d/default.conf

cd /var/www/html

# storage link untuk upload foto
php artisan storage:link 2>/dev/null || true

# migrasi DB (idempotent)
if [ -n "$DB_HOST" ]; then
  php artisan migrate --force 2>&1 || echo "migrate gagal - cek kredensial DB"
fi

php artisan config:cache 2>/dev/null || true
php artisan route:cache 2>/dev/null || true
php artisan view:cache 2>/dev/null || true

exec supervisord -c /etc/supervisord.conf