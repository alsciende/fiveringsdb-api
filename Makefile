#include .env

COMPOSER_FLAGS :=
CONSOLE_BIN    ?= bin/console
SYMFONY_ENV    ?= dev
COMPOSER_BIN   ?= composer
FIND_BIN       ?= find
PHP_BIN        ?= php
HOST           ?= localhost:8642

ifeq ($(SYMFONY_ENV),prod)
	COMPOSER_FLAGS := --optimize-autoloader --classmap-authoritative --no-dev --no-interaction
endif

.DEFAULT_GOAL := help
.PHONY: clean db phpstan rm start stop reset run test tf tu vendor fixtures docs

help:
	@echo
	@echo 'Main available targets are:'
	@echo '  reset   : Restart local server'
	@echo '  test    : Install vendors, configure project and run various tests'
	@echo '  docs    : Generate the documentation'
	@echo
	@echo 'Secondary targets are:'
	@echo '  clean     : Make your working dir virgin again'
	@echo '  db        : Recreate schema'
	@echo '  help      : This help message'
	@echo '  vendor    : Install vendors and configure project'
	@echo
	@echo 'See Makefile for a complete list.'
	@echo

start: fixtures
	@echo
	bin/console server:start $(HOST)

stop:
	@echo
	bin/console server:stop

clean: rm
	@echo
	rm -rf var/sessions/* vendor

vendor: composer.lock
	@echo
	-$(COMPOSER_BIN) install $(COMPOSER_FLAGS)

schema: vendor
	@echo
	$(CONSOLE_BIN) doctrine:schema:validate --skip-sync

db: vendor
	@echo
	$(CONSOLE_BIN) doctrine:database:drop --force
	$(CONSOLE_BIN) doctrine:database:create
	$(CONSOLE_BIN) doctrine:schema:update --force

fixtures: db
	@echo
	$(CONSOLE_BIN) doctrine:fixtures:load --append
	$(CONSOLE_BIN) app:data:import

phpstan: export SYMFONY_ENV = test
phpstan: vendor
	@echo
	$(PHP_BIN) vendor/bin/phpstan analyse src/AppBundle --configuration phpstan.neon --level 7

reset: stop start

rm:
	@echo
	rm -rf var/cache/* var/logs/*

test: export SYMFONY_ENV = test
test: schema phpstan tu tf

tf: export SYMFONY_ENV = test
tf: vendor db
	@echo
	vendor/bin/behat

tu: export SYMFONY_ENV = test
tu: vendor db fixtures
	@echo
	vendor/bin/phpunit

docs: documentation.apib
	@echo
	aglio -i documentation.apib -o docs/index.html --theme-variables slate
