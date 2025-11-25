.PHONY: install, deploy, compile

server 	:= 'ousseine'
domain 	:= 'sites/ousseine.fr'
sc		:= php bin/console

deploy:
	ssh $(server) 'cd $(domain) && git pull origin master && make install'

install: vendor/autoload.php compile
	#$(sc) doctrine:migrations:migrate -n
	#$(sc) importmap:install
	php composer dump-env prod
	APP_ENV=prod APP_DEBUG=0 $(sc) cache:clear
	APP_ENV=prod APP_DEBUG=0 $(sc) cache:warmup

vendor/autoload.php: composer.json composer.lock
	composer install --no-dev --optimize-autoloader
	touch vendor/autoload.php

compile:
	set -e
	$(sc) tailwind:build -m
	$(sc) asset-map:compile
	git add public/assets
	git commit -m "Compile assets Compile assets [$(shell date +%Y-%m-%d)]"
	git push --all
	rm -rf public/assets
	git add public/assets
	git commit -m "Clean local assets [$(shell date +%Y-%m-%d)]"
