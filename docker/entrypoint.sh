#!/bin/bash
set -e

# Install PHP dependencies
composer install

# Check if .env exists, if not copy from example
if [ ! -f ".env" ]; then
    echo "Creating .env file..."
    cp .env.example .env
fi

# Generate Application Key if not set
if grep -q "APP_KEY=" .env && [ -z "$(grep "APP_KEY=base64" .env)" ]; then
    echo "Generating application key..."
    php artisan key:generate
fi

# Run Migrations
echo "Running migrations..."
php artisan migrate --force

# Create Swagger documentation
echo "Generating Swagger documentation..."
php artisan l5-swagger:generate

echo "Container is ready."

# Execute the main command (php-fpm)
exec "$@"
