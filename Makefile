test: unit

unit: composer_install
	phpunit

composer_install:
	composer install
