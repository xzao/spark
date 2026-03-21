#
#	Makefile
#
.DEFAULT_GOAL := logs


#
#	dir[s]
#
$(shell mkdir -p mnt/etc/spark)
$(shell mkdir -p mnt/etc/spark/sparks)


#
#	env[s]
#
$(shell test -f .env || cp .env.sample .env)
include .env
export $(shell sed 's/=.*//' .env)


#
#	target[s]
#
%:
	@:

build:
	@docker compose build

develop:
	@php -S 0.0.0.0:8000 -t public

install:
	@sudo ln -sf $(shell pwd)/${SPARK_VOLUME_ETC_SPARK} /etc/spark

logs:
	@docker compose logs -f

restart:
	@make stop
	@make start

shell:
	@docker exec -it ${SPARK_CONTAINER_NAME} bash

start:
	@docker compose up -d --build

stop:
	@docker compose down

upgrade:
	@docker compose up -d --build --force-recreate

version:
	@NEW_VERSION="$(filter-out $@,$(MAKECMDGOALS))"; \
	if [ -z "$$NEW_VERSION" ]; then \
		echo "Usage: make version <version>"; \
		echo "Example: make version 1.0.0.0 or make version 1.0.0.0-rc1"; \
		exit 1; \
	fi; \
	if git rev-parse "v$$NEW_VERSION" >/dev/null 2>&1; then \
		echo "Error: Version tag v$$NEW_VERSION already exists."; \
		exit 1; \
	fi; \
	if [ -n "$$(git status --porcelain)" ]; then \
		echo "Error: Working directory is not clean. Please commit or stash your changes."; \
		git status --short; \
		exit 1; \
	fi; \
	echo "$$NEW_VERSION" > VERSION; \
	git add VERSION; \
	git commit -m "Version $$NEW_VERSION"; \
	git tag "v$$NEW_VERSION"; \
	echo "Successfully created version v$$NEW_VERSION"
