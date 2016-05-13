#!/usr/bin/env bash

source "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/../../../bash/common.lib.sh"

if [ $(git diff --name-only HEAD origin/master | grep -c -v -e ^docs) -eq 0 ]; then
    print_header "Skipped suite" "Application"
    print_warning "No other changes than those in docs/* were found"
    exit 1
fi

exit 0
