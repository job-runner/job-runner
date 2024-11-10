.PHONY: php

SUPPORTED_COMMANDS := php composer cube43 test test-and-bdd coverage cs static cbf infection psalm
SUPPORTS_MAKE_ARGS := $(findstring $(firstword $(MAKECMDGOALS)), $(SUPPORTED_COMMANDS))
ifneq "$(SUPPORTS_MAKE_ARGS)" ""
  COMMAND_ARGS := $(wordlist 2,$(words $(MAKECMDGOALS)),$(MAKECMDGOALS))
  COMMAND_ARGS := $(subst :,\:,$(COMMAND_ARGS))
  $(eval $(COMMAND_ARGS):;@:)
endif

is-valid: test cs psalm

dup:
	docker compose up -d

kill:
	docker compose rm -f -s


test:
	docker compose exec php php -dpcov.enabled=0 vendor/bin/phpunit $(COMMAND_ARGS)

cs:
	docker compose exec php ./vendor/bin/phpcs

cbf:
	docker compose exec php ./vendor/bin/phpcbf

psalm:
	docker compose exec php ./vendor/bin/psalm

infections:
	docker compose exec php ./vendor/bin/infection -s

coverage: dup
	docker compose exec php ./vendor/bin/phpunit --coverage-html coverage

update: dup
	docker compose exec php composer update

install: dup
	docker compose exec php composer install



composer-valid: dup
	docker compose exec php composer validate

login:
	docker compose exec php sh