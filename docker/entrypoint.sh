#!/bin/bash
set -e

# Install PHP dependencies
composer install

# Check if .env exists, if not copy from example
if [ ! -f ".env" ]; then
    echo "Creating .env file..."
    cp .env.example .env
fi

# Wait for DB to be ready
echo "Waiting for database connection..."
# Sourcing .env again just in case, though docker-compose should handle it
if [ -f .env ]; then
    export $(grep -v '^#' .env | xargs)
fi

until php -r "try { new PDO('mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE'), getenv('DB_USERNAME'), getenv('DB_PASSWORD')); exit(0); } catch (Exception \$e) { exit(1); }"; do
    echo "Database is unavailable or credentials missing - sleeping"
    sleep 2
done
echo "Database is up!"

# Run Migrations
echo "Running migrations..."
php artisan migrate --force

# Create Swagger documentation
echo "Generating Swagger documentation..."
php artisan l5-swagger:generate

echo "Container is ready."

# Execute the main command (php-fpm)
exec "$@"
