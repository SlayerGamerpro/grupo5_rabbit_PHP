FROM php:8.5-cli

WORKDIR /app

# Instalar dependencias necesarias para Composer y ZIP
RUN apt-get update \
    && docker-php-ext-install sockets \
    && apt-get install -y unzip libzip-dev \
    && docker-php-ext-install zip \
    && rm -rf /var/lib/apt/lists/*

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copiar dependencias
COPY composer.json .

# Instalar librerías PHP
RUN composer install

# Copiar código
COPY productor ./productor
COPY consumidor ./consumidor

CMD ["php", "productor/productor.php"]