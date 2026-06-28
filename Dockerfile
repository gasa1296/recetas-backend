FROM php:8.5-fpm-alpine

WORKDIR /var/www

ARG user
ARG uid

RUN apk add --no-cache \
    git \
    curl \
    nodejs \
    npm \
    openssl \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    icu-dev \
    linux-headers \
    make \
    ghostscript \
    fontforge

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN addgroup -g $uid -S $user && \
    adduser -u $uid -S $user -G $user -h /home/$user -s /bin/sh && \
    adduser $user www-data && \
    adduser $user root && \
    mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

USER $user
