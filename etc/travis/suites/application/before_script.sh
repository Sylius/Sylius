#!/usr/bin/env bash

source "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/../../../bash/common.lib.sh"

print_header "Setting the application up" "Sylius"
run_command "app/console doctrine:database:create --env=test_cached -vvv" || exit $? # Have to be run with debug = true, to omit generating proxies before setting up the database
run_command "app/console cache:warmup --env=test_cached --no-debug -vvv" || exit $?
run_command "app/console doctrine:schema:create --env=test_cached --no-debug -vvv" || exit $?
run_command "app/console doctrine:phpcr:repository:init --env=test_cached --no-debug -vvv" || exit $?

print_header "Setting the web assets up" "sylius"
run_command "app/console assets:install --env=test_cached --no-debug -vvv" || exit $?
run_command "app/console assetic:dump --env=test_cached --no-debug -vvv" || exit $?
run_command "gulp" || exit $?
