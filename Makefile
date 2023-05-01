help: ## Show this message
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

phpcs: ## Run PHP CS Fixer
	./vendor/bin/php-cs-fixer fix src
	./vendor/bin/php-cs-fixer fix tests

test: ## Run code tests
	./vendor/bin/phpunit --testdox

phpstan:
	./vendor/bin/phpstan analyse src --level=9

test-phpcs: ## Run coding standard tests
	./vendor/bin/php-cs-fixer --diff --dry-run --using-cache=no -v fix src

all: ## Run all DX tools
all: phpcs phpstan test

.PHONY: test test-phpcs phpstan
