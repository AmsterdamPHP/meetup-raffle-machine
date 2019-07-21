ENV?=dev
SHELL=bash

# set all to phony
.PHONY: *

help: ## Show help
	@echo
	@echo "Usage: make [target]"
	@echo
	@printf "\033[1;93mAvailable targets:\033[0m\n"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf " \033[36m%-30s\033[0m %s\n", $$1, $$2}'
	@echo


mkfile_path := $(abspath $(lastword $(MAKEFILE_LIST)))
current_dir := $(abspath $(patsubst %/,%,$(dir $(mkfile_path))))

PROJECT_NAME=amsterdamphp-raffler
IMAGE_TAG=usabillabv/php

DOCKER_RUN=docker run --rm -t \
	-v "${current_dir}:/opt/project" -v "${HOME}/.config/composer:/.config/composer" \
	-e COMPOSER_HOME=/.config/composer \
	--workdir=/opt/project \
	"${IMAGE_TAG}:${PROJECT_NAME}-dev"

all: install check up ## Runs everything

install: docker-build  node_modules build-assets

check: docker-lint

test: unit-tests

export DOCKER_BUILDKIT=1
docker-build:
	docker build -t "${IMAGE_TAG}:${PROJECT_NAME}-dev" --target=dev -f docker/Dockerfile-fpm .

docker-check: # If the docker image isn't built, do it
ifeq ($(shell docker images --filter=reference="${IMAGE_TAG}:${PROJECT_NAME}-dev" --format={{.ID}}),)
	$(MAKE) docker-build
endif

docker-lint: # Lints Dockerfile with Hadolint
	docker run --rm -t -v $(current_dir):/p --workdir=/p hadolint/hadolint:latest-debian hadolint ./docker/Dockerfile-fpm

PORT?=8081
up: docker-check # Runs Nginx and FPM containers while sharing the socket among them
	@docker-compose up -d
	@docker run --name "${PROJECT_NAME}-fpm" --rm -d -m 256M --network amsphpraffler --env-file=./.env -v $(current_dir):/opt/project:rw "${IMAGE_TAG}:${PROJECT_NAME}-dev"
	@docker run --name "${PROJECT_NAME}-nginx" --rm -d -m 64M --network amsphpraffler --env-file=./.env -p ${PORT}:80 --volumes-from="${PROJECT_NAME}-fpm" "${IMAGE_TAG}:nginx" || ($(MAKE) down && exit 1)
	@echo -e "\033[0;32m [RUNNING] \033[0m       \033[0;33m http://localhost:${PORT}/\033[0m"

down: # Stop the containers
	@docker stop -t 2 "${PROJECT_NAME}-nginx" || true
	@docker stop -t 5 "${PROJECT_NAME}-fpm" || true
	@docker-compose down
	@echo -e "\033[0;32m [STOPPED] \033[0m\033[0m" $<

install-composer: docker-check ## Install dependencies with composer
	$(DOCKER_RUN) composer install --prefer-dist -n -o $(FLAGS)


build-assets: ## Build JS, CSS and images
	node_modules/.bin/encore $(ENV)

node_modules: package.json
	npm install

unit-tests: ## Run unit tests
	vendor/bin/phpunit
