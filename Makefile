start:
	@sudo docker-compose up  -d --build --remove-orphans

stop:
	@sudo docker-compose down
