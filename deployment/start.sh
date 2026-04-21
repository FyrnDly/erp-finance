#!/bin/bash
set -e

# Link storage and generate new key
php artisan key:generate || true
php artisan storage:link || true

# Cache config
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec /usr/bin/supervisord -c /etc/supervisor/supervisord.conf