############################################################
# PROJECT ##################################################
############################################################
.PHONY: project
project: install setup

.PHONY: init
init:
	cp config/local.neon.example config/local.neon

.PHONY: install
install:
	composer install

.PHONY: setup
setup:
	mkdir -p var/tmp var/log
	chmod +0777 var/tmp var/log

.PHONY: clean
clean:
	find var/tmp -mindepth 1 ! -name '.gitignore' -type f,d -exec rm -rf {} +
	find var/log -mindepth 1 ! -name '.gitignore' -type f,d -exec rm -rf {} +

############################################################
# DEVELOPMENT ##############################################
############################################################
.PHONY: qa
qa: cs phpstan

.PHONY: cs
cs:
	vendor/bin/codesniffer app tests

.PHONY: csf
csf:
	vendor/bin/codefixer app tests

.PHONY: phpstan
phpstan:
	vendor/bin/phpstan analyse -c phpstan.neon --memory-limit=512M

.PHONY: tests
tests:
	vendor/bin/phpunit -c phpunit.xml --stderr --testsuite App.Unit
	vendor/bin/phpunit -c phpunit.xml --stderr --testsuite App.E2E

.PHONY: coverage
coverage:
	vendor/bin/phpunit -c phpunit.xml --coverage-text --colors=never
	vendor/bin/phpunit -c phpunit.xml --coverage-html var/tmp/tests/coverage --colors

.PHONY: dev
dev:
	NETTE_DEBUG=1 NETTE_ENV=dev php -S 0.0.0.0:8000 -t www

.PHONY: build
build:
	echo "OK"

############################################################
# DOCKER ###################################################
############################################################
.PHONY: docker-up
docker-up:
	docker compose up

############################################################
# DEPLOYMENT ###############################################
############################################################
.PHONY: deploy
deploy:
	$(MAKE) clean
	$(MAKE) project
	$(MAKE) build
	$(MAKE) clean
