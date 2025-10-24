# 1. Base Image
FROM php:8.2-bullseye

# 2. Set Working Directory
WORKDIR /var/www/html

# 3. Install System Dependencies
# This is the updated, more robust list.
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    zip \
    # Core build tools
    build-essential \
    # Dependencies for PHP extensions
    libpq-dev \      # for pdo_pgsql
    libzip-dev \     # for zip
    libxml2-dev \    # for xml
    libonig-dev \    # for mbstring
    # Dependencies for gd
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libwebp-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 4. Install PHP Extensions (More robust method)
# Install core extensions
RUN docker-php-ext-install pdo pdo_pgsql mbstring xml zip fileinfo

# Configure and install GD with common features
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp
RUN docker-php-ext-install gd

# Install remaining extensions
RUN docker-php-ext-install exif pcntl bcmath

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