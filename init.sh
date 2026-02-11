#!/bin/bash
echo "============================================="
echo "  Archivio Dati Societari - Inizializzazione"
echo "  Gruppo di Martino"
echo "============================================="
echo ""

set -e

# 1. Check .env
if [ ! -f .env ]; then
    echo "[1/8] Creazione .env da .env.example..."
    cp .env.example .env
else
    echo "[1/8] .env esistente, salto la copia"
fi

# 2. Build
echo "[2/8] Build Docker containers..."
docker compose build

# 3. Start
echo "[3/8] Starting containers..."
docker compose up -d

# 4. Wait for MySQL
echo "[4/8] Waiting for MySQL to be ready..."
sleep 20

# 5. Fix permissions
echo "[5/8] Fixing permissions..."
docker exec archivio_app bash -c "mkdir -p /var/www/html/storage/framework/{cache/data,sessions,views} /var/www/html/storage/logs /var/www/html/storage/app/documents /var/www/html/bootstrap/cache && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache"

# 6. Laravel setup
echo "[6/8] Installing dependencies and setting up Laravel..."
docker exec archivio_app bash -c "cd /var/www/html && composer install --no-interaction --prefer-dist && php artisan key:generate && php artisan migrate --force && php artisan db:seed --force"

# 7. NPM
echo "[7/8] Installing NPM dependencies..."
docker exec archivio_app bash -c "cd /var/www/html && npm install"

# 8. Build frontend
echo "[8/8] Building frontend assets..."
docker exec archivio_app bash -c "cd /var/www/html && npm run build"

echo ""
echo "============================================="
echo "  INSTALLAZIONE COMPLETATA!"
echo "============================================="
echo ""
echo "  App:     http://localhost:8080"
echo "  Mailhog: http://localhost:8025"
echo ""
echo "  Login:    admin@gruppodimartino.it"
echo "  Password: Admin@2024!Secure"
echo ""
echo "  (Cambia la password al primo accesso!)"
echo "============================================="
