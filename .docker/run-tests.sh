#!/bin/bash

CURRENT_NODE_VERSION=$(docker run -i sylius node --version)
EXPECTED_NODE_VERSION="v14.19.0"

if [[ $CURRENT_NODE_VERSION != "$EXPECTED_NODE_VERSION" ]]; then
    echo "Invalid NODE version got $CURRENT_NODE_VERSION expected $EXPECTED_NODE_VERSION"
    exit 1
else
    echo "NODE version: $CURRENT_NODE_VERSION"
fi

CURRENT_PHP_VERSION=$(docker run -i sylius php -r 'echo phpversion();')
EXPECTED_PHP_VERSION="8.0.16"

if [[ $CURRENT_PHP_VERSION != "$EXPECTED_PHP_VERSION" ]]; then
    echo "Invalid PHP version got $CURRENT_PHP_VERSION expected $EXPECTED_PHP_VERSION"
    exit 1
else
    echo "PHP version: $CURRENT_PHP_VERSION"
fi

exit 0
