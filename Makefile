init:
	composer install --no-interaction
	bin/console sylius:install -n
	yarn install --pure-lockfile
	node_modules/gulp/bin/gulp.js

ci:
	composer install --no-interaction
	bin/console sylius:install -n
	yarn install --pure-lockfile
	node_modules/gulp/bin/gulp.js
	vendor/bin/phpunit
	vendor/bin/behat --colors --strict --no-interaction -vvv --tags="@cli&&~@todo"
	vendor/bin/behat --colors --strict --no-interaction -vvv --tags="~@javascript&&~@todo&&~@cli"
