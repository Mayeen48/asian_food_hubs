FROM php:8.4-fpm

# Install system dependencies
RUN apt-get update && \
    apt-get install -y \
        git \
        curl \
        unzip \
        libpng-dev \
        libonig-dev \
        libxml2-dev \
        libzip-dev \
        zip && \
    docker-php-ext-install pdo pdo_mysql mbstring zip gd && \
    rm -rf /var/lib/apt/lists/*

# Copy Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
