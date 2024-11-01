PHPUNITDIR=tests

.PHONY: phpunit phpunit-help

phpunit:
	@echo ">>> Running project unit tests"
	./vendor/bin/phpunit
test: phpunit

phpunit-clean:
	rm -rf build/logs
	rm -rf build/report
	rm -rf build/tmp
clean: phpunit-clean

phpunit-help:
	@echo "- phpunit (< test): run PHPUnit to perform unit tests."
help: phpunit-help
