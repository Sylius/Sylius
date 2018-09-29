#!/usr/bin/env bash

source "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/../../../bash/common.lib.sh"

code=0
commands=(
    validate-composer
    validate-composer-security
    validate-doctrine-schema
    validate-twig
    validate-yaml-files
    validate-yarn-packages
    test-phpstan
    test-phpspec
    test-phpunit
    test-installer
    test-fixtures
    test-behat-without-javascript
    test-behat-with-javascript
    test-behat-with-cli
)

for command in ${commands[@]}; do
    "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/script/${command}" || code=$?
done

exit ${code}
