#!/bin/sh
set -ex  # Enable error exit and debugging

# Ensure all Composer packages are installed
composer install

# Run database migrations and seed the database
php artisan migrate:fresh --seed

# Start PHP-FPM or any passed command
exec "$@"
