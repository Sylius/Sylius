#!/usr/bin/env bash

source "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/../../../bash/common.lib.sh"
source "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/../../../bash/application.sh"

print_header "Moving 'Sylius\Behat' to autoload-dev (needed later for composer-require-checker)" "Sylius"
run_command "(cat composer.json | jq '.[\"autoload-dev\"][\"psr-4\"] |= . + {\"Sylius\\\\Behat\\\\\": \"src/Sylius/Behat/\"}' | jq 'del(.autoload[\"psr-4\"][\"Sylius\\\\Behat\\\\\"])') > _composer.json"
run_command "mv _composer.json composer.json"
run_command "cat composer.json"

print_header "Installing dependencies" "Sylius"
run_command "if [ ! -z \"${SYMFONY_VERSION}\" ]; then bin/require-symfony-version composer.json \"${SYMFONY_VERSION}\"; fi" || exit $?
run_command "composer install --no-interaction --prefer-dist" || exit $?
run_command "composer dump-env test_cached" || exit $?

print_header "Warming up dependencies" "Sylius"
run_command "yarn install" || exit $?
