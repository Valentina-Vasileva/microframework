start:
	php -S localhost:8000 public/index.php
install:
	composer install
lint:
	composer phpcs

lint-fix:
	composer phpcbf