init:
	composer install
	bin/console sylius:install -n
	yarn install
	node_modules/gulp/bin/gulp.js
