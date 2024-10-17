#!/bin/sh
set -ex  # Enable error exit and debugging


# Run Laravel migrations
php artisan migrate

# Start PHP-FPM or the passed command
exec "$@"
