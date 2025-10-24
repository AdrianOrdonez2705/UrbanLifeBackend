# 1. Base Image
# We use the official PHP 8.2 image. 'bullseye' is a stable OS version.
FROM php:8.2-bullseye

# 2. Set Working Directory
WORKDIR /var/www/html

# 3. Install System Dependencies
# We need Git, Zip (for Composer), and the dev libraries for PHP extensions.
# libpq-dev is critical for your pgsql (Supabase) connection.
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    zip \
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
# Copy all your project files into the container.
COPY . .

# 7. Install Composer Dependencies (for production)
# We skip dev dependencies and all scripts (like npm install).
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction --ignore-platform-reqs

# 8. Set Permissions
# The 'www-data' user (which we will use to run the app) needs to own
# the storage and cache folders to write logs and cache files.
RUN chown -R www-data:www-data storage bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

# 9. Run Laravel Optimizations
# We cache config and routes for faster production performance.
RUN php artisan config:cache
RUN php artisan route:cache

# 10. Change User
# Switch from the default 'root' user to 'www-data' for better security.
USER www-data

# 11. Expose Port
# This tells Render which port your application *intends* to use.
# The default for artisan serve is 8000.
EXPOSE 8000

# 12. Entrypoint Command
# This is the command that starts your server.
#
# --host=0.0.0.0 is REQUIRED to make the server accessible outside the container.
#
# --port=${PORT:-8000} tells Laravel to listen on the $PORT variable
# provided by Render. If for some reason $PORT isn't set, it defaults to 8000.
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=${PORT:-8000}"]