#!/usr/bin/env bash
set -e

source "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/../../../bash/common.lib.sh"

code=0

print_header "Installing" "Sphinx"
run_command "pip install --user sphinx" || code=$?

etc/bin/install-packages || code=$?

exit ${code}
