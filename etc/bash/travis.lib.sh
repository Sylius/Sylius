#!/usr/bin/env bash

source "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/output.lib.sh"

has_sylius_cache() {
    if [[ ! -z "$SYLIUS_CACHE_DIR" && -d "$SYLIUS_CACHE_DIR" ]]; then
        return 0
    else
        return 1
    fi
}

inform_about_sylius_cache() {
    if ! has_sylius_cache; then
        print_warning "Sylius cache should be used, but it is not configured correctly."
        print_warning "Check whether you have \$SYLIUS_CACHE_DIR set and if that directory exists."
    fi
}
