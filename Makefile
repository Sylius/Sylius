docker-install:
	docker-compose run nodejs yarn install
	docker-compose run nodejs yarn build