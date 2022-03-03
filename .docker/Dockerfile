FROM ubuntu:20.04
ARG DEBIAN_FRONTEND=noninteractive
ENV LC_ALL=C.UTF-8

# Install basic tools
RUN apt update && apt install -y \
    software-properties-common \
    apt-utils \
    curl

# Append NODE and PHP repositories
RUN add-apt-repository ppa:ondrej/php \
    && add-apt-repository ppa:ondrej/nginx \
    && curl -sL https://deb.nodesource.com/setup_14.x | bash -

# Install required PHP extensions
RUN apt update && apt install -y \
    make \
    supervisor \
    git \
    unzip \
    nodejs \
    nginx \
    php8.0 \
    php8.0-common \
    php8.0-cli \
    php8.0-xml \
    php8.0-curl \
    php8.0-gd \
    php8.0-intl \
    php8.0-opcache \
    php8.0-pdo \
    php8.0-ctype \
    php8.0-calendar \
    php8.0-dom \
    php8.0-exif \
    php8.0-xsl \
    php8.0-zip \
    php8.0-fpm \
    php8.0-mysql \
    php8.0-mbstring

# Link php-fpm binary file without version
RUN ln -s /usr/sbin/php-fpm8.0 /usr/sbin/php-fpm

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename composer

# Create directory for php-fpm socket
RUN mkdir -p /run/php

# Install yarn
RUN npm install -g yarn

# Initialize config files
COPY supervisord.conf   /etc/supervisor/conf.d/supervisor.conf
COPY nginx.conf         /etc/nginx/nginx.conf
COPY fpm.conf           /etc/php/8.0/fpm/pool.d/www.conf
COPY php.ini            /etc/php/8.0/fpm/php.ini
COPY php.ini            /etc/php/8.0/cli/php.ini

WORKDIR /app

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]
