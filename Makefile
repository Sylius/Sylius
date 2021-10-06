docker-install:
	docker-compose build
	docker-compose up -d
	docker-compose run php composer install
	docker-compose run php php bin/console sylius:install
	docker-compose run nodejs yarn install
	docker-compose run nodejs yarn build
	docker-compose up -d
	docker-compose start

docker-dev-restart:
	docker-compose stop
	docker-compose up -d

docker-dev-restart-php:
	docker-compose stop php
	docker-compose up -d php

docker-dev-reload:
	docker-compose up -d

docker-dev-shell:
	if [ "$(shell)" = "" ];then \
		make docker-shell service=php shell=zsh; \
	else \
		make docker-shell service=php shell=$(shell); \
	fi

docker-shell:
	docker-compose exec $(service) $(shell)