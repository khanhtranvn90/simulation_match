COMPOSE_FILE=docker-compose.yml

.PHONY: setup up down build restart shell redis-cli remove-all rebuild help

setup:
	@echo "Installing PHP dependencies inside backend container..."
	docker compose run --rm backend composer install --no-interaction --prefer-dist --optimize-autoloader

up: setup
	@echo "Starting Docker Compose services..."
	docker compose -f $(COMPOSE_FILE) up

down:
	@echo "Stopping and removing Docker Compose services..."
	docker compose -f $(COMPOSE_FILE) down

build:
	@echo "Building Docker Compose services..."
	docker compose -f $(COMPOSE_FILE) build

restart:
	@echo "Restarting Docker Compose services..."
	docker compose -f $(COMPOSE_FILE) down
	docker compose -f $(COMPOSE_FILE) up

shell:
	@echo "Accessing Backend container shell..."
	docker compose -f $(COMPOSE_FILE) exec backend bash

redis-cli:
	@echo "Accessing Redis CLI..."
	docker compose -f $(COMPOSE_FILE) exec redis redis-cli

remove-all:
	@echo "Removing all containers, images, and volumes..."
	@docker compose -f $(COMPOSE_FILE) down --rmi all --volumes --remove-orphans
	@echo "All resources removed."

rebuild: remove-all
	@echo "Rebuilding and starting Docker Compose services from scratch..."
	docker compose -f $(COMPOSE_FILE) build --no-cache
	docker compose -f $(COMPOSE_FILE) up -d
	@echo "Rebuild completed."

help:
	@echo "Available commands:"
	@echo "  make up        - Start Docker Compose services in detached mode"
	@echo "  make down      - Stop and remove Docker Compose services"
	@echo "  make build     - Build Docker Compose services"
	@echo "  make restart   - Restart Docker Compose services"
	@echo "  make shell     - Access Backend container shell"
	@echo "  make redis-cli - Access Redis CLI"
	@echo "  make remove-all - Remove all containers, images, and volumes"
	@echo "  make rebuild   - Remove all resources and rebuild from scratch"
	@echo "  make help      - Show this help message"