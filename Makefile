start:
	php -S localhost:8000 public/index.php
install:
	composer install
lint:
	composer phpcs public

lint-fix:
	composer phpcbf public