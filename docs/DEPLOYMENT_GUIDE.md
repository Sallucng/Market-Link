# MarketLink Backend Production Deployment & LEMP Setup Guide

**Target Environment:** Linux Ubuntu 22.04 / 24.04 LTS (LEMP Stack: Nginx, MySQL 8.0, PHP 8.2+ FPM)  
**Document Classification:** Production Infrastructure Specification  
**Author:** Senior DevOps & Laravel Infrastructure Architect

---

## 1. Environment Preparation & `.env.production.example`

When provisioning a new production server, copy `.env.production.example` to `.env` and generate an immutable application key:

```bash
cp .env.production.example .env
php artisan key:generate --show
```

### Key Security & Performance Directives in `.env`:

```ini
# Application Mode (CRITICAL: Disables detailed debug stack traces on errors)
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.marketlink.com

# Database Connection (MySQL 8.0 with InnoDB Engine & Strict Mode)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=marketlink_production
DB_USERNAME=marketlink_app
DB_PASSWORD=REPLACE_WITH_STRONG_UNGUESSABLE_PASSWORD

# Caching & Session Stores (Redis for sub-millisecond throughput)
CACHE_STORE=redis
SESSION_DRIVER=database
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
SESSION_DOMAIN=.marketlink.com

# Asynchronous Queue Processing
QUEUE_CONNECTION=database

# Public Symlinked File Storage
FILESYSTEM_DISK=public

# Daily Rolling Error Logs (Retained for 14 days)
LOG_CHANNEL=daily
LOG_LEVEL=error
LOG_DAILY_DAYS=14
```

---

## 2. File Storage & Deployment Optimization Command Sequence

In production environments, Laravel must compile and cache its configurations, routes, and views into static PHP arrays to bypass runtime disk reads on every HTTP request.

### 2.1. Complete Sequence of Artisan Deployment Commands

```bash
# 1. Enter Maintenance Mode (Optional secret allows deployment team bypass)
php artisan down --secret="MarketLinkDeploy2026" --render="errors::503"

# 2. Install Production PHP Dependencies (Omits dev packages & optimizes autoloader)
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# 3. Create Public Storage Symlink (Maps storage/app/public -> public/storage)
php artisan storage:link

# 4. Flush Any Stale Development Artifacts
php artisan optimize:clear

# 5. Execute Non-Destructive Database Migrations
php artisan migrate --force

# 6. Warm Up Production Caches
php artisan config:cache    # Compiles config files into bootstrap/cache/config.php
php artisan route:cache     # Compiles routes into a single fast regex map
php artisan view:cache      # Precompiles Blade templates
php artisan event:cache     # Discovers and caches event listeners

# 7. Restart Queue Workers (Picks up new codebase in running daemon processes)
sudo supervisorctl restart marketlink-worker:*

# 8. Reload PHP-FPM (Flushes OPcache in-memory bytecode cache)
sudo systemctl reload php8.2-fpm

# 9. Exit Maintenance Mode
php artisan up
```

### 2.2. Directory Permissions (Linux / Ubuntu)

Ensure the web server user (`www-data`) owns the storage and cache directories:

```bash
sudo chown -R www-data:www-data /var/www/marketlink/storage /var/www/marketlink/bootstrap/cache
sudo chmod -R 775 /var/www/marketlink/storage /var/www/marketlink/bootstrap/cache
```

---

## 3. Web Server Configuration (Nginx Server Block)

Save the following configuration as `/etc/nginx/sites-available/marketlink.conf` and enable it via symlink into `/etc/nginx/sites-enabled/`:

