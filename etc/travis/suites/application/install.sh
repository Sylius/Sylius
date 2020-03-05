#!/usr/bin/env bash

source "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/../../../bash/common.lib.sh"
source "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/../../../bash/application.sh"

print_header "Installing dependencies" "Sylius"
run_command "if [ ! -z \"${SYMFONY_VERSION}\" ]; then bin/require-symfony-version composer.json \"${SYMFONY_VERSION}\"; fi" || exit $?
run_command "composer install --no-interaction --prefer-dist" || exit $?
run_command "composer dump-env test_cached" || exit $?

print_header "Setting up JWT for API" "Sylius"
run_command "source .env.test"
run_command "openssl genrsa -out config/jwt/private-test.pem 4096 -algorithm rsa -passout env:JWT_PASSPHRAS rsa_keygen_bits:4096"
run_command "openssl pkey -in config/jwt/private-test.pem -out config/jwt/public-test.pem -pubout"

print_header "Warming up dependencies" "Sylius"
run_command "yarn install" || exit $?
