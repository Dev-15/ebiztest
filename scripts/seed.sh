#!/bin/bash
docker exec -it laravel_app php artisan db:seed --class=MassDataSeeder
