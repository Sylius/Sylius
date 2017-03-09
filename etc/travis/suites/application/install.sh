#!/usr/bin/env bash

source "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/../../../bash/common.lib.sh"
source "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/../../../bash/application.sh"

print_header "Installing dependencies" "Sylius"
run_command "composer install --no-interaction --no-scripts --prefer-dist" || exit $?

print_header "Warming up dependencies" "Sylius"
run_command "composer run-script travis-build --no-interaction" || exit $?
run_command "yarn install" || exit $?
