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
RUN docker-php-ext-install exif || echo "exif failed, continuing..."
RUN docker-php-ext-install bcmath || echo "bcmath failed, continuing..."
RUN docker-php-ext-install intl || echo "intl failed, continuing..."

# Try GD extension (optional for basic functionality)
RUN docker-php-ext-install gd || echo "GD extension failed, continuing..."

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

# Set proper permissions
RUN chown -R www:www /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Create .env file from example
RUN cp .env.example .env

# Generate application key
RUN php artisan key:generate --no-interaction

# Optimize for production
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Stage 3: Nginx stage
FROM nginx:alpine AS nginx

# Copy nginx configuration
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/default.conf /etc/nginx/conf.d/default.conf

# Copy application files from PHP stage
COPY --from=php-base /var/www/html /var/www/html

# Create nginx user and set permissions
RUN adduser -D -S -G www nginx \
    && chown -R nginx:www-data /var/www/html \
    && chmod -R 755 /var/www/html

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
RUN docker-php-ext-install exif || echo "exif failed, continuing..."
RUN docker-php-ext-install bcmath || echo "bcmath failed, continuing..."
RUN docker-php-ext-install intl || echo "intl failed, continuing..."

# Try GD extension (optional for basic functionality)
RUN docker-php-ext-install gd || echo "GD extension failed, continuing..."

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application code
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Verify Node.js installation and install dependencies
RUN node --version && npm --version || echo "Node.js not available, skipping..."

# Install Node.js dependencies and build assets (with error handling)
RUN npm ci --only=production && npm run build || echo "Node.js build failed, continuing..."

# Set proper permissions
RUN chown -R www:www /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Copy configuration files
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/default.conf /etc/nginx/conf.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Create .env file from example
RUN cp .env.example .env

# Generate application key
RUN php artisan key:generate --no-interaction

# Optimize for production
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Expose port 80
EXPOSE 80

# Start supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
