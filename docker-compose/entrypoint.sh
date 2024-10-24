#!/bin/sh
set -ex  # Enable error exit and debugging

compose install --no-dev --no-interaction --prefer-dist --optimize-autoloader

php artisan migrate:fresh --seed

# Start PHP-FPM or the passed command
exec "$@"
