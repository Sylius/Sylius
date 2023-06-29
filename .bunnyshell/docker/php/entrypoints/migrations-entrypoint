#!/bin/sh
set -e

FIXTURES_INITIALIZED_FILE=/srv/sylius/public/media/.docker-initialized

attempts_left=20

php bin/console cache:clear

until php bin/console doctrine:query:sql "select 1" >/dev/null 2>&1;
do
    attempts_left=$((attempts_left-1))

    if [ "${attempts_left}" -eq "0" ]; then
        (>&2 echo "MySQL did not answer. Aborting migrations.")
        exit 1
    else
        (>&2 echo "Waiting for MySQL to be ready...")
    fi

    sleep 1
done

php bin/console doctrine:migrations:migrate --no-interaction

if [ "$LOAD_FIXTURES" = "1" ] && [ ! -f "$FIXTURES_INITIALIZED_FILE" ]; then
    # Replace localhost with BASE_DOMAIN in fixtures.yml
    if [ -z "$BASE_DOMAIN" ]; then
        sed -i "s/localhost/$BASE_DOMAIN/g" vendor/sylius/sylius/src/Sylius/Bundle/CoreBundle/Resources/config/app/fixtures.yml
    fi

    php bin/console sylius:fixtures:load --no-interaction

    # Generate image cache
    find public/media/image -type f -print0 | sed 's/public\/media\/image\///' | xargs -0 -I{} sh -c 'php bin/console liip:imagine:cache:resolve {} || true'

    touch "$FIXTURES_INITIALIZED_FILE"
fi
