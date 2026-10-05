#!/bin/bash
set -e

# If a secret .env file exists (Render Secret Files feature), copy and load it
if [ -f "/etc/secrets/.env" ]; then
    echo "Applying /etc/secrets/.env to .env..."
    cp /etc/secrets/.env .env
    set -a
    . ./.env 2>/dev/null || true
    set +a
fi

# Adapt Apache configuration to dynamic Render $PORT
PORT="${PORT:-80}"
echo "Configuring Apache to listen on port ${PORT}..."
sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf 2>/dev/null || true
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:${PORT}>/g" /etc/apache2/sites-available/*.conf 2>/dev/null || true

# Ensure framework storage directories exist with correct permissions
mkdir -p storage/framework/sessions \
         storage/framework/views \
         storage/framework/cache \
         storage/logs \
         bootstrap/cache

chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Generate storage symlink if missing
php artisan storage:link --force || true

# Production configuration caching
if [ "${APP_ENV}" = "production" ]; then
    echo "Caching Laravel configuration and routes for production..."
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

# Prepare SQLite database if using SQLite or default
if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ] || [ -z "${DB_HOST}" ]; then
    echo "Preparing SQLite database at database/database.sqlite..."
    mkdir -p database
    if [ ! -f "database/database.sqlite" ]; then
        touch database/database.sqlite
    fi
    chown -R www-data:www-data database
    chmod -R 775 database
    chmod 664 database/database.sqlite 2>/dev/null || true
fi

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force || true

# Auto-seed database if empty or if requested
USER_COUNT=$(php artisan tinker --execute="echo App\Models\User::count();" 2>/dev/null || echo "0")
if [ "$USER_COUNT" = "0" ] || [ "${RUN_SEEDER}" = "true" ]; then
    echo "Database has 0 users or RUN_SEEDER requested. Running initial database seeding..."
    php artisan db:seed --force || true
fi

# Hand over process execution to Apache foreground runner
echo "Starting Apache Web Server on port ${PORT}..."
exec apache2-foreground
