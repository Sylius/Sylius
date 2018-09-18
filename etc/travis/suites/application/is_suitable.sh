#!/usr/bin/env bash

source "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/../../../bash/common.lib.sh"

if [ "${TRAVIS_PULL_REQUEST}" = "false" ]; then
    exit 0 # Always execute full suite on branch builds
fi

if [ $(git diff --name-only HEAD origin/$TRAVIS_BRANCH | grep -c -v -e ^docs -e ^.github -e ^CHANGELOG -e ^LICENSE -e ^README -e ^UPGRADE) -eq 0 ]; then
    print_header "Skipped suite" "Application"
    print_warning "No other changes than those in docs/* were found"
    exit 1
fi

exit 0
