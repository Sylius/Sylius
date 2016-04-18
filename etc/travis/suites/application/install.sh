#!/usr/bin/env bash
set -e

source "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/../../../bash/common.lib.sh"

print_header "Installing dependencies" "Sylius"
run_command "composer install --no-interaction --no-scripts --prefer-dist"
run_command "npm install"

print_header "Warming up dependencies" "Sylius"
run_command "composer run-script travis-build --no-interaction"
