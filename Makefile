phpunit:
	vendor/bin/phpunit

phpspec:
	vendor/bin/phpspec run --ansi --no-interaction -f dot

phpstan:
	vendor/bin/phpstan analyse

psalm:
	vendor/bin/psalm

behat-cli:
	vendor/bin/behat --colors --strict --no-interaction -vvv -f progress --tags="~@javascript&&@cli&&~@todo"

behat-non-js:
	vendor/bin/behat --colors --strict --no-interaction -vvv -f progress --tags="~@javascript&&~@cli&&~@todo"

behat-js:
	vendor/bin/behat --colors --strict --no-interaction -vvv -f progress --tags="@javascript&&~@cli&&~@todo"

install:
	composer install --no-interaction --no-scripts

backend:
	composer install --no-interaction --no-scripts
	bin/console sylius:install --no-interaction
	bin/console sylius:fixtures:load default --no-interaction

frontend:
	yarn install --pure-lockfile
	GULP_ENV=prod yarn build

docker:
	docker compose up -d --build
	docker compose exec node make frontend
	docker compose exec php make backend

behat: behat-cli behat-non-js behat-js

init: install backend frontend

ci: init phpstan psalm phpunit phpspec behat

integration: init phpunit behat

static: install phpspec phpstan psalm

# Example execution: make profile url=http://app
profile:
	docker compose exec blackfire blackfire curl -L $(url)
