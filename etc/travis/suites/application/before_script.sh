#!/usr/bin/env bash

source "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/../../../bash/common.lib.sh"
source "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/../../../bash/application.sh"

print_header "Setting the application up" "Sylius"
run_command "bin/console doctrine:database:create --env=test_cached -vvv" || exit $? # Have to be run with debug = true, to omit generating proxies before setting up the database
run_command "bin/console cache:warmup --env=test_cached --no-debug -vvv" || exit $?
run_command "bin/console doctrine:migrations:migrate --no-interaction --env=test_cached --no-debug -vvv" || exit $?

print_header "Setting the web assets up" "sylius"
run_command "bin/console assets:install --env=test_cached --no-debug -vvv" || exit $?
run_command "yarn run gulp" || exit $?
