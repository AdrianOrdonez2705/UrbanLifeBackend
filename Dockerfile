# ----
# 1. La Base: Usamos una imagen oficial de PHP 8.2 (necesaria para Laravel 11/12)
# ----
FROM php:8.2-cli AS base

# Marcamos nuestra carpeta de trabajo dentro del "contenedor"
WORKDIR /var/www/html

# ----
# 2. Instalamos las herramientas, extensiones Y FORZAMOS IPv4
# ----
RUN apt-get update && apt-get install -y \
    curl \
    zip \
    unzip \
    libpq-dev \
    libonig-dev \
    libexif-dev \
    libzip-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/* \
    \
    # --- ¡ESTA ES LA LÍNEA NUEVA! ---
    # Forzamos al sistema a preferir IPv4 sobre IPv6 para conexiones salientes
    && echo "precedence ::ffff:0:0/96 100" >> /etc/gai.conf \
    # --- FIN DE LA LÍNEA NUEVA ---
    \
    && docker-php-ext-install \
    pdo_pgsql \
    mbstring \
    exif \
    bcmath \
    zip

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

# Exponemos el puerto (esto es más informativo, el $PORT de abajo es el que manda)
EXPOSE 10000

# ----
# 6. COMANDO DE INICIO
# ----
# Esto le dice a Render qué ejecutar CUANDO el contenedor se inicie.
CMD ["sh", "-c", "php artisan config:cache && php artisan route:cache && php artisan migrate --force && php artisan serve --host 0.0.0.0 --port $PORT"]