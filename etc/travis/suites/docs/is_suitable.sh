#!/usr/bin/env bash

source "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/../../../bash/common.lib.sh"

if [ "${TRAVIS_PULL_REQUEST}" = "false" ]; then
    exit 0 # Always execute full suite on branch builds
fi

if [ $(git diff --name-only HEAD origin/master | grep -c -e ^docs) -eq 0 ]; then
    print_header "Skipped suite" "Docs"
    print_warning "No changes detected in docs/*"
    exit 1
fi

exit 0
