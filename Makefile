.PHONY: install qa cs csf phpstan tests coverage-clover coverage-html

install:
	composer install --no-interaction --no-progress --optimize-autoloader

qa: phpstan cs

cs:
	mkdir -p temp/phpcs
	vendor/bin/phpcs --standard=ruleset.xml app tests

csf:
	mkdir -p temp/phpcs
	vendor/bin/phpcbf --standard=ruleset.xml app tests

phpstan:
	vendor/bin/phpstan analyse -l 8 -v -c phpstan.neon app tests

tests:
	php vendor/bin/tester -C tests/cases

coverage-clover:
	php vendor/bin/tester -C tests/cases --coverage ./temp/coverage.xml --coverage-src app --coverage-src tests/src

coverage-html:
	php vendor/bin/tester -C tests/cases --coverage ./temp/coverage.html --coverage-src app --coverage-src tests/src
