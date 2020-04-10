#!/usr/bin/env bash

source "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/../../../bash/common.lib.sh"
source "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/../../../bash/application.sh"

print_header "Activating memcached extension" "Sylius"
run_command "echo \"extension = memcached.so\" >> ~/.phpenv/versions/$(phpenv version-name)/etc/conf.d/travis.ini" || exit $?
run_command "echo \"memory_limit=4096M\" >> ~/.phpenv/versions/$(phpenv version-name)/etc/conf.d/travis.ini" || exit $?

print_header "Updating Composer" "Sylius"
run_command "composer self-update --preview"

# Download and configure Symfony webserver
print_header "Downloading Symfony CLI" "Sylius"
if [ ! -f $SYLIUS_CACHE_DIR/symfony ]; then
    run_command "wget https://get.symfony.com/cli/installer -O - | bash"
    run_command "mv ~/.symfony/bin/symfony $SYLIUS_CACHE_DIR"
fi
run_command "$SYLIUS_CACHE_DIR/symfony version"
