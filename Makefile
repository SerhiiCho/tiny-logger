stan:
	./vendor/bin/phpstan analyse

cs:
	./vendor/bin/phpcs src logger.php --colors -p

test:
	./vendor/bin/phpunit --colors

check:
	make cs
	make stan
	make test

push:
	make check
	git pull origin HEAD
	git push origin HEAD