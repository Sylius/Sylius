init:
	composer install --no-interaction
	bin/console sylius:install -n
	yarn install --frozen-lockfile
	node_modules/gulp/bin/gulp.js

ci:
	composer install --no-interaction
	bin/console sylius:install -n
	yarn install --frozen-lockfile
	node_modules/gulp/bin/gulp.js
	vendor/bin/phpunit
