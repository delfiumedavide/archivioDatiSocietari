.PHONY: up down build init migrate seed fresh test logs shell npm-build npm-dev key cache-clear

# Start all containers
up:
	docker-compose up -d

# Stop all containers
down:
	docker-compose down

# Build/rebuild containers
build:
	docker-compose build --no-cache

# Full initialization (first run)
init: build up composer-install key npm-install npm-build migrate seed
	@echo ""
	@echo "============================================="
	@echo "  Archivio Dati Societari - Pronto!"
	@echo "============================================="
	@echo "  App:     http://localhost:8080"
	@echo "  Mailhog: http://localhost:8025"
	@echo ""
	@echo "  Login: admin@gruppodimartino.it"
	@echo "  Password: Admin@2024!Secure"
	@echo "============================================="

# Install Composer dependencies
composer-install:
	docker-compose exec app composer install --no-interaction --prefer-dist

# Generate app key
key:
	docker-compose exec app php artisan key:generate

# Install NPM dependencies
npm-install:
	docker-compose exec app npm install

# Build frontend assets
npm-build:
	docker-compose exec app npm run build

# Run Vite dev server
npm-dev:
	docker-compose exec app npm run dev

# Run migrations
migrate:
	docker-compose exec app php artisan migrate --force

# Run seeders
seed:
	docker-compose exec app php artisan db:seed --force

# Fresh migration + seed
fresh:
	docker-compose exec app php artisan migrate:fresh --seed --force

# Run tests
test:
	docker-compose exec app php artisan test

# View logs
logs:
	docker-compose logs -f

# App logs only
app-logs:
	docker-compose logs -f app

# Shell into app container
shell:
	docker-compose exec app bash

# Clear all caches
cache-clear:
	docker-compose exec app php artisan cache:clear
	docker-compose exec app php artisan config:clear
	docker-compose exec app php artisan route:clear
	docker-compose exec app php artisan view:clear

# Optimize for production
optimize:
	docker-compose exec app php artisan config:cache
	docker-compose exec app php artisan route:cache
	docker-compose exec app php artisan view:cache

# Check document expirations manually
check-expirations:
	docker-compose exec app php artisan documents:check-expirations

# MySQL shell
mysql:
	docker-compose exec mysql mysql -u archivio_user -p archivio_societario

# Redis CLI
redis:
	docker-compose exec redis redis-cli
