#!/usr/bin/env bash

source "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/../../../bash/common.lib.sh"

if [ "${TRAVIS_PULL_REQUEST}" = "false" ]; then
    exit 0 # Always execute full suite on branch builds
fi

git fetch origin ${TRAVIS_BRANCH}
git branch
git branch -r
git remote -v

if [ $(git diff --name-only HEAD origin/${TRAVIS_BRANCH} | egrep -c -v -e ^docs -e ^LICENSE -e ^README.md -e "^CHANGELOG-[\d\.]+.md" -e "^UPGRADE-[\d\.]+.md") -eq 0 ]; then
    print_header "Skipped suite" "Application"
    print_warning "No other changes than those in docs & metafiles were found"
    exit 1
fi

exit 0
