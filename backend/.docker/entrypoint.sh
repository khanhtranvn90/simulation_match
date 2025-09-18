#!/bin/sh
set -e

echo "Running migrations and seeders..."
php artisan migrate --force

php artisan db:seed --class=TeamSeeder --force
php artisan db:seed --class=StandingSeeder --force

php artisan serve --host=0.0.0.0 --port=8000 &
exec "$@"
