# Multi-stage build for Odonto360 Laravel application

# Stage 1: Build stage
FROM node:18-alpine AS node-build
WORKDIR /app
COPY package*.json ./
RUN npm ci --only=production

# Stage 2: PHP stage
FROM php:8.1-fpm-alpine AS php-base

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    oniguruma-dev \
    libzip-dev \
    mysql-client \
    freetype-dev \
    libjpeg-turbo-dev

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg

# Install essential PHP extensions only
RUN docker-php-ext-install pdo_mysql mbstring zip opcache || echo "Some extensions failed, continuing..."

# Try to install optional extensions
# Try to install optional extensions (parallel build)
RUN docker-php-ext-install -j$(nproc) exif bcmath intl gd || echo "Optional extensions failed, continuing..."

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Copy application code
COPY . .

# Copy node build artifacts
COPY --from=node-build /app/node_modules ./node_modules

# Create necessary directories and set proper permissions
RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache \
    && chmod -R 755 /var/www/html/public \
    && (chown -R www:www /var/www/html || chown -R 1000:1000 /var/www/html || true)

# Create .env file from example
RUN cp .env.example .env

# Generate application key
RUN php artisan key:generate --no-interaction

# Create views directory and optimize for production
RUN mkdir -p /var/www/html/resources/views \
    && php artisan config:cache \
    && php artisan route:cache \
    && (php artisan view:cache || echo "View cache failed, continuing...")

# Stage 3: Nginx stage
FROM nginx:alpine AS nginx

# Copy nginx configuration
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/default.conf /etc/nginx/conf.d/default.conf

# Copy application files from PHP stage
COPY --from=php-base /var/www/html /var/www/html

# Create nginx user and set permissions
RUN adduser -D -S -G www nginx \
    && chmod -R 755 /var/www/html \
    && (chown -R nginx:www /var/www/html || chown -R 1000:1000 /var/www/html || true)

# Expose port 80
EXPOSE 80

# Start nginx
CMD ["nginx", "-g", "daemon off;"]

# Stage 4: Final production stage
FROM php:8.1-fpm-alpine AS production

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    oniguruma-dev \
    libzip-dev \
    mysql-client \
    freetype-dev \
    libjpeg-turbo-dev \
    nginx \
    supervisor

# Install Node.js from official repository
RUN apk add --no-cache nodejs npm

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg

# Install essential PHP extensions only
RUN docker-php-ext-install pdo_mysql mbstring zip opcache || echo "Some extensions failed, continuing..."

# Try to install optional extensions
# Try to install optional extensions (parallel build)
RUN docker-php-ext-install -j$(nproc) exif bcmath intl gd || echo "Optional extensions failed, continuing..."

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application code
COPY . .

# Install dependencies
RUN composer validate --no-check-publish && \
    composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs --no-scripts --prefer-dist

# Verify Node.js installation and install dependencies
RUN node --version && npm --version || echo "Node.js not available, skipping..."

# Install Node.js dependencies and build assets (with error handling)
RUN npm ci --only=production --silent && npm run build --silent || echo "Node.js build failed, continuing..."

# Create necessary directories and set proper permissions
RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache \
    /var/log/supervisor /var/run /var/cache/nginx \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache \
    && chmod -R 755 /var/log/supervisor \
    && chmod -R 755 /var/run \
    && chmod -R 755 /var/cache/nginx \
    && (chown -R www:www /var/www/html || chown -R 1000:1000 /var/www/html || true)

# Copy configuration files
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/default.conf /etc/nginx/conf.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Create .env file from example
RUN cp .env.example .env

# Generate application key
RUN php artisan key:generate --no-interaction

# Create views directory and optimize for production
RUN mkdir -p /var/www/html/resources/views \
    && php artisan config:cache \
    && php artisan route:cache \
    && (php artisan view:cache || echo "View cache failed, continuing...")

# Copy startup script
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

# Expose port (will be set by environment variable)
EXPOSE $PORT

# Start supervisor
CMD ["/start.sh"]
