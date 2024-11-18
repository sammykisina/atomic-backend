#!/bin/sh
set -ex  # Enable error exit and debugging

# Install PHP dependencies
composer install

# Run database migrations and seed
php artisan migrate

# Start PHP-FPM or execute the passed command
exec "$@"
