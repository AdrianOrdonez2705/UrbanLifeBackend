# ----
# 1. La Base: Usamos una imagen oficial de PHP 8.2 (necesaria para Laravel 11/12)
# ----
FROM php:8.2-cli AS base

# Marcamos nuestra carpeta de trabajo dentro del "contenedor"
WORKDIR /var/www/html

# ----
# 2. Instalamos las herramientas y extensiones de PHP que Laravel necesita
# ----
RUN apt-get update && apt-get install -y \
    curl \
    zip \
    unzip \
    && apt-get clean && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install \
    pdo_pgsql \
    mbstring \
    exif \
    bcmath

# ----
# 3. Instalamos Composer (el manejador de paquetes de PHP)
# ----
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ----
# 4. Copiamos tu proyecto y configuramos permisos
# ----
# Primero copiamos solo composer.json para instalar dependencias
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-autoloader --no-scripts

# Ahora copiamos el resto de tu código
COPY . .

# Generamos el autoloader de Laravel
RUN composer dump-autoload --optimize

# Damos permisos a la carpeta de storage
RUN chmod -R 775 storage bootstrap/cache

# ----
# 5. Configuración final
# ----
# Limpiamos la caché por si acaso
RUN php artisan config:clear
RUN php artisan view:clear
RUN php artisan route:clear

# Exponemos el puerto que Render usará (esto es solo informativo)
EXPOSE 10000

# El comando de inicio se lo daremos en el dashboard de Render