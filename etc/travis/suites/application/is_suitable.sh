#!/usr/bin/env bash

source "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/../../../bash/common.lib.sh"

if [ "${TRAVIS_PULL_REQUEST}" = "false" ]; then
    exit 0 # Always execute full suite on branch builds
fi

if [ $(git diff --name-only HEAD origin/master | grep -c -v -e ^docs -e ^CHANGELOG.md -e ^CONTRIBUTING.md -e ^LICENSE -e ^PULL_REQUEST_TEMPLATE.md -e ^README.md -e ^UPGRADE.md) -eq 0 ]; then
    print_header "Skipped suite" "Application"
    print_warning "No other changes than those in docs/* were found"
    exit 1
fi

exit 0
