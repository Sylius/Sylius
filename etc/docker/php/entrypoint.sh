#!/usr/bin/env sh
chown -R www-data:www-data app/cache app/logs

composer install --no-interaction

app/console doctrine:database:create
app/console cache:warmup
app/console doctrine:migrations:migrate --no-interaction
app/console doctrine:phpcr:repository:init
app/console assets:install

php-fpm7 -F
