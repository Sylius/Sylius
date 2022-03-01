FROM ubuntu:20.04
ARG DEBIAN_FRONTEND=noninteractive

# Add php official repository
RUN apt update && apt install -y software-properties-common
RUN add-apt-repository ppa:ondrej/php

# Only required PHP extensions
RUN apt update && apt install -y \
    curl \
    make \
    supervisor \
    git \
    unzip \
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
    nginx

RUN ln -s /usr/sbin/php-fpm8.0 /usr/sbin/php-fpm

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename composer

RUN mkdir -p /run/php

# Initialize config files
COPY supervisord.conf   /etc/supervisor/conf.d/supervisor.conf
COPY nginx.conf         /etc/nginx/nginx.conf
COPY fpm.conf           /etc/php/8.0/fpm/pool.d/www.conf
COPY php.ini            /etc/php/8.0/fpm/php.init
COPY php.ini            /etc/php/8.0/cli/php.init

WORKDIR /app

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]
