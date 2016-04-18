#!/usr/bin/env bash

source "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/../../../bash/common.lib.sh"

print_header "Removing Symfony CMF vendors" "Sylius"
run_command "yes 'Y' | rm -fr vendor/symfony-cmf/create-bundle/Resources/public/vendor/*"
