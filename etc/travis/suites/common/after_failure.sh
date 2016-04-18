#!/usr/bin/env bash

source "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/../../../bash/common.lib.sh"

print_header "Uploading logs" "Sylius"
run_command "vendor/lakion/mink-debug-extension/travis/tools/upload-textfiles \"${SYLIUS_BUILD_DIR}/*.log\""

print_header "Uploading screenshots" "Sylius"
run_command "IMGUR_API_KEY=4907fcd89e761c6b07eeb8292d5a9b2a vendor/lakion/mink-debug-extension/travis/tools/upload-screenshots \"${SYLIUS_BUILD_DIR}/*.png\""
