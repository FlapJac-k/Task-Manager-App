#!/bin/bash

echo "📄 Setting up .env from container ENV..."

if [ ! -f ".env" ]; then
  cp .env.example .env
fi

sed -i "s/^DB_HOST=.*/DB_HOST=${DB_HOST}/" .env
sed -i "s/^DB_DATABASE=.*/DB_DATABASE=${DB_DATABASE}/" .env
sed -i "s/^DB_USERNAME=.*/DB_USERNAME=${DB_USERNAME}/" .env
sed -i "s/^DB_PASSWORD=.*/DB_PASSWORD=${DB_PASSWORD}/" .env

echo "⏳ Waiting for MySQL to be ready..."

until mysql -h"$DB_HOST" -u"$DB_USERNAME" -p"$DB_PASSWORD" -e 'select 1' &> /dev/null; do
  echo "❌ Failed to connect. Retrying..."
  sleep 2
done

echo "✅ MySQL is up."

if [ ! -d "vendor" ]; then
  echo "📦 Running composer install..."
  composer install --no-interaction --prefer-dist --optimize-autoloader
fi

echo "⚙️ Running Laravel setup..."
php artisan config:clear
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force

echo "🚀 Starting Apache..."
apache2-foreground
