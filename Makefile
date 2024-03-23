login:
	docker exec -it php bash
setup:
	@make build
	@make up 
	@make composer-update
build:
	docker compose build --no-cache --force-rm
stop:
	docker compose stop
up:
	docker compose up -d
composer-update:
	docker exec  todoman-app bash -c "composer update"
migrate:
	docker exec  todoman-app bash -c "php artisan migrate"
rollback:
	docker exec  todoman-app bash -c "php artisan migrate:rollback"
admin:
	docker exec  todoman-app bash -c "php artisan db:seed"
db_todo:
	docker exec  todoman-app bash -c "php artisan make:migration create_todo_table"
model_todo: 
	docker exec  todoman-app bash -c "php artisan make:model todolistModel"
todolist_controller:
	docker exec  todoman-app bash -c "php artisan make:controller /todolist/todolistController"
login_controller:
	docker exec  todoman-app bash -c "php artisan make:controller /auth/loginController"
register_controller:
	docker exec  todoman-app bash -c "php artisan make:controller /auth/registerController"
	