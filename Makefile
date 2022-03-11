up:
	docker compose --env-file .docker/.env up -d

init:
	composer install --no-interaction --no-scripts
	bin/console sylius:install --no-interaction
	bin/console sylius:fixtures:load default --no-interaction
	yarn install --pure-lockfile
	node_modules/gulp/bin/gulp.js

ci:
	apt install wget
	wget -q -O - https://dl-ssl.google.com/linux/linux_signing_key.pub | apt-key add -
	echo 'deb [arch=amd64] http://dl.google.com/linux/chrome/deb/ stable main' | tee /etc/apt/sources.list.d/google-chrome.list
	apt update && apt install google-chrome-stable
	composer install --no-interaction --no-scripts
	bin/console sylius:install --no-interaction
	bin/console sylius:fixtures:load default --no-interaction
	yarn install --pure-lockfile
	node_modules/gulp/bin/gulp.js
	vendor/bin/phpunit
	vendor/bin/phpspec run --ansi --no-interaction -f dot
	vendor/bin/behat --colors --strict --stop-on-failure --no-interaction -vvv -f progress

unit:
	vendor/bin/phpunit

spec:
	vendor/bin/phpspec run --ansi --no-interaction -f dot

behat:
	vendor/bin/behat --colors --strict --stop-on-failure --no-interaction -vvv -f progress
