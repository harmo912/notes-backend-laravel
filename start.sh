#!/bin/bash

# Générer la clé si elle n'existe pas
php artisan key:generate --force

# Lancer les migrations + seeders
php artisan migrate --seed --force

# Vider les caches
php artisan config:clear
php artisan cache:clear

# Démarrer Apache
apache2-foreground