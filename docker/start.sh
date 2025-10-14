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
        include fastcgi_params;
        
        # FastCGI cache
        fastcgi_cache app_cache;
        fastcgi_cache_valid 60m;
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

# Start supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
