FROM php:7.2.4-fpm-alpine3.7

RUN set -ex \
    && apk --no-cache --virtual .deps add zlib-dev postgresql-dev icu-dev \
    && apk --no-cache add postgresql-libs icu-libs \
    && docker-php-ext-install -j$(nproc) zip pdo pdo_pgsql bcmath intl \
    && apk del .deps

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

ENV COMPOSER_CACHE_DIR=/var/www/html/.composer
