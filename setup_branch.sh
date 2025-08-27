#!/bin/bash

echo "Running post-checkout setup..."

# Install Composer dependencies
composer install --no-interaction --prefer-dist --optimize-autoloader

# Ensure directories exist (念のため)
mkdir -p bootstrap/cache storage/framework/{sessions,views,cache/data} storage/logs storage/app/public

# Set permissions
# (所有者/グループ名は環境に合わせてください)
chown -R www-data:www-data bootstrap/cache storage
chmod -R 775 bootstrap/cache storage

# Link storage (必要なら)
# php artisan storage:link

# Clear/cache framework items (必要なら)
# php artisan cache:clear
# php artisan config:cache
# php artisan route:cache
# php artisan view:cache

echo "Setup complete."