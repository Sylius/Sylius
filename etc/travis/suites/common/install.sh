#!/usr/bin/env bash
set -e

source "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/../../../bash/common.lib.sh"

print_header "Activate Opcache extension" "Sylius"
run_command "phpenv config-add \"$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/assets/opcache.php.ini\""
