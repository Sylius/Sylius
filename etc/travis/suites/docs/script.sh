#!/usr/bin/env bash

source "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/../../../bash/common.lib.sh"

code=0

print_header "Building" "Documentation"
run_command "sphinx-build -nW -b html docs docs/build" || code=$?

if [[ ${code} != 0 ]]; then
    print_warning "Build failed, rerunning to show all the warnings and errors"
    run_command "sphinx-build -n -b html docs docs/build"
fi

exit ${code}
