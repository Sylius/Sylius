#!/bin/bash

cd /var/www/sylius

composer install --optimize-autoloader

php app/console sylius:install --no-interaction
