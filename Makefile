BACKEND := docker compose run --rm backend
FRONTEND := docker compose run --rm frontend

default: help

help: ## Lists all available commands
	@grep -E '^[a-zA-Z0-9_-]+:.*?## .*$$' $(MAKEFILE_LIST) | column	-c2 -t -s ':#' | sed -e 's/^// '

phpunit: ## Runs phpunit tests
	$(BACKEND) vendor/bin/phpunit

phpspec: ## Runs phpspec tests
	$(BACKEND) vendor/bin/phpspec run --ansi --no-interaction -f dot

phpstan: ## Runs phpstan static analysis
	$(BACKEND) vendor/bin/phpstan analyse

psalm: ## Runs psalm static analysis
	$(BACKEND) vendor/bin/psalm

behat-cli: ## Runs behat CLI tests
	$(BACKEND) vendor/bin/behat --colors --strict --no-interaction -vvv -f progress --tags="~@javascript&&@cli&&~@todo" || vendor/bin/behat --colors --strict --no-interaction -vvv -f progress --tags="~@javascript&&@cli&&~@todo" --rerun

behat-non-js: ## Runs behat non-JS tests
	$(BACKEND) vendor/bin/behat --colors --strict --no-interaction -vvv -f progress --tags="~@javascript&&~@cli&&~@todo" || vendor/bin/behat --colors --strict --no-interaction -vvv -f progress --tags="~@javascript&&~@cli&&~@todo" --rerun

behat-js: ## Runs behat JS tests
	$(BACKEND) vendor/bin/behat --colors --strict --no-interaction -vvv -f progress --tags="@javascript&&~@cli&&~@todo" || vendor/bin/behat --colors --strict --no-interaction -vvv -f progress --tags="@javascript&&~@cli&&~@todo" --rerun

install: ## Installs PHP dependencies
	$(BACKEND) composer install --no-interaction --no-scripts

backend: ## Prepare backend
	$(BACKEND) bin/console doctrine:database:create --if-not-exists
	$(BACKEND) bin/console sylius:install --no-interaction
	$(BACKEND) bin/console sylius:fixtures:load default --no-interaction

frontend: ## Prepare frontend
	$(FRONTEND) yarn install --pure-lockfile
	$(FRONTEND) yarn encore dev

profile: ## Profile a URL by blackfire. Example execution: make profile url=http://backend
	docker compose exec blackfire blackfire curl -L $(url)

behat: behat-cli behat-non-js behat-js

init: install backend frontend

ci: init phpstan psalm phpunit phpspec behat

integration: init phpunit behat-cli behat-non-js

static: install phpspec phpstan psalm
