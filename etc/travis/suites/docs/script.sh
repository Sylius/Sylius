#!/usr/bin/env bash

source "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/../../../bash/common.lib.sh"

code=0

print_header "Building" "Documentation"
run_command "sphinx-build -nWT docs docs/build" || code=$?
# Flags used here
#  -n   Run in nit-picky mode. Currently, this generates warnings for all missing references.
#  -W   Turn warnings into errors. This means that the build stops at the first warning and sphinx-build exits with exit status 1.
#  -T   Displays the full stack trace if an unhandled exception occurs.

if [[ ${code} != 0 ]]; then
    print_warning "Build failed, rerunning to show all the warnings and errors"
    run_command "sphinx-build -nT docs docs/build"
fi

exit ${code}
