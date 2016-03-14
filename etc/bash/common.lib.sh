#!/usr/bin/env bash

# Argument 1: Command to run
run_command() {
    echo "> $1"

    eval "$1"
}

retry_run_command() {
    run_command "$1"

    if [ "$?" != "0" ]; then
        run_command "$1"
    fi
}

# Argument 1: String to hash
text_md5sum() {
    echo "$1" | md5sum | awk '{ print $1 }'
}

# Argument 1: File to hash
file_md5sum() {
    md5sum "$1" | awk '{ print $1 }'
}

# Argument 1: Binary name
get_binary()
{
    if [ -x "bin/$1" ]; then
        echo "bin/$1"
    elif [ -x "vendor/bin/$1" ]; then
        echo "vendor/bin/$1"
    else
        return 1
    fi
}

get_number_of_jobs_for_parallel()
{
    local jobs="100%"

    if [[ "${TRAVIS}" = "true" ]]; then
        jobs="2"
    fi

    echo "${jobs}"
}

get_sylius_path()
{
    echo "$(cd "$(dirname "${BASH_SOURCE[0]}")/../../" && pwd)"
}
