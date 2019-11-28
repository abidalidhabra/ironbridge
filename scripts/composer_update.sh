#!/bin/bash

docker exec -it irinbridge bash -c "php artisan view:clear"
docker exec -it irinbridge bash -c "php artisan route:clear"
docker exec -it irinbridge bash -c "php artisan key:generate"
docker exec -it irinbridge bash -c "php artisan cache:clear"
docker exec -it irinbridge bash -c "php artisan config:cache"
docker exec -it irinbridge bash -c "chown -R www-data:www-data /var/www/html"