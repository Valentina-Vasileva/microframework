start:
	php -S localhost:8000
install:
	composer install
lint:
	composer phpcs

lint-fix:
	composer phpcbf