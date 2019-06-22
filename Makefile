ENV?=dev

help: ## Show help
	@echo
	@echo "Usage: make [target]"
	@echo
	@printf "\033[1;93mAvailable targets:\033[0m\n"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf " \033[36m%-30s\033[0m %s\n", $$1, $$2}'
	@echo

install: vendor node_modules build-assets ## Install all project dependencies

build-assets: ## Build JS, CSS and images
	node_modules/.bin/encore $(ENV)

vendor: composer.json composer.lock
	composer install

node_modules: package.json
	npm install

.PHONY: build-assets
