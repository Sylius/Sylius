#!/usr/bin/env bash

source "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/../../../bash/common.lib.sh"

print_header "Uploading logs" "Sylius"
run_command "vendor/bin/upload-textfiles \"${SYLIUS_BUILD_DIR}/*.log\""
