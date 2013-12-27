#!/bin/bash
#
# @copyright (c) 2013 phpBB Group
# @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
#
#set -e

sudo apt-get update -qq
sudo apt-get install -qq nginx realpath php5-fpm

sudo service php5-fpm stop
sudo service nginx stop

DIR=$(dirname "$0")
ROOT_PATH=$(realpath "$DIR/../web")

NGINX_CONF="/etc/nginx/sites-enabled/default"

PHP_FPM_BIN="php5-fpm"
PHP_FPM_CONF="$DIR/php-fpm.conf"
PHP_FPM_SOCK=$(realpath "$DIR")/php-fpm.sock

USER=$(whoami)

# php-fpm configuration
echo "
[global]

[travis]
user = $USER
group = $USER
listen = $PHP_FPM_SOCK
pm = static
pm.max_children = 2

php_admin_value[memory_limit] = 128M
" > $PHP_FPM_CONF

# nginx configuration
echo "
server {
    server_name localhost;
    listen 8080;
    root $ROOT_PATH;

    location / {
        try_files $uri @rewriteapp /dev/null =404;
    }

    location @rewriteapp {
        rewrite ^(.*)$ /app_test.php/$1 last;
    }

    location ~ ^/app_test\.php(/|$) {
        fastcgi_pass unix:$PHP_FPM_SOCK;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include /etc/nginx/fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $ROOT_PATH/app_test.php;
    }
}

" | sudo tee $NGINX_CONF > /dev/null

sudo cp -r /etc/php5/cli/conf.d /etc/php5/fpm/conf.d

# Start daemons
sudo $PHP_FPM_BIN --fpm-config "$DIR/php-fpm.conf"
sudo service nginx start
