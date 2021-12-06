#!/usr/bin/env bash

# Argument 1: Command to run
run_command() {
    echo "> $1"

    eval "$1"
}

# Argument 1: Command to run
run_command_reporting_status()
{
    local code=0

    run_command "$1" || code=$?

    if [ "${code}" = "0" ]; then
        print_success "Command \"$1\" exited with code ${code}\n"
    else
        print_error "Command \"$1\" exited with code ${code}\n"
    fi

    return ${code}
}

# Argument 1: Command to run
retry_run_command() {
    run_command "$1"

    if [ "$?" != "0" ]; then
        run_command "$1"
    fi
}

# Argument 1: Text
bold() {
    echo -e "\e[1m$1\e[0;20m"
}

# Argument 1: Text
bold_green() {
    echo -e "\e[33;1m$1\e[0;20m"
}

# Argument 1: Text
red() {
    echo -e "\e[31m$1\e[0m"
}

# Argument 1: Text
bold_red() {
    echo -e "\e[31;1m$1\e[0;20m"
}

# Argument 1: Text
print_error() {
    echo -e "$(bold_red "$1")" 1>&2
}

# Argument 1: Text
print_success() {
    echo -e "$(bold_green "$1")"
}

# Argument 1: Action
# Argument 2: Subject
print_header() {
    echo -e "$(bold "$1"): $(bold_green "$2")"
}

# Argument 1: Text
print_info() {
    echo "=> $1"
}

# Argument 1: Text
print_warning() {
    echo "=> $1" 1>&2
}

# Argument 1: Text
exit_on_error() {
    if [ "$?" != "0" ]; then
        print_error "$1"
        exit 1
    fi
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

get_sylius_path()
{
    echo "$(cd "$(dirname "${BASH_SOURCE[0]}")/../../" && pwd)"
}

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
