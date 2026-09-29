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

# Run database migrations if database connection is available
if [ -n "${DB_HOST}" ]; then
    echo "Connecting to MySQL database at ${DB_HOST}:${DB_PORT:-3306}..."
    php artisan migrate --force || true

    # Optional seeding on initial boot
    if [ "${RUN_SEEDER}" = "true" ]; then
        echo "Running initial database seeding..."
        php artisan db:seed --force || true
    fi
fi

# Hand over process execution to Apache foreground runner
echo "Starting Apache Web Server on port ${PORT}..."
exec apache2-foreground
