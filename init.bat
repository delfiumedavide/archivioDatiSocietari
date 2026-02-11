@echo off
echo =============================================
echo   Archivio Dati Societari - Inizializzazione
echo   Gruppo di Martino
echo =============================================
echo.

echo [1/8] Checking .env file...
if not exist .env (
    copy .env.example .env
    echo    .env creato da .env.example
) else (
    echo    .env esistente, salto la copia
)

echo [2/8] Build Docker containers...
docker-compose build
if %ERRORLEVEL% neq 0 goto :error

echo [3/8] Starting containers...
docker-compose up -d
if %ERRORLEVEL% neq 0 goto :error

echo [4/8] Waiting for MySQL to be ready...
timeout /t 20 /nobreak >nul

echo [5/8] Fixing permissions...
docker exec archivio_app bash -c "mkdir -p /var/www/html/storage/framework/{cache/data,sessions,views} /var/www/html/storage/logs /var/www/html/storage/app/documents /var/www/html/bootstrap/cache && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache"

echo [6/8] Installing Composer dependencies and setting up Laravel...
docker exec archivio_app bash -c "cd /var/www/html && composer install --no-interaction --prefer-dist && php artisan key:generate && php artisan migrate --force && php artisan db:seed --force"
if %ERRORLEVEL% neq 0 goto :error

echo [7/8] Installing NPM dependencies...
docker exec archivio_app bash -c "cd /var/www/html && npm install"
if %ERRORLEVEL% neq 0 goto :error

echo [8/8] Building frontend assets...
docker exec archivio_app bash -c "cd /var/www/html && npm run build"
if %ERRORLEVEL% neq 0 goto :error

echo.
echo =============================================
echo   INSTALLAZIONE COMPLETATA!
echo =============================================
echo.
echo   App:     http://localhost:8080
echo   Mailhog: http://localhost:8025
echo.
echo   Login:    admin@gruppodimartino.it
echo   Password: Admin@2024!Secure
echo.
echo   (Cambia la password al primo accesso!)
echo =============================================
goto :end

:error
echo.
echo ERRORE: L'installazione ha riscontrato un problema.
echo Controlla i log con: docker-compose logs

:end
pause
