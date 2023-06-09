ARG PHP_VERSION=8.2.6
ARG ALPINE_VERSION=3.18
ARG PHP_EXTENSION_INSTALLER_VERSION=2.1.28
ARG COMPOSER_VERSION=2.5.5
ARG NODE_VERSION=16

FROM composer:${COMPOSER_VERSION} AS composer
FROM mlocati/php-extension-installer:${PHP_EXTENSION_INSTALLER_VERSION} AS php_extension_installer
FROM php:${PHP_VERSION}-cli-alpine${ALPINE_VERSION} AS php
FROM node:${NODE_VERSION}-alpine${ALPINE_VERSION} AS node

FROM composer AS vendor

ARG COMPOSER_NO_DEV=1
# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1

COPY composer.* symfony.lock ./

RUN composer install --prefer-dist --no-autoloader --no-interaction --no-scripts --no-progress --ignore-platform-reqs

FROM node AS frontend

WORKDIR /app/

COPY package.json /app/

RUN yarn install

COPY . /app/

RUN yarn encore dev

CMD ["yarn", "encore", "dev", "--watch"]

FROM php AS backend

ENV PHP_CLI_SERVER_WORKERS=3

COPY --from=php_extension_installer /usr/bin/install-php-extensions /usr/bin/install-php-extensions

RUN install-php-extensions blackfire xdebug exif gd intl mbstring ctype pdo_mysql pdo_pgsql zip opcache

COPY --from=composer /usr/bin/composer /usr/bin/composer

COPY .docker/ $PHP_INI_DIR/conf.d/

COPY --from=vendor /app/ /app/

COPY . /app/

WORKDIR /app/

RUN composer check-platform-reqs && \
    composer dump-autoload --classmap-authoritative --optimize

COPY --from=frontend /app/public/build /app/public/build

RUN find public/media/image -type f -print0 | sed "s/public\/media\/image\///" | xargs -0 -I{} sh -c "bin/console liip:imagine:cache:resolve {} || true"

CMD ["php", "-S", "0.0.0.0:80", "-t", "/app/public"]
EXPOSE 80

HEALTHCHECK --interval=5s --timeout=3s --start-period=5s --retries=3 CMD ["curl", "http://localhost:80"]
