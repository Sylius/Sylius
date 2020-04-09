#!/usr/bin/env bash
set -e

source "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/../../../bash/common.lib.sh"

# To be able to install sylius/* packages by symlinking
run_command "git branch master 2> /dev/null || true"
