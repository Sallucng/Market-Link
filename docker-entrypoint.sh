#!/bin/bash
set -e

# Adapt Apache configuration to dynamic Render $PORT
PORT="${PORT:-80}"
echo "Configuring Apache to listen on port ${PORT}..."
sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf 2>/dev/null || true
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:${PORT}>/g" /etc/apache2/sites-available/*.conf 2>/dev/null || true

# If a secret .env file exists (Render Secret Files feature), copy it
if [ -f "/etc/secrets/.env" ]; then
    echo "Applying /etc/secrets/.env to .env..."
    cp /etc/secrets/.env .env
fi

# Ensure .env exists
if [ ! -f ".env" ]; then
    echo "Creating .env from .env.example..."
    if [ -f ".env.example" ]; then
        cp .env.example .env
    else
        touch .env
    fi
fi

# Ensure storage, cache, and database directories exist
mkdir -p storage/framework/sessions \
         storage/framework/views \
         storage/framework/cache/data \
         storage/logs \
         bootstrap/cache \
         database

# Ensure database.sqlite exists
touch database/database.sqlite

# If DB_HOST is empty or unset, or DB_CONNECTION is sqlite, enforce SQLite configuration
if [ -z "${DB_HOST}" ] || [ "${DB_CONNECTION}" = "sqlite" ] || [ "${DB_CONNECTION}" = "" ]; then
    echo "Enforcing SQLite database driver..."
    export DB_CONNECTION=sqlite
    export DB_DATABASE=/var/www/html/database/database.sqlite

    if grep -q "^DB_CONNECTION=" .env 2>/dev/null; then
        sed -i 's/^DB_CONNECTION=.*/DB_CONNECTION=sqlite/' .env
    else
        echo "DB_CONNECTION=sqlite" >> .env
    fi

    if grep -q "^DB_DATABASE=" .env 2>/dev/null; then
        sed -i 's|^DB_DATABASE=.*|DB_DATABASE=/var/www/html/database/database.sqlite|' .env
    else
        echo "DB_DATABASE=/var/www/html/database/database.sqlite" >> .env
    fi
fi

# Pass environment variables to Apache mod_php
cat <<EOF > /etc/apache2/conf-available/marketlink-env.conf
SetEnv DB_CONNECTION ${DB_CONNECTION:-sqlite}
SetEnv DB_DATABASE ${DB_DATABASE:-/var/www/html/database/database.sqlite}
SetEnv APP_ENV ${APP_ENV:-production}
SetEnv APP_NAME "MarketLink"
SetEnv LOG_CHANNEL stderr
EOF
a2enconf marketlink-env 2>/dev/null || true

# Set initial permissions so artisan and web server can read/write without friction
chown -R www-data:www-data storage bootstrap/cache database .env 2>/dev/null || true
chmod -R 777 storage bootstrap/cache database
chmod 666 database/database.sqlite

# Generate application key if missing
if [ -z "${APP_KEY}" ]; then
    if ! grep -q "^APP_KEY=base64:" .env 2>/dev/null; then
        echo "Generating fresh Laravel APP_KEY..."
        php artisan key:generate --force || true
    fi
else
    # Keep provided APP_KEY in .env
    if grep -q "^APP_KEY=" .env 2>/dev/null; then
        sed -i "s|^APP_KEY=.*|APP_KEY=${APP_KEY}|" .env
    else
        echo "APP_KEY=${APP_KEY}" >> .env
    fi
fi

# Clear any cached configurations
php artisan config:clear || true
php artisan cache:clear || true
php artisan route:clear || true
php artisan view:clear || true

# Storage symlink
php artisan storage:link --force || true

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force || echo "Warning: Migration command encountered an issue."

# Seed database if 0 users or RUN_SEEDER=true
USER_COUNT=$(php artisan tinker --execute="echo App\Models\User::count();" 2>/dev/null || echo "0")
if [ "$USER_COUNT" = "0" ] || [ "${RUN_SEEDER}" = "true" ]; then
    echo "Seeding database with default records..."
    php artisan db:seed --force || echo "Warning: Database seeding encountered an issue."
fi

# Cache for production
if [ "${APP_ENV}" = "production" ]; then
    echo "Caching configuration, routes, and views..."
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

# Ensure final permissions
chown -R www-data:www-data storage bootstrap/cache database
chmod -R 777 storage bootstrap/cache database
chmod 666 database/database.sqlite

echo "Starting Apache Web Server on port ${PORT}..."
exec apache2-foreground
