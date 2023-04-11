#!/bin/bash
docker compose up -d --build
docker compose exec php-apache /bin/bash
composer create-project laravel/laravel .
chmod -R 775 storage bootstrap/cache
chmod -R 777 storage/