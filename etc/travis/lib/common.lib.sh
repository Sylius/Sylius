#!/usr/bin/env bash

run_command() {
    echo "> $1"

    $1
}

get_package_name() {
    basename $1
}
