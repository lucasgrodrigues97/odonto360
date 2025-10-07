#!/bin/bash

# User data script for Odonto360 EC2 instances

# Update system
yum update -y

# Install required packages
yum install -y \
    httpd \
    php81 \
    php81-cli \
    php81-common \
    php81-curl \
    php81-gd \
    php81-intl \
    php81-mbstring \
    php81-mysqlnd \
    php81-opcache \
    php81-pdo \
    php81-xml \
    php81-zip \
    php81-bcmath \
    php81-json \
    php81-tokenizer \
    php81-fileinfo \
    php81-dom \
    php81-simplexml \
    php81-xmlreader \
    php81-xmlwriter \
    php81-iconv \
    php81-session \
    php81-openssl \
    php81-readline \
    php81-libxml \
    php81-zlib \
    mysql \
    git \
    unzip \
    wget \
    curl \
    htop \
    vim

# Install Composer
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
chmod +x /usr/local/bin/composer

# Install Node.js and npm
curl -fsSL https://rpm.nodesource.com/setup_18.x | bash -
yum install -y nodejs

# Start and enable Apache
systemctl start httpd
systemctl enable httpd

# Configure Apache
cat > /etc/httpd/conf.d/odonto360.conf << 'EOF'
<VirtualHost *:80>
    DocumentRoot /var/www/html/odonto360/public
    ServerName ${app_url}
    
    <Directory /var/www/html/odonto360/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog /var/log/httpd/odonto360_error.log
    CustomLog /var/log/httpd/odonto360_access.log combined
</VirtualHost>
EOF

# Create application directory
mkdir -p /var/www/html/odonto360
cd /var/www/html/odonto360

# Clone the application (in production, this would be from a private repo)
# For now, we'll create a placeholder
echo "<?php echo 'Odonto360 Application'; ?>" > public/index.php

# Set proper permissions
chown -R apache:apache /var/www/html/odonto360
chmod -R 755 /var/www/html/odonto360

# Configure PHP
cat > /etc/php-8.1.d/99-odonto360.ini << 'EOF'
memory_limit = 256M
upload_max_filesize = 64M
post_max_size = 64M
max_execution_time = 300
max_input_time = 300
date.timezone = America/Sao_Paulo
session.gc_maxlifetime = 1440
opcache.enable = 1
opcache.memory_consumption = 128
opcache.interned_strings_buffer = 8
opcache.max_accelerated_files = 4000
opcache.revalidate_freq = 2
opcache.fast_shutdown = 1
EOF

