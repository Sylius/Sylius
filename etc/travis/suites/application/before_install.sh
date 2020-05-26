#!/usr/bin/env bash

source "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/../../../bash/common.lib.sh"
source "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/../../../bash/application.sh"

# Download and configure Symfony webserver
print_header "Downloading Symfony CLI" "Sylius"
if [ ! -f $SYLIUS_CACHE_DIR/symfony ]; then
    run_command "wget https://get.symfony.com/cli/installer -O - | bash"
    run_command "mv ~/.symfony/bin/symfony $SYLIUS_CACHE_DIR"
fi
run_command "php -v | head -n 1 | awk '{ print \$2 }' > .php-version"
run_command "$SYLIUS_CACHE_DIR/symfony version"
run_command "$SYLIUS_CACHE_DIR/symfony local:php:list"
run_command "$SYLIUS_CACHE_DIR/symfony php -v"
