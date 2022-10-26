run:
	docker run -d -p 6379:6379 redis
	docker run --network=host --env-file=.env -v $(CURDIR):/usr/src/atm -it redis-demo bash
build:
	docker build . -t redis-demo

configure-debug:
	export PHP_IDE_CONFIG="serverName=redis"

redis-cli:
#как передать параметр сюда
	docker exec -it $1 redis-cli