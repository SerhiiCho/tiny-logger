stan:
	./vendor/bin/phpstan analyse

cs:
	./vendor/bin/phpcs src logger.php --colors -p

test:
	./vendor/bin/phpunit --colors

check:
	make cs
	make stan
	make phpunit