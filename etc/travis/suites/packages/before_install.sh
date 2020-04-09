#!/usr/bin/env bash
set -e

# Make swap
#https://getcomposer.org/doc/articles/troubleshooting.md#proc-open-fork-failed-errors
sudo fallocate -l 4G /var/swap.1
sudo /sbin/mkswap /var/swap.1
sudo /sbin/swapon /var/swap.1

source "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/../../../bash/common.lib.sh"

# To be able to install sylius/* packages by symlinking
run_command "git branch master 2> /dev/null || true"
