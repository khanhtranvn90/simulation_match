#!/bin/sh
set -e

dockerize -wait tcp://$DB_HOST:$DB_PORT -timeout 60s

echo "DB is ready, running migrations and seeders..."
php artisan migrate --force
php artisan db:seed --class=TeamSeeder --force
php artisan db:seed --class=StandingSeeder --force

echo "Starting Laravel server..."
php artisan serve --host=0.0.0.0 --port=8000 &
exec "$@"
