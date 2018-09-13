#!/usr/bin/env bash

source "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/../../../bash/common.lib.sh"
source "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/../../../bash/application.sh"

print_header "Installing dependencies" "Sylius"
run_command "if [ ! -z \"${SYMFONY_VERSION}\" ]; then bin/require-symfony-version composer.json \"${SYMFONY_VERSION}\"; fi" || exit $?
run_command "composer install --no-interaction --prefer-dist" || exit $?

print_header "Warming up dependencies" "Sylius"
run_command "yarn install" || exit $?
