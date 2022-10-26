run:
	docker run --network=host --env-file=.env -v $(CURDIR):/usr/src/atm -it redis-demo bash
	docker run -d -p 6379:6379 redis
build:
	docker build . -t redis-demo

configure-debug:
	export PHP_IDE_CONFIG="serverName=redis"

run-worker:
	php worker.php
