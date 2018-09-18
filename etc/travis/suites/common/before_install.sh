#!/usr/bin/env bash

source "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/../../../bash/common.lib.sh"

print_header "Customizing the environment" "Sylius"
run_command "git fetch origin $TRAVIS_BRANCH:refs/remotes/origin/$TRAVIS_BRANCH" || exit $? # Make origin/master available for is_suitable steps
run_command "phpenv config-rm xdebug.ini" # Disable XDebug
run_command "echo \"memory_limit=4096M\" >> ~/.phpenv/versions/$(phpenv version-name)/etc/conf.d/travis.ini" || exit $? # Increase memory limit to 4GB
run_command "mkdir -p \"${SYLIUS_CACHE_DIR}\"" || exit $? # Create Sylius cache directory
