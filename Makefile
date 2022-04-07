up:
	docker compose --env-file .docker/.env up -d

init:
	composer install --no-interaction --no-scripts
	bin/console doctrine:database:create --if-not-exists
	bin/console sylius:install --no-interaction
	bin/console sylius:fixtures:load default --no-interaction
	yarn install --pure-lockfile
	node_modules/gulp/bin/gulp.js

ci:
	composer install --no-interaction --no-scripts
	bin/console doctrine:database:create --if-not-exists
	bin/console sylius:install --no-interaction
	bin/console sylius:fixtures:load default --no-interaction
	bin/console cache:warmup
	yarn install --pure-lockfile
	node_modules/gulp/bin/gulp.js
	vendor/bin/phpunit
	vendor/bin/phpspec run --ansi --no-interaction -f dot
	vendor/bin/behat --colors --strict --no-interaction -vvv -f progress --tags="~@javascript&&@cli&&~@todo"  # CLI Behat
	vendor/bin/behat --colors --strict --no-interaction -vvv -f progress --tags="~@javascript&&~@cli&&~@todo" # NON JS Behat
	#vendor/bin/behat --colors --strict --no-interaction -vvv -f progress --tags="@javascript&&~@cli&&~@todo" # JS Behat

unit:
	vendor/bin/phpunit

spec:
	vendor/bin/phpspec run --ansi --no-interaction -f dot

behat:
	vendor/bin/behat --colors --strict --stop-on-failure --no-interaction -vvv -f progress
