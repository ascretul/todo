#!/bin/bash

# Clear Laravel cache
php artisan config:clear
php artisan route:clear
php artisan cache:clear

# Start php-fpm
exec "$@"

