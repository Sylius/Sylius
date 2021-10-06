FROM php:7.4-fpm

ARG UID=1000
ARG PLATFORM_OS=Linux

COPY --from=composer:1 /usr/bin/composer /usr/local/bin/composer

# Get packages
RUN set -xe; \
    apt-get update; \
    apt-get install -y ${PHPIZE_DEPS} \
        libfreetype6-dev \
        libjpeg-dev \
        libjpeg62-turbo-dev \
        libonig-dev \
        libpng-dev \
        libxml2-dev \
        librabbitmq-dev \
        libfontconfig1 \
        libxrender1 \
        libxext6 \
        libzip-dev \
        apt-utils \
        gnupg \
        default-mysql-client \
        redis-tools \
        vim \
        procps \
        git \
        unzip \
        ssh \
        zsh \
        bash-completion;

# PHP extensions
RUN set -xe; \
    pecl install -o -f redis xdebug amqp;
RUN set -xe; \
    docker-php-ext-configure gd --with-freetype --with-jpeg;
RUN set -xe; \
    docker-php-ext-install -j$(nproc) \
        mbstring gd zip opcache pdo_mysql bcmath soap intl exif pcntl sockets; \
    docker-php-ext-enable redis pcntl amqp;

# Fix user / folder rights
RUN set -xe; \
    usermod -u ${UID} www-data; \
    chown -R www-data:www-data /var/www;

# Bash competion
RUN set -xe; \
    echo  '. /etc/bash_completion' >> /var/www/.bashrc;

USER www-data

# OhMyZsh auto completion and shell custom
RUN set -xe; \
    curl -sL https://github.com/robbyrussell/oh-my-zsh/raw/master/tools/install.sh | zsh || true; \
    echo 'setopt correct' >> /var/www/.zshrc;

VOLUME ["/var/www/html"]
