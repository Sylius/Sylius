# Makefile
prod:
    php app/console cache:clear --env=prod
    php app/console doctrine:database:create --env=prod
    php app/console doctrine:schema:create --env=prod

dev:
    php app/console cache:clear --env=dev
    php app/console doctrine:database:create --env=dev
    php app/console doctrine:schema:create --env=dev

test:
    php app/console cache:clear --env=test
    php app/console doctrine:database:create --env=test
    php app/console doctrine:schema:create --env=test
    phpunit -c app
    bin/phpspec run
    php app/console doctrine:database:drop --force --env=test