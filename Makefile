up:
	./vendor/bin/sail up -d

down:
	./vendor/bin/sail down

install:
	make composer-install
	make up
	make migrate
	make testing

migrate:
	./vendor/bin/sail artisan migrate:fresh
	./vendor/bin/sail artisan db:seed

composer-install:
	.docker/composer.sh composer install

testing:
	./vendor/bin/sail test

tinker:
	./vendor/bin/sail artisan tinker