```nginx
# ==============================================================================
# Nginx Server Block Configuration for MarketLink Production
# ==============================================================================

# HTTP to HTTPS Enforcement
server {
    listen 80;
    listen [::]:80;
    server_name api.marketlink.com;

    location /.well-known/acme-challenge/ {
        root /var/www/marketlink/public;
        allow all;
    }

    location / {
        return 301 https://$host$request_uri;
    }
}

# Primary HTTPS Server Block
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name api.marketlink.com;

    # Document Root strictly points to the Laravel public directory
    root /var/www/marketlink/public;
    index index.php index.html;

    charset utf-8;

    # SSL Certificates (Automated via Certbot Let's Encrypt)
    ssl_certificate /etc/letsencrypt/live/api.marketlink.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/api.marketlink.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;
    ssl_session_timeout 1d;
    ssl_session_cache shared:SSL:10m;
    ssl_session_tickets off;

    # HSTS Policy (2 Years preload)
    add_header Strict-Transport-Security "max-age=63072000; includeSubDomains; preload" always;

    # Essential Hardened Security Headers
    add_header X-Frame-Options "DENY" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header Content-Security-Policy "default-src 'self'; img-src 'self' data: https: blob:; font-src 'self' data:;" always;

    # Maximum Upload Size for High-Resolution Product Images
    client_max_body_size 10M;

    # Logging Paths
    access_log /var/log/nginx/marketlink_access.log combined buffer=512k flush=1m;
    error_log /var/log/nginx/marketlink_error.log warn;

    # Gzip Compression Optimization
    gzip on;
    gzip_vary on;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_types text/plain text/css text/xml application/json application/javascript image/svg+xml;

    # Laravel Routing
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Static Product Media & Static Assets
    location /storage/ {
        alias /var/www/marketlink/storage/app/public/;
        expires 30d;
        add_header Cache-Control "public, no-transform, immutable";
        access_log off;
        log_not_found off;

        # SECURITY GUARD: Prevent arbitrary script execution in upload directory
        location ~ \.php$ {
            deny all;
        }
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    # FastCGI PHP 8.2 FPM Process Manager
    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;

        fastcgi_buffers 16 16k;
        fastcgi_buffer_size 32k;
        fastcgi_read_timeout 60;
        fastcgi_hide_header X-Powered-By;
    }

    # Deny access to sensitive hidden files (.env, .git)
    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## 4. Database & Performance Optimization (MySQL 8.0)

MarketLink's order processing and product catalogs rely heavily on indexed range scans, state machine updates, and group-by sales aggregates. Configure `/etc/mysql/mysql.conf.d/marketlink.cnf`:

### 4.1. Core Engine Tuning (`my.cnf`)

```ini
[mysqld]
default_storage_engine          = InnoDB
character-set-server            = utf8mb4
collation-server                = utf8mb4_unicode_ci

# Buffer Pool (Dedicate 60-70% of server RAM on dedicated database instances)
innodb_buffer_pool_size         = 4G
innodb_buffer_pool_instances     = 4
innodb_log_file_size            = 512M
innodb_log_buffer_size          = 64M

# Financial ACID Guarantee (1 = Flush to disk on every transaction commit)
innodb_flush_log_at_trx_commit  = 1
innodb_flush_method             = O_DIRECT

# Connection Pool Tuning
max_connections                 = 500
thread_cache_size               = 128
table_open_cache                = 4096

# Memory Allocation for Aggregations (Prevents on-disk temp tables for dashboard metrics)
tmp_table_size                  = 128M
max_heap_table_size             = 128M

# Slow Query Diagnostics
slow_query_log                  = 1
slow_query_log_file             = /var/log/mysql/marketlink_slow.log
long_query_time                 = 1.0
```

### 4.2. Indexing Strategy Review for Orders & Products

- **Orders Composite Queries:** The index `(farmer_profile_id, status)` prevents table scans when the farmer orders API queries incoming orders (`where farmer_profile_id = ? and status = ?`).
- **Pessimistic Locking Verification:** `Product::where('id', $id)->lockForUpdate()` executes as an exclusive row lock on the Primary Key `id` without causing table-level lock escalation.

---

## 5. Asynchronous Queue Worker Setup (Supervisor)

Background notifications, order status change emails, and platform broadcast messages are queued asynchronously so API response latencies stay under **50 milliseconds**.

### 5.1. Supervisor Configuration

Create `/etc/supervisor/conf.d/marketlink-worker.conf`:

```ini
[program:marketlink-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/marketlink/artisan queue:work database --sleep=3 --tries=3 --max-time=3600 --timeout=90
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=4
redirect_stderr=true
stdout_logfile=/var/www/marketlink/storage/logs/worker.log
stdout_logfile_maxbytes=20MB
stdout_logfile_backups=5
stopwaitsecs=3600
```

### 5.2. Activate Workers

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start marketlink-worker:*
```

---

## 6. Zero-Downtime Deployment Script (`deploy.sh`)

An automated bash deployment script is located at [deployment/deploy.sh](file:///c:/Users/PC/OneDrive/Desktop/techwiz/MarketLink/deployment/deploy.sh). Run it from the server terminal:

```bash
chmod +x deployment/deploy.sh
./deployment/deploy.sh
```

This single command handles repository fetching, composer installation, zero-downtime database migrations, cache re-compilation, worker recycling, and OPcache flushing seamlessly.
