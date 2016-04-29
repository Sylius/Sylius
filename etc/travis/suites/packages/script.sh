#!/usr/bin/env bash

source "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/../../../bash/common.lib.sh"

code=0

print_header "Building" "Documentation"
run_command "sphinx-build -nW -b html docs docs/build" || code=$?

etc/bin/validate-packages || code=$?
etc/bin/test-packages || code=$?

exit ${code}
