#!/bin/sh
echo "Checking lexik configuration"

FILE=config/jwt/public.pem

if test -f "$FILE"; then
    echo "$FILE exists.\nExiting."
    else
    echo "$FILE does not exist. Preparing lexik configuration"
    bin/console lexik:jwt:generate-keypair
fi
