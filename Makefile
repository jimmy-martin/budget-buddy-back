# Variables
DOCKER = docker
DOCKER_COMPOSE = docker-compose
EXEC = $(DOCKER) exec -w /var/www/project www_budget_buddy
PHP = $(EXEC) php
COMPOSER = $(EXEC) composer
NPM = $(EXEC) npm
SYMFONY_CONSOLE = $(PHP) bin/console

# Colors
GREEN = echo "\x1b[32m\#\# $1\x1b[0m"
RED = echo "\x1b[31m\#\# $1\x1b[0m"


## ———— 🔥 App ————
init: ## Init the project with docker
	$(MAKE) docker-start
	$(MAKE) composer-install
	@$(call GREEN,"The application is available at: http://127.0.0.1:8000/.")

cache-clear: ## Clear cache
	$(SYMFONY_CONSOLE) cache:clear

## ———— 🐳 Docker ————
start: ## Start app
	$(MAKE) docker-start
docker-start:
	$(DOCKER_COMPOSE) up -d
	@$(call GREEN,"The containers are now running.")

stop: ## Stop app
	$(MAKE) docker-stop
docker-stop:
	$(DOCKER_COMPOSE) stop
	@$(call RED,"The containers are now stopped.")

## ———— 🎻 Composer ————
composer-install: ## Install dependencies
	$(COMPOSER) install
	@$(call GREEN,"All dependencies are installed.")

composer-update: ## Update dependencies
	$(COMPOSER) update
	@$(call GREEN,"All dependencies are updated.")

## ———— 📊 Database ————
database-init: ## Init database
	$(MAKE) database-drop
	$(MAKE) database-create
	$(MAKE) database-migrate
	$(MAKE) database-fixtures-load
	@$(call GREEN,"The database is now initialized with migrations and fake data.")

database-drop: ## Drop database
	$(SYMFONY_CONSOLE) d:d:d --force --if-exists

database-create: ## Create database
	$(SYMFONY_CONSOLE) d:d:c --if-not-exists

database-migration: ## Make migration
	$(SYMFONY_CONSOLE) make:migration

migration: ## Alias for database-migration
	$(MAKE) database-migration

database-migrate: ## Migrate migrations
	$(SYMFONY_CONSOLE) d:m:m --no-interaction

migrate: ## Alias for database-migrate
	$(MAKE) database-migrate

database-fixtures-load: ## Load fixtures
	$(SYMFONY_CONSOLE) d:f:l --no-interaction

fixtures: ## Alias for database-fixtures-load
	$(MAKE) database-fixtures-load

## —— ✅ Test ——
.PHONY: tests
tests: ## Run all tests
	$(MAKE) database-init-test
	$(PHP) bin/phpunit --testdox tests/Unit/
	$(PHP) bin/phpunit --testdox tests/Functional/
	$(PHP) bin/phpunit --testdox tests/E2E/

database-init-test: ## Init database for test
	$(SYMFONY_CONSOLE) d:d:d --force --if-exists --env=test
	$(SYMFONY_CONSOLE) d:d:c --env=test
	$(SYMFONY_CONSOLE) d:m:m --no-interaction --env=test

unit-test: ## Run unit tests
	$(MAKE) database-init-test
	$(PHP) bin/phpunit --testdox tests/Unit/

functional-test: ## Run functional tests
	$(MAKE) database-init-test
	$(PHP) bin/phpunit --testdox tests/Functional/

## ———— 🛠️  Others ————
help: ## List of commands
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'



