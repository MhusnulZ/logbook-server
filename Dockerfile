# Stage 1: Build assets
FROM node:20-alpine AS assets-builder
WORKDIR /var/www
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# Stage 2: PHP Application (Production Ready)
FROM php:8.3-fpm-alpine

# Set working directory
WORKDIR /var/www

# Install system dependencies
RUN apk add --no-cache \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    oniguruma-dev \
    libxml2-dev \
    icu-dev \
    nodejs \
    npm

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application files
COPY . .

# Copy built assets from builder stage
COPY --from=assets-builder /var/www/public/build ./public/build

# Install production dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Set permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Switch to non-root user for security
USER www-data

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
