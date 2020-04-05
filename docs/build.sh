#!/usr/bin/env bash

set -x

code=0

sphinx-build -nWT . build || code=$?
# Flags used here
#  -n   Run in nit-picky mode. Currently, this generates warnings for all missing references.
#  -W   Turn warnings into errors. This means that the build stops at the first warning and sphinx-build exits with exit status 1.
#  -T   Displays the full stack trace if an unhandled exception occurs.

if [[ ${code} != 0 ]]; then
    (>&2 echo "Build failed, rerunning to show all the warnings and errors")
    sphinx-build -nT . build
fi

exit ${code}
