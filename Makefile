# List available commands
help:
	@awk '/^#/{c=substr($$0,3);next}c&&/^[[:alpha:]][[:alnum:]_-]+:/{print substr($$1,1,index($$1,":")),c}1{c=0}' $(MAKEFILE_LIST) | column -s: -t

# Run all tests
test: lint code-style behat phpstan

# Lint all php files
lint:
	vendor/bin/parallel-lint --exclude vendor/ .

# Check code for style problems
code-style: phpmd phpcs

# Check code for design problems
phpmd: phpmd.phar
	php phpmd.phar src/ xml phpmd.xml --suffixes php

# Check code adheres to PSR-2
phpcs:
	vendor/bin/phpcs --standard=PSR2 src/

# Run behat
behat:
	vendor/bin/behat

# Run phpstan
phpstan:
	vendor/bin/phpstan analyze src/ --level max

phpmd.phar:
	wget -c http://static.phpmd.org/php/latest/phpmd.phar > /dev/null

.PHONY: help test lint code-style phpmd phpcs behat phpstan
