init:
	composer install
	bin/console sylius:install -n
	yarn install
	node_modules/gulp/bin/gulp.js

ci:
	composer install
	bin/console sylius:install -n
	yarn install
	node_modules/gulp/bin/gulp.js
	vendor/bin/phpunit --debug
