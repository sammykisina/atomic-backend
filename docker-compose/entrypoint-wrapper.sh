#!/bin/sh

# Run migrations
php artisan migrate --force

# Clear application cache
php artisan cache:clear
php artisan config:cache
php artisan route:cache

# Start PHP-FPM
exec php-fpm
