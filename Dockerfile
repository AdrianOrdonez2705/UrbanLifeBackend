FROM php:8.3-apache AS builder
WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    build-essential \
    libpq-dev \
    libzip-dev \
    libxml2-dev \
    libonig-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libwebp-dev \
    git \
    curl \
    unzip \
    zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install \
    pdo \
    pdo_pgsql \
    mbstring \
    xml \
    zip \
    gd \
    exif \
    bcmath

RUN a2enmod rewrite

COPY --from=composer:2.8.12 /usr/bin/composer /usr/bin/composer

COPY database/ database/
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

COPY . .

RUN composer dump-autoload --optimize --no-dev

RUN chown -R www-data:www-data storage bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

FROM php:8.3-apache AS final
WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    libxml2-dev \
    libonig-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libwebp-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=builder /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=builder /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/

COPY --from=builder /etc/apache2/mods-enabled/rewrite.load /etc/apache2/mods-enabled/

COPY --from=builder /var/www/html .

COPY .docker/laravel.conf /etc/apache2/sites-available/000-default.conf
COPY .docker/docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]

CMD ["apache2-foreground"]