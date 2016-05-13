#!/usr/bin/env bash

source "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/../../../bash/common.lib.sh"

if [ $(git diff --name-only HEAD origin/master | grep -c -e ^docs) -eq 0 ]; then
    print_header "Skipped suite" "Docs"
    print_warning "No changes detected in docs/*"
    exit 1
fi

exit 0
