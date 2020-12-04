docker-up:
	docker-compose up -d

docker-down:
	docker-compose down

docker-build: app-perm
	docker-compose up --build -d

docker-check:
	docker ps -a

app-test:
	docker-compose exec php-cli vendor/bin/phpunit --color=always

app-perm:
	sudo chmod -R 0777 bootstrap/cache
	sudo chmod -R 0777 storage

app-composer-install:
	docker-compose run composer install

app-composer-update:
	docker-compose run composer update

app-migrate:
	docker-compose exec php-cli php artisan migrate

app-migration-refresh:
	docker-compose exec php-cli php artisan migrate:refresh

assets-install:
	docker-compose exec node yarn install

assets-rebuild:
	docker-compose exec node npm rebuild node-sass --force

assets-dev:
	docker-compose exec node yarn run dev

assets-watch:
	docker-compose exec node yarn run watch
