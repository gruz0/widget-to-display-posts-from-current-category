.PHONY: help dockerize shell install_linters

help:
	@echo 'Available targets:'
	@echo '  make dockerize'
	@echo '  make shell'
	@echo '  make install_linters'

dockerize:
	docker-compose down
	docker-compose up --build

shell:
	docker-compose exec wordpress bash

install_linters:
	bin/install_linters_dependencies.sh
