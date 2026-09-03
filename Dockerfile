FROM php:8.5-fpm

WORKDIR /var/www
ARG user
ARG uid

RUN apt-get update && \
    apt-get install -y --no-install-recommends \
        curl \
        openssl \
        libpng-dev \
        libxml2-dev \
        libonig-dev \
        libzip-dev \
        libicu-dev \
        libsqlite3-dev \
        zip \
        unzip \
        make \
        git \
        && \
    apt-get clean -y && \
    rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql pdo_sqlite mbstring exif pcntl bcmath gd zip intl

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY --from=node:lts /usr/local/bin/node /usr/local/bin/node
COPY --from=node:lts /usr/local/lib/node_modules /usr/local/lib/node_modules
RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm && \
    ln -s /usr/local/lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx


COPY composer.json composer.lock ./
RUN composer install --no-interaction --prefer-dist --no-scripts

COPY package.json package-lock.json ./
RUN npm ci

COPY . ./
RUN composer dump-autoload --no-interaction --optimize && npm run build

RUN addgroup --gid $uid $user && \
    adduser --uid $uid --ingroup $user --home /home/$user --shell /bin/sh --disabled-password $user && \
    adduser $user www-data && \
    adduser $user root && \
    mkdir -p /home/$user/.composer && \
    mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache && \
    chown -R $user:$user /home/$user /var/www/storage /var/www/bootstrap/cache

USER $user
