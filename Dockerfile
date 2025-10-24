# 1. Base Image
FROM php:8.2-bullseye

# 2. Set Working Directory
WORKDIR /var/www/html

# 3. Install System Dependencies
# This is one single, unbroken RUN command.
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    zip \
    build-essential \
    libpq-dev \
    libzip-dev \
    libxml2-dev \
    libonig-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libwebp-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 4. Install PHP Extensions
# This is also one single, unbroken RUN command.
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install \
    pdo \
    pdo_pgsql \
    mbstring \
    xml \
    zip \
    fileinfo \
    gd \
    exif \
    pcntl \
    bcmath

# 5. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 6. Copy Application Code
COPY . .

# 7. Install Composer Dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction --ignore-platform-reqs

# 8. Set Permissions
RUN chown -R www-data:www-data storage bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

# 9. Run Laravel Optimizations
RUN php artisan config:cache
RUN php artisan route:cache

# 10. Change User
USER www-data

# 11. Expose Port
EXPOSE 8000

# 12. Entrypoint Command
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=${PORT:-8000}"]