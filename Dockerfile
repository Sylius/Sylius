# the different stages of this Dockerfile are meant to be built into separate images
# https://docs.docker.com/compose/compose-file/#target

ARG PHP_VERSION=8.0
ARG NODE_VERSION=14
ARG NGINX_VERSION=1.21

FROM php:${PHP_VERSION}-fpm-alpine AS sylius_php

# persistent / runtime deps
RUN apk add --no-cache \
        acl \
        file \
        gettext \
        git \
        mariadb-client \
    ;

ARG APCU_VERSION=5.1.17
RUN set -eux; \
	apk add --no-cache --virtual .build-deps \
		$PHPIZE_DEPS \
		coreutils \
		freetype-dev \
		icu-dev \
		libjpeg-turbo-dev \
		libpng-dev \
		libtool \
		libwebp-dev \
		libzip-dev \
		mariadb-dev \
		zlib-dev \
	; \
	\
	docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype; \
	docker-php-ext-configure zip --with-zip; \
	docker-php-ext-install -j$(nproc) \
		exif \
		gd \
		intl \
		pdo_mysql \
		zip \
	; \
	pecl install \
		apcu-${APCU_VERSION} \
		xdebug \
	; \

	pecl clear-cache; \
	docker-php-ext-enable \
		apcu \
		opcache \
		xdebug \
	; \
	\
	runDeps="$( \
		scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions \
			| tr ',' '\n' \
			| sort -u \
			| awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
	)"; \
	apk add --no-cache --virtual .sylius-phpexts-rundeps $runDeps; \
	\
	apk del .build-deps \
;
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY docker/php/php.ini /usr/local/etc/php/php.ini
COPY docker/php/php-cli.ini /usr/local/etc/php/php-cli.ini

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN set -eux; \
    composer clear-cache
ENV PATH="${PATH}:/root/.composer/vendor/bin"

WORKDIR /srv/sylius

ARG APP_ENV=dev

# prevent the reinstallation of vendors at every changes in the source code
COPY composer.* symfony.lock ./
RUN set -eux; \
    composer update --prefer-dist --no-autoloader --no-scripts --no-progress; \
    composer clear-cache

# copy only specifically what we need
COPY .env .env.test .env.test_cached ./
COPY bin bin/
COPY config config/
COPY public public/
COPY src src/
COPY templates templates/
COPY translations translations/

RUN mkdir -p var/cache var/log;
RUN composer dump-autoload --classmap-authoritative;

VOLUME /srv/sylius/var

VOLUME /srv/sylius/public/media

COPY docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]

FROM node:${NODE_VERSION}-alpine AS sylius_node

WORKDIR /srv/sylius

RUN set -eux; \
	apk add --no-cache --virtual .build-deps \
		g++ \
		gcc \
		git \
		make \
		python2 \
	;

# prevent the reinstallation of vendors at every changes in the source code
COPY package.json yarn.lock ./
RUN set -eux; \
    yarn install; \
    yarn cache clean

COPY --from=sylius_php /srv/sylius/src/Sylius/Bundle/UiBundle/Resources/private src/Sylius/Bundle/UiBundle/Resources/private/
COPY --from=sylius_php /srv/sylius/src/Sylius/Bundle/AdminBundle/Resources/private src/Sylius/Bundle/AdminBundle/Resources/private/
COPY --from=sylius_php /srv/sylius/src/Sylius/Bundle/ShopBundle/Resources/private src/Sylius/Bundle/ShopBundle/Resources/private/

COPY --from=sylius_php /srv/sylius/src/Sylius/Bundle/AdminBundle/gulpfile.babel.js src/Sylius/Bundle/AdminBundle/gulpfile.babel.js
COPY --from=sylius_php /srv/sylius/src/Sylius/Bundle/ShopBundle/gulpfile.babel.js src/Sylius/Bundle/ShopBundle/gulpfile.babel.js

COPY gulpfile.babel.js .babelrc ./
RUN set -eux; \
    GULP_ENV=prod yarn build

COPY docker/node/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]
CMD ["yarn", "watch"]

FROM nginx:${NGINX_VERSION}-alpine AS sylius_nginx

COPY docker/nginx/conf.d/default.conf /etc/nginx/conf.d/

WORKDIR /srv/sylius

COPY --from=sylius_php /srv/sylius .
COPY --from=sylius_php /srv/sylius/public public/
#COPY --from=sylius_php /srv/sylius/public/media/image public/media/image
#
#COPY --from=sylius_php /srv/sylius/public/media/image/ public/media/image/
#RUN ls -la public/media/image/*


