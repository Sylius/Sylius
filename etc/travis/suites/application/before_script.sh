#!/usr/bin/env bash

source "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/../../../bash/common.lib.sh"
source "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/../../../bash/application.sh"

print_header "Setting the application up" "Sylius"
run_command "bin/console doctrine:database:create --env=test_cached -vvv" || exit $? # Have to be run with debug = true, to omit generating proxies before setting up the database
run_command "APP_DEBUG=1 bin/console cache:warmup --env=dev -vvv" || exit $? # For PHPStan
run_command "bin/console cache:warmup --env=test_cached --no-debug -vvv" || exit $? # For tests
run_command "bin/console doctrine:migrations:migrate --no-interaction --env=test_cached --no-debug -vvv" || exit $?

print_header "Setting the web assets up" "Sylius"
run_command "bin/console assets:install public --env=test_cached --no-debug -vvv" || exit $?
run_command "yarn build" || exit $?

print_header "Making filesystem readonly" "Sylius"
run_command "chmod -R 555 app bin config docs features src templates tests translations vendor"
