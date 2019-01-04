#!/usr/bin/env bash
set -e

source "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/../../../bash/common.lib.sh"

print_header "Installing" "Python & Sphinx"
run_command "docker build -t sylius-docs docs"
