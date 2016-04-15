#!/usr/bin/env bash
set -e

source "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/../../../bash/common.lib.sh"

prepare_memcached_extension() {
    if [ "${TRAVIS_PHP_VERSION}" != "7.0" ]; then
        return 0
    fi

    print_header "Preparing memcached extension for PHP 7.0" "Sylius"
    if [ ! -f "${SYLIUS_CACHE_DIR}/memcached.so" ]; then
        run_command "git clone -b php7 https://github.com/php-memcached-dev/php-memcached.git php-memcached"
        run_command "cd php-memcached && phpize && ./configure --disable-memcached-sasl && make"
        run_command "cp php-memcached/modules/memcached.so \"${SYLIUS_CACHE_DIR}\""
    fi

    run_command "cp \"${SYLIUS_CACHE_DIR}/memcached.so\" \"$(php -i | grep extension_dir | head -n 1 | awk '{ print $5 }')\""
}

print_header "Activating memcached extension" "Sylius"
run_command "echo \"extension = memcached.so\" >> ~/.phpenv/versions/$(phpenv version-name)/etc/conf.d/travis.ini"
