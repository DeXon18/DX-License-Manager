FROM php:8.4-fpm-alpine

# Instalar dependencias del sistema
RUN apk add --no-cache \
    bash \
    curl \
    git \
    zip \
    unzip \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    icu-dev \
    oniguruma-dev \
    libzip-dev \
    freetype \
    libjpeg-turbo \
    libpng \
    mariadb-client \
    docker-cli

# Instalar extensiones PHP
RUN docker-php-ext-configure gd \
        --with-freetype=/usr/include/ \
        --with-jpeg=/usr/include/ \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        zip \
        gd \
        intl \
        bcmath \
        opcache

# Instalar extensión Redis
RUN apk add --no-cache $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del $PHPIZE_DEPS

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# PHP ini para producción
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Configurar directorio seguro para Git (evita errores de ownership en volúmenes)
RUN git config --system --add safe.directory /var/www/html

WORKDIR /var/www/html
