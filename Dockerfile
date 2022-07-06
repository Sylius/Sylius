ARG PHP_VERSION=8.0
ARG NODE_VERSION=14
ARG NGINX_VERSION=1.21
ARG ALPINE_VERSION=3.15
ARG COMPOSER_VERSION=2
ARG PHP_EXTENSION_INSTALLER_VERSION=latest

FROM composer:${COMPOSER_VERSION} AS composer

FROM mlocati/php-extension-installer:${PHP_EXTENSION_INSTALLER_VERSION} AS php_extension_installer

FROM php:${PHP_VERSION}-fpm-alpine${ALPINE_VERSION} AS php

COPY --from=composer                /usr/bin/composer               /usr/local/bin/
COPY --from=php_extension_installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions zip gd intl exif pdo_mysql pdo_pgsql opcache apcu xml curl mbstring

RUN apk add --no-cache make

COPY .docker/dev/php.ini /usr/local/etc/php/php.ini

WORKDIR /app

CMD ["php-fpm"]

FROM node:${NODE_VERSION}-alpine${ALPINE_VERSION} AS node

RUN apk add --no-cache g++ gcc make python2

WORKDIR /app

CMD ["yarn", "watch"]

FROM nginx:${NGINX_VERSION}-alpine AS nginx
