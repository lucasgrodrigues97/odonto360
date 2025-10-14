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

# Create public directory if it doesn't exist
mkdir -p /var/www/html/public

# Check if Laravel index.php exists
if [ ! -f /var/www/html/public/index.php ]; then
    echo "Laravel index.php not found, creating basic one..."
    echo "<?php echo 'Laravel not properly installed'; ?>" > /var/www/html/public/index.php
fi

# Create a simple test file
echo "<?php echo 'PHP is working!'; ?>" > /var/www/html/public/test.php

# Start supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
