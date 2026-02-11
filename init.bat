@echo off
echo =============================================
echo   Archivio Dati Societari - Inizializzazione
echo   Gruppo di Martino
echo =============================================
echo.

echo [1/7] Build Docker containers...
docker-compose build
if %ERRORLEVEL% neq 0 goto :error

echo [2/7] Starting containers...
docker-compose up -d
if %ERRORLEVEL% neq 0 goto :error

echo [3/7] Waiting for MySQL to be ready...
timeout /t 15 /nobreak >nul

echo [4/7] Installing Composer dependencies...
docker-compose exec app composer install --no-interaction --prefer-dist
if %ERRORLEVEL% neq 0 goto :error

echo [5/7] Generating app key and running migrations...
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate --force
docker-compose exec app php artisan db:seed --force
if %ERRORLEVEL% neq 0 goto :error

echo [6/7] Installing NPM dependencies...
docker-compose exec app npm install
if %ERRORLEVEL% neq 0 goto :error

echo [7/7] Building frontend assets...
docker-compose exec app npm run build
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
