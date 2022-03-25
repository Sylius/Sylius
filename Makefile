up:
	docker compose --env-file .docker/.env up -d

init:
	composer install --no-interaction --no-scripts
	bin/console sylius:install --no-interaction
	bin/console sylius:fixtures:load default --no-interaction
	yarn install --pure-lockfile
	node_modules/gulp/bin/gulp.js

browser-validation:
	vendor/bin/behat features/account/customer_account/address_book/adding_address_validation.feature

check-connection:
	curl -H Host:app.sylius.localhost http://chromium:9222/json/version

ci:
	composer install --no-interaction --no-scripts
	bin/console sylius:install --no-interaction
	bin/console sylius:fixtures:load default --no-interaction
	yarn install --pure-lockfile
	node_modules/gulp/bin/gulp.js
	vendor/bin/phpunit
	vendor/bin/phpspec run --ansi --no-interaction -f dot
	vendor/bin/behat --stop-on-failure --colors --strict --no-interaction -vvv -f progress --tags="@javascript&&~@todo&&~@cli"  # CLI Behat
	vendor/bin/behat --stop-on-failure --colors --strict --no-interaction -vvv -f progress --tags="~@javascript&&~@todo&&~@cli" # NON JS Behat
	vendor/bin/behat --stop-on-failure --colors --strict --no-interaction -vvv -f progress --tags="@javascript&&~@todo&&~@cli"  # JS Behat

unit:
	vendor/bin/phpunit

spec:
	vendor/bin/phpspec run --ansi --no-interaction -f dot

behat:
	vendor/bin/behat --colors --strict --no-interaction -vvv -f progress --tags="@javascript&&~@todo&&~@cli"  # CLI Behat
	vendor/bin/behat --colors --strict --no-interaction -vvv -f progress --tags="~@javascript&&~@todo&&~@cli" # NON JS Behat
	vendor/bin/behat --colors --strict --no-interaction -vvv -f progress --tags="@javascript&&~@todo&&~@cli"  # JS Behat
