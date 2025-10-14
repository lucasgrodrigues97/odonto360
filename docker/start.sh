#!/bin/sh

# Set default port if not provided
export PORT=${PORT:-80}

# Create nginx config with proper port substitution
cat > /etc/nginx/conf.d/default.conf << EOF
server {
    listen ${PORT};
    server_name _;
    root /var/www/html/public;
    index index.php index.html;

    # Security
    server_tokens off;
    
    # Client max body size
    client_max_body_size 64M;

    # Main location
    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    # PHP handling
    location ~ \\.php\$ {
        try_files \$uri =404;
        fastcgi_split_path_info ^(.+\\.php)(/.+)\$;
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        fastcgi_param PATH_INFO \$fastcgi_path_info;
        include fastcgi_params;
        
        # FastCGI settings
        fastcgi_connect_timeout 60s;
        fastcgi_send_timeout 60s;
        fastcgi_read_timeout 60s;
        fastcgi_buffer_size 128k;
        fastcgi_buffers 4 256k;
        fastcgi_busy_buffers_size 256k;
        
        # FastCGI cache (disabled to avoid errors)
        # fastcgi_cache app_cache;
        # fastcgi_cache_valid 60m;
    }

    # API rate limiting
    location /api/ {
        limit_req zone=api burst=20 nodelay;
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    # Login rate limiting
    location /api/login {
        limit_req zone=login burst=5 nodelay;
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    # Static files
    location ~* \\.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)\$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
    }

    # Deny access to hidden files
    location ~ /\\. {
        deny all;
        access_log off;
        log_not_found off;
    }

    # Deny access to sensitive files
    location ~* \\.(env|log|htaccess|htpasswd|ini|sh|sql|conf)\$ {
        deny all;
        access_log off;
        log_not_found off;
    }

    # Health check
    location /health {
        access_log off;
        return 200 "healthy\\n";
        add_header Content-Type text/plain;
    }

    # API health check
    location /api/health {
        access_log off;
        try_files \$uri \$uri/ /index.php?\$query_string;
    }
}
EOF

# Test if PHP-FPM is available
echo "Testing PHP-FPM connection..."
timeout 10 sh -c 'until nc -z 127.0.0.1 9000; do sleep 1; done' || echo "PHP-FPM not ready, continuing..."

# Create necessary directories
mkdir -p /var/www/html/public
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/bootstrap/cache

# Fix permissions for Laravel
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache

# Clear and recreate Laravel cache
echo "Clearing Laravel cache..."
cd /var/www/html
php artisan config:clear || echo "Config clear failed"
php artisan cache:clear || echo "Cache clear failed"
php artisan view:clear || echo "View clear failed"
php artisan route:clear || echo "Route clear failed"

# Check .env file
echo "Checking .env file..."
if [ ! -f /var/www/html/.env ]; then
    echo "Creating .env file..."
    cp /var/www/html/.env.example /var/www/html/.env
    php artisan key:generate --no-interaction || echo "Key generate failed"
fi

# Recreate cache
echo "Recreating Laravel cache..."
php artisan config:cache || echo "Config cache failed"
php artisan route:cache || echo "Route cache failed"
php artisan view:cache || echo "View cache failed"

# Check if Laravel index.php exists
if [ ! -f /var/www/html/public/index.php ]; then
    echo "Laravel index.php not found, checking Laravel installation..."
    # Check if Laravel is installed
    if [ -f /var/www/html/artisan ]; then
        echo "Laravel found, but public/index.php missing. Creating it..."
        # Create a proper Laravel index.php
        cat > /var/www/html/public/index.php << 'EOF'
<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Check If The Application Is Under Maintenance
|--------------------------------------------------------------------------
|
| If the application is in maintenance / demo mode via the "down" command
| we will load this file so that any pre-rendered content can be shown
| instead of starting the framework, which could cause an exception.
|
*/

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| this application. We just need to utilize it! We'll simply require it
| into the script here so we don't need to manually load our classes.
|
*/

require __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request using
| the application's HTTP kernel. Then, we will send the response back
| to this client's browser, allowing them to enjoy our application.
|
*/

$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
EOF
    else
        echo "Laravel not found, creating basic index.php..."
        echo "<?php echo 'Laravel not properly installed'; ?>" > /var/www/html/public/index.php
    fi
fi

# Create a simple test file
echo "<?php echo 'PHP is working!'; ?>" > /var/www/html/public/test.php

# Start supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
