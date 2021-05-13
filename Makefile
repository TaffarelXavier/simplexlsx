help-default help:
	@echo "======================================================================"
	@echo " OPÇÕES DO MAKEFILE"
	@echo "======================================================================"
	@echo "    dump: Executa o dump no banco de dados (para cliente MySQL 8)"
	@echo " restore: Executa o restore no banco de dados"
	@echo "  docker: Faz o dump e restaura o banco de dados no repositório docker"
	@echo "    exec: Executa o restore do banco de dados no repositório docker"
	@echo "    push: Executa o push do branch atual para os repositórios remotos"
	@echo "    pull: Executa o pull do repositório remoto para o branch atual"
	@echo "  commit: Adiciona os arquivos no índice e executa o commit"
	@echo "   start: php artisan serve"
	@echo "    test: Roda o PHPUnit no projeto"
	@echo "   phpcs: Roda o PHP Code Sniffer"
	@echo "   files: Faz o download das imagens presentes no servidor"
	@echo ""

dump:
	mysqldump --defaults-extra-file=.sqlpwd -B --add-drop-database opecsis --skip-lock-tables --skip-tz-utc --column-statistics=0 | pv -s 4M > database.sql

restore:
	pv database.sql | mysql -uroot -proot

docker:
	make dump
	make exec

exec:
	cat database.sql | pv | docker exec -i mysql-server /usr/bin/mysql -u root --init-command="SET autocommit=0;"

push:
	git push -u origin $(shell git rev-parse --abbrev-ref HEAD)

pull:
	git pull origin $(shell git rev-parse --abbrev-ref HEAD)

commit:
	git add .
	git commit -m "$(m)"

start:
	php artisan serve

test:
	docker exec opecsis-php ./vendor/bin/phpunit

phpcs:
	docker exec opecsis-php phpcs --standard=PSR2 --extensions=php app database tests

files:
	rsync -Cravzp --delete-after 45.79.92.163:/var/www/html/opecsis.com.br/storage/app/public/ storage/app/public/

open-vscode:
	sudo code . --user-data-dir