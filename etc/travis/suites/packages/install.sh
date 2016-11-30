#!/usr/bin/env bash
set -e

source "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/../../../bash/common.lib.sh"

bin/install-packages
