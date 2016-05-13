#!/usr/bin/env bash

source "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/../../../bash/common.lib.sh"

if [ $(git rev-parse --abbrev-ref HEAD) = "master" ]; then
    exit 0 # Always execute full suite on master
fi

if [ $(git diff --name-only HEAD origin/master | grep -c -e ^src/Sylius/Bundle -e ^src/Sylius/Component) -eq 0 ]; then
    print_header "Skipped suite" "Packages"
    print_warning "No changes detected in src/Sylius/Bundle/* or src/Sylius/Component/*"
    exit 1
fi

exit 0
