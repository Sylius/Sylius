#!/bin/bash

CURRENT_NODE_VERSION=$(docker run -i sylius node --version)
EXPECTED_NODE_VERSION="v14.19.0"

if [[ $CURRENT_NODE_VERSION != "$EXPECTED_NODE_VERSION" ]]; then
  echo "Invalid NODE version got $NODE_VERSION expected $EXPECTED_NODE_VERSION"
  exit 1
fi

exit 0
