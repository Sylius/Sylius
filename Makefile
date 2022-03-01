init:
	composer install
	bin/console sylius:install -n
	node_modules/gulp/bin/gulp.js
