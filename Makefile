up:
	docker compose --env-file .docker/.env up -d

init:
	composer install --no-interaction --no-scripts
	bin/console sylius:install -n
	yarn install --pure-lockfile
	node_modules/gulp/bin/gulp.js

ci:
	composer install --no-interaction --no-scripts
	bin/console sylius:install -n
	yarn install --pure-lockfile
	node_modules/gulp/bin/gulp.js
	vendor/bin/phpunit
	vendor/bin/phpspec run --ansi --no-interaction -f dot
	vendor/bin/behat --colors --strict --stop-on-failure --no-interaction -vvv -f progress
