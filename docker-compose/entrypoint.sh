#!/bin/sh

# composer clear-cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# composer install or update for new added package
# composer install -n --prefer-dist
# Run Laravel migrations
php artisan migrate:fresh

# php artisan db:seed 

# Start the main process
exec "$@"