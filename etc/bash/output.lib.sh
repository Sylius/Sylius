#!/usr/bin/env bash

# Argument 1: Text
bold() {
    echo "\e[1m$1\e[20m"
}

# Argument 1: Text
bold_green() {
    echo "\e[33;1m$1\e[0;20m"
}

# Argument 1: Text
red() {
    echo "\e[31m$1\e[0m"
}

# Argument 1: Text
print_error() {
    echo -e "$(red "$1")" 1>&2
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
