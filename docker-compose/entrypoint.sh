#!/bin/sh
set -ex  # Enable error exit and debugging

php artisan migrate

# Start PHP-FPM or the passed command
exec "$@"
