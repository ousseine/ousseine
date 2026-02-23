.PHONY: install, deploy, compile

server 	:= 'fdm'
domain 	:= 'domains/ousseine.site/public_html/ousseine'
sc		:= php bin/console
vendor 	:= 'composer install --no-dev --optimize-autoloader && touch vendor/autoload.php'
clear  	:= 'APP_ENV=prod APP_DEBUG=0 $(sc) cache:clear && PP_ENV=prod APP_DEBUG=0 $(sc) cache:warmup'

deploy: compile
	ssh $(server) 'cd $(domain) && git pull origin master && $(sc) doctrine:migrations:migrate -n && $(vendor) && $(clear)'

compile:
	set -e
	$(sc) tailwind:build --minify
	$(sc) asset-map:compile
	git add public/assets
	git commit -m "Compile assets Compile assets [$(shell date +%Y-%m-%d)]"
	git push --all
	rm -rf public/assets
	git add public/assets
	git commit -m "Clean local assets [$(shell date +%Y-%m-%d)]"

#deploy: compile
#	ssh $(server) 'cd $(domain) && git pull origin master && make install'
#
#install: vendor/autoload.php
#	$(sc) doctrine:migrations:migrate -n
#	$(sc) importmap:install
#	composer dump-env prod
#	APP_ENV=prod APP_DEBUG=0 $(sc) cache:clear
#	APP_ENV=prod APP_DEBUG=0 $(sc) cache:warmup
#
#vendor/autoload.php: composer.json composer.lock
#	composer install --no-dev --optimize-autoloader
#	touch vendor/autoload.php

start:
	docker compose up -d
	symfony serve -d
	symfony console tailwind:build -w

stop:
	docker compose down
	symfony server:stop
