#!/bin/bash

# PHP

apt-get -y install php7.0-cli php7.0-fpm php7.0-dev php7.0-curl php7.0-intl \
    php7.0-mysql php7.0-sqlite3 php7.0-gd php7.0-mbstring php7.0-xml

sed -i 's/;date.timezone.*/date.timezone = Europe\/Brussels/' /etc/php/7.0/fpm/php.ini
sed -i 's/;date.timezone.*/date.timezone = Europe\/Brussels/' /etc/php/7.0/cli/php.ini
sed -i 's/^user = www-data/user = vagrant/' /etc/php/7.0/fpm/pool.d/www.conf
sed -i 's/^group = www-data/group = vagrant/' /etc/php/7.0/fpm/pool.d/www.conf

service php7.0-fpm restart

# composer
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin
ln -s /usr/bin/composer.phar /usr/bin/composer
