#!/usr/bin/env bash

source "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/../../../bash/common.lib.sh"

code=0

etc/bin/validate-packages || code=$?
etc/bin/test-packages || code=$?

exit ${code}
