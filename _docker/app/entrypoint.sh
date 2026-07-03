#!/bin/bash
set -e

echo "Waiting for MySQL..."
until php -r "new PDO('mysql:host=${DB_HOST:-mysql};port=${DB_PORT:-3306}', '${DB_USERNAME:-admin}', '${DB_PASSWORD:-secret}');" 2>/dev/null; do
    sleep 2
done
echo "MySQL ready."

php artisan config:cache
php artisan migrate --force

echo "migrate  done."

exec php-fpm
