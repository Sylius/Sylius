#!/usr/bin/env bash

locate_packages() {
    find "$(pwd)/src/Sylius" -mindepth 3 -maxdepth 3 -type f -name composer.json -exec dirname '{}' \;
}

find_packages() {
    locate_packages | package_path_to_package_name
}

# Argument 1: Package path
package_path_to_package_name() {
    basename "$1"
}

# Argument 1: Package name
package_name_to_package_path() {
    find "$(pwd)/src/Sylius" -mindepth 2 -maxdepth 2 -type d -name "$1"
}

# Argument 1: Package path or name
cast_package_argument_to_package_path() {
    local package_path="$1"

    if [ ! -d "${package_path}" ]; then
        package_path="$(package_name_to_package_path "$1")"
    fi

    if [[ -z "${package_path}" || ! -d "${package_path}" ]]; then
        return 1
    fi

    echo "${package_path}"
}

# Argument 1: Cache key
is_package_cache_fresh() {
    local current_hash cached_hash

    if [[ -f "${SYLIUS_CACHE_DIR}/composer-$1.lock" && -f "${SYLIUS_CACHE_DIR}/composer-$1.json.md5sum" ]]; then
        current_hash="$(file_md5sum "composer.json")"
        cached_hash="$(cat "${SYLIUS_CACHE_DIR}/composer-$1.json.md5sum")"

        if [ "${current_hash}" = "${cached_hash}" ]; then
            return 0
        fi
    fi

    return 1
}

# Argument 1: Package path or name
get_package_cache_key()
{
    text_md5sum "$(package_path_to_package_name "$1")"
}
