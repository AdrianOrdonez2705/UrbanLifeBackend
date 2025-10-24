# 1. Base Image: Use the official PHP 8.2-FPM image.
FROM php:8.2-fpm-bullseye

# 2. Set Working Directory
WORKDIR /var/www/html

# 3. Install System Dependencies
# We need Git, Zip (for Composer), and Supervisor.
# We also need Nginx as our web server.
# And we need the dev libraries for the PHP extensions (libpq for pgsql, libzip, etc.)
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    zip \
    nginx \
    supervisor \
    libpq-dev \
    libzip-dev \
    libxml2-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 4. Install PHP Extensions
# This installs all extensions Laravel needs, PLUS pdo_pgsql for your Supabase database.
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    zip \
    xml \
    fileinfo \
    gd

# 5. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 6. Copy Application Code
# We copy the code *before* installing dependencies to leverage Docker caching.
COPY . .

# 7. Install Composer Dependencies (for production)
# We ignore platform reqs to use the image's PHP version and skip dev tools.
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction --ignore-platform-reqs

# 8. Set Permissions
# The web server (www-data) needs to own storage and cache to write logs and cache files.
RUN chown -R www-data:www-data storage bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

# 9. Copy Configuration Files
# Copy the Nginx and Supervisor configs we will create next.
COPY .docker/nginx.conf /etc/nginx/sites-available/default
COPY .docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# 10. Expose Port
# Nginx will listen on port 80 inside the container.
# Render will automatically map its public URL to this port.
EXPOSE 80

# 11. Run Laravel Optimizations
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# 12. Entrypoint
# Start Supervisor, which will in turn start Nginx and PHP-FPM.
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]