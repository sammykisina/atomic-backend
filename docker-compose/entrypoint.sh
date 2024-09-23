#!/bin/sh
set -ex  # Enable error exit and debugging

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# Run Composer tasks if necessary
# composer clear-cache
# composer install -n --prefer-dist

# Run Laravel migrations
php artisan migrate

# Start PHP-FPM or the passed command
exec "$@"