# Create environment file
cat > /var/www/html/odonto360/.env << EOF
APP_NAME="Odonto360"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://${app_url}

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=${db_host}
DB_PORT=3306
DB_DATABASE=${db_name}
DB_USERNAME=${db_username}
DB_PASSWORD=${db_password}

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="\${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_PUSHER_APP_KEY="\${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="\${PUSHER_HOST}"
VITE_PUSHER_PORT="\${PUSHER_PORT}"
VITE_PUSHER_SCHEME="\${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="\${PUSHER_APP_CLUSTER}"

GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=http://${app_url}/api/auth/google/callback

OPENAI_API_KEY=

TWILIO_SID=
TWILIO_TOKEN=
TWILIO_WHATSAPP_NUMBER=

JWT_SECRET=
JWT_ALGO=HS256
EOF

# Set proper permissions for .env file
chown apache:apache /var/www/html/odonto360/.env
chmod 600 /var/www/html/odonto360/.env

# Create health check endpoint
cat > /var/www/html/odonto360/public/health.php << 'EOF'
<?php
header('Content-Type: application/json');
echo json_encode([
    'status' => 'ok',
    'timestamp' => date('c'),
    'service' => 'Odonto360 API',
    'version' => '1.0.0'
]);
EOF

# Create a simple index page
cat > /var/www/html/odonto360/public/index.php << 'EOF'
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Odonto360 - Sistema de Agendamento Odontológico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid vh-100 d-flex align-items-center justify-content-center">
        <div class="text-center">
            <i class="fas fa-tooth fa-5x text-primary mb-4"></i>
            <h1 class="display-4 fw-bold text-primary mb-3">Odonto360</h1>
            <p class="lead text-muted mb-4">Sistema de Agendamento Odontológico</p>
            <div class="alert alert-success" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                Aplicação instalada com sucesso!
            </div>
            <p class="text-muted">
                <small>
                    Instância: <?php echo gethostname(); ?><br>
                    Data: <?php echo date('d/m/Y H:i:s'); ?>
                </small>
            </p>
        </div>
    </div>
</body>
</html>
EOF

# Restart Apache
systemctl restart httpd

# Create log rotation for application logs
cat > /etc/logrotate.d/odonto360 << 'EOF'
/var/log/httpd/odonto360_*.log {
    daily
    missingok
    rotate 30
    compress
    delaycompress
    notifempty
    create 644 apache apache
    postrotate
        /bin/systemctl reload httpd > /dev/null 2>&1 || true
    endscript
}
EOF

# Install CloudWatch agent
wget https://s3.amazonaws.com/amazoncloudwatch-agent/amazon_linux/amd64/latest/amazon-cloudwatch-agent.rpm
rpm -U ./amazon-cloudwatch-agent.rpm

# Configure CloudWatch agent
cat > /opt/aws/amazon-cloudwatch-agent/etc/amazon-cloudwatch-agent.json << 'EOF'
{
    "logs": {
        "logs_collected": {
            "files": {
                "collect_list": [
                    {
                        "file_path": "/var/log/httpd/odonto360_*.log",
                        "log_group_name": "/aws/ec2/odonto360",
                        "log_stream_name": "{instance_id}-apache"
                    },
                    {
                        "file_path": "/var/log/messages",
                        "log_group_name": "/aws/ec2/odonto360",
                        "log_stream_name": "{instance_id}-system"
                    }
                ]
            }
        }
    },
    "metrics": {
        "namespace": "Odonto360/EC2",
        "metrics_collected": {
            "cpu": {
                "measurement": [
                    "cpu_usage_idle",
                    "cpu_usage_iowait",
                    "cpu_usage_user",
                    "cpu_usage_system"
                ],
                "metrics_collection_interval": 60
            },
            "disk": {
                "measurement": [
                    "used_percent"
                ],
                "metrics_collection_interval": 60,
                "resources": [
                    "*"
                ]
            },
            "mem": {
                "measurement": [
                    "mem_used_percent"
                ],
                "metrics_collection_interval": 60
            }
        }
    }
}
EOF

# Start CloudWatch agent
/opt/aws/amazon-cloudwatch-agent/bin/amazon-cloudwatch-agent-ctl \
    -a fetch-config \
    -m ec2 \
    -c file:/opt/aws/amazon-cloudwatch-agent/etc/amazon-cloudwatch-agent.json \
    -s

# Create a simple deployment script
cat > /var/www/html/odonto360/deploy.sh << 'EOF'
#!/bin/bash
# Deployment script for Odonto360

echo "Starting deployment..."

# Pull latest code (in production, this would be from a private repo)
# git pull origin main

# Install/update dependencies
# composer install --no-dev --optimize-autoloader
# npm ci
# npm run build

# Run migrations
# php artisan migrate --force

# Clear caches
# php artisan config:cache
# php artisan route:cache
# php artisan view:cache

# Restart services
systemctl restart httpd

echo "Deployment completed successfully!"
EOF

chmod +x /var/www/html/odonto360/deploy.sh

# Create a status check script
cat > /var/www/html/odonto360/status.sh << 'EOF'
#!/bin/bash
# Status check script for Odonto360

echo "=== Odonto360 Status Check ==="
echo "Date: $(date)"
echo "Hostname: $(hostname)"
echo "Uptime: $(uptime)"
echo ""

echo "=== Services Status ==="
systemctl status httpd --no-pager -l
echo ""

echo "=== Disk Usage ==="
df -h
echo ""

echo "=== Memory Usage ==="
free -h
echo ""

echo "=== Load Average ==="
cat /proc/loadavg
echo ""

echo "=== Network Connections ==="
netstat -tuln | grep :80
echo ""

echo "=== Application Files ==="
ls -la /var/www/html/odonto360/
echo ""

echo "=== Environment Check ==="
if [ -f /var/www/html/odonto360/.env ]; then
    echo "Environment file exists"
    echo "Database host: $(grep DB_HOST /var/www/html/odonto360/.env | cut -d'=' -f2)"
else
    echo "Environment file not found"
fi
EOF

chmod +x /var/www/html/odonto360/status.sh

# Create a backup script
cat > /var/www/html/odonto360/backup.sh << 'EOF'
#!/bin/bash
# Backup script for Odonto360

BACKUP_DIR="/var/backups/odonto360"
DATE=$(date +%Y%m%d_%H%M%S)

mkdir -p $BACKUP_DIR

# Backup application files
tar -czf $BACKUP_DIR/app_$DATE.tar.gz -C /var/www/html odonto360

# Backup database (if accessible)
# mysqldump -h ${db_host} -u ${db_username} -p${db_password} ${db_name} > $BACKUP_DIR/db_$DATE.sql

# Keep only last 7 days of backups
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete

echo "Backup completed: $BACKUP_DIR/app_$DATE.tar.gz"
EOF

chmod +x /var/www/html/odonto360/backup.sh

# Set up cron jobs
cat > /etc/cron.d/odonto360 << 'EOF'
# Odonto360 cron jobs

# Run backup daily at 2 AM
0 2 * * * root /var/www/html/odonto360/backup.sh >> /var/log/odonto360_backup.log 2>&1

# Clean up old logs weekly
0 3 * * 0 root find /var/log -name "*.log" -mtime +30 -delete
EOF

# Create log directory
mkdir -p /var/log/odonto360
chown apache:apache /var/log/odonto360

# Final status check
echo "=== Installation Complete ==="
echo "Application URL: http://${app_url}"
echo "Health Check: http://${app_url}/health.php"
echo "Status Script: /var/www/html/odonto360/status.sh"
echo "Deploy Script: /var/www/html/odonto360/deploy.sh"
echo "Backup Script: /var/www/html/odonto360/backup.sh"
echo ""

# Run status check
/var/www/html/odonto360/status.sh

echo "=== Installation Finished Successfully ==="
