default: build

build:
	bin/build.sh

clean:
	@if [ -d ".git" ]; then git clean -xdf --exclude ".env" --exclude ".idea" --exclude "web/app/uploads"; fi

deep-clean:
	@if [ -d ".git" ]; then git clean -xdf --exclude ".idea"; fi

run:
	docker-compose up

# Open a bash shell on the running container
bash:
	docker-compose exec wordpress bash

# Within docker (run bash first); process a db import on the first .sql file found in the current directory
# Maybe add an admin user if needed
db:
	bin/local-db-import.sh

# Run tests
test:
	composer test
