.PHONY: help dockerize shell

help:
	@echo 'Available targets:'
	@echo '  make dockerize'
	@echo '  make shell'

dockerize:
	docker-compose down
	docker-compose up --build

shell:
	docker-compose exec wordpress bash
