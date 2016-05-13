#!/usr/bin/env bash
set -e

source "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/../../../bash/common.lib.sh"

print_header "Installing" "Sphinx"
run_command "pip install -q --user -r docs/requirements.txt"
