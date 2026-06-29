#!/bin/bash

# Permissions storage
chmod -R 777 /var/www/html/storage
chmod -R 777 /var/www/html/bootstrap/cache

# Migrations + seeders
php artisan migrate --force
php artisan db:seed --force

# Cache
php artisan config:clear
php artisan cache:clear

# Démarrer Apache
apache2-foreground