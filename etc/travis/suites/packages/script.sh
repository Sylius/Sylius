#!/usr/bin/env bash

source "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/../../../bash/common.lib.sh"

code=0

bin/validate-packages || code=$?
bin/test-packages || code=$?

exit ${code}
