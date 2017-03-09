#!/usr/bin/env bash

source "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/../../../bash/common.lib.sh"

print_header "Activating memcached extension" "Sylius"
run_command "echo \"extension = memcached.so\" >> ~/.phpenv/versions/$(phpenv version-name)/etc/conf.d/travis.ini" || exit $?

print_header "Installing Yarn" "Sylius"
run_command "rm -rf ~/.nvm && git clone https://github.com/creationix/nvm.git ~/.nvm && (cd ~/.nvm && git checkout \`git describe --abbrev=0 --tags\`) && source ~/.nvm/nvm.sh && nvm install $TRAVIS_NODE_VERSION" || exit $?
run_command "curl -o- -L https://yarnpkg.com/install.sh | bash" || exit $?
