#!/usr/bin/env bash
set -e

source "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/../../../bash/common.lib.sh"

print_header "Customizing the environment" "Sylius"
run_command "phpenv config-rm xdebug.ini" # Disable XDebug
run_command "echo \"memory_limit=4096M\" >> ~/.phpenv/versions/$(phpenv version-name)/etc/conf.d/travis.ini" # Increase memory limit to 4GB
run_command "mkdir -p \"${SYLIUS_CACHE_DIR}\"" # Create Sylius cache directory

print_header "Preparing Composer" "Sylius"
run_command "composer config -g github-oauth.github.com f89d08114c80d1a1b019b09ce60a38f75a5c9480" # Please do not use this key outside the Sylius project
run_command "composer self-update"
