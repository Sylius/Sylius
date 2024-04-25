# the different stages of this Dockerfile are meant to be built into separate images
# https://docs.docker.com/compose/compose-file/#target

ARG PHP_VERSION=8.2
ARG NODE_VERSION=20
ARG ALPINE_VERSION=3.18
ARG COMPOSER_VERSION=2.6
ARG PHP_EXTENSION_INSTALLER_VERSION=latest

FROM composer:${COMPOSER_VERSION} AS composer

FROM mlocati/php-extension-installer:${PHP_EXTENSION_INSTALLER_VERSION} AS php_extension_installer

FROM php:${PHP_VERSION}-fpm-alpine${ALPINE_VERSION} AS base

# persistent / runtime deps
RUN apk add --no-cache \
        acl \
        file \
        gettext \
        unzip \
        apk-cron \
    ;

COPY --from=php_extension_installer /usr/bin/install-php-extensions /usr/local/bin/

# default PHP image extensions
# ctype curl date dom fileinfo filter ftp hash iconv json libxml mbstring mysqlnd openssl pcre PDO pdo_sqlite Phar
# posix readline Reflection session SimpleXML sodium SPL sqlite3 standard tokenizer xml xmlreader xmlwriter zlib
RUN install-php-extensions apcu exif gd intl pdo_mysql opcache zip

COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY .bunnyshell/docker/php/prod/php.ini        $PHP_INI_DIR/php.ini
COPY .bunnyshell/docker/php/prod/opcache.ini    $PHP_INI_DIR/conf.d/opcache.ini

# copy file required by opcache preloading
COPY config/preload.php /srv/sylius/config/preload.php

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN set -eux; \
    composer clear-cache
ENV PATH="${PATH}:/root/.composer/vendor/bin"

WORKDIR /srv/sylius

# build for production
ENV APP_ENV=prod

# prevent the reinstallation of vendors at every changes in the source code
COPY composer.* symfony.lock ./
RUN set -eux; \
    composer install --prefer-dist --no-autoloader --no-interaction --no-scripts --no-progress; \
    composer clear-cache

# copy only specifically what we need
COPY .env ./
COPY bin bin/
COPY config config/
COPY public public/
COPY src src/
COPY templates templates/
COPY translations translations/

RUN set -eux; \
    mkdir -p var/cache var/log; \
    composer dump-autoload --classmap-authoritative; \
    chmod +x bin/console; sync; \
    bin/console sylius:install:assets --no-interaction; \
    bin/console sylius:theme:assets:install public --no-interaction

VOLUME /srv/sylius/var

VOLUME /srv/sylius/public/media

COPY .bunnyshell/docker/php/files/crontab /etc/crontabs/root
COPY .bunnyshell/docker/php/entrypoints/* /usr/local/bin/
RUN chmod +x /usr/local/bin/*-entrypoint

ENTRYPOINT ["php-entrypoint"]
CMD ["php-fpm"]

FROM node:${NODE_VERSION}-alpine${ALPINE_VERSION} AS sylius_node

WORKDIR /srv/sylius

RUN set -eux; \
	apk add --no-cache --virtual .build-deps \
		g++ \
		gcc \
		make \
	;

# prevent the reinstallation of vendors at every changes in the source code
COPY package.json yarn.* ./
RUN set -eux; \
    yarn install; \
    yarn cache clean

COPY --from=base /srv/sylius/src/Sylius/Bundle/UiBundle/Resources/private       src/Sylius/Bundle/UiBundle/Resources/private/
COPY --from=base /srv/sylius/src/Sylius/Bundle/AdminBundle/Resources/private    src/Sylius/Bundle/AdminBundle/Resources/private/
COPY --from=base /srv/sylius/src/Sylius/Bundle/ShopBundle/Resources/private     src/Sylius/Bundle/ShopBundle/Resources/private/

COPY webpack.config.js ./
RUN yarn build:prod

FROM base AS sylius_php_prod

COPY --from=sylius_node /srv/sylius/public/build public/build

WORKDIR /srv/sylius

COPY --from=base        /srv/sylius/public public/
COPY --from=sylius_node /srv/sylius/public public/
