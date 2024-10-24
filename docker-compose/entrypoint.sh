#!/bin/sh
set -ex  # Enable error exit and debugging


# Run Laravel migrations
composer update

php artisan migrate

# Start PHP-FPM or the passed command
exec "$@"
