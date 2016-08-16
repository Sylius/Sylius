#!/usr/bin/env sh
chown -R www-data:www-data -R app/cache app/logs
php app/console doctrine:schema:create > /dev/null 2>&1 || true
php app/console sylius:install
php-fpm7 -F
