@echo off
echo ========================================
echo    PICTORTS DATABASE RECOVERY
echo ========================================
echo.
echo This will fix your database issues:
echo 1. Add persistent storage to prevent data loss
echo 2. Create admin users for login
echo 3. Seed basic data
echo.
echo IMPORTANT: This will restart your containers!
echo.
pause

echo.
echo Step 1: Stopping containers...
docker-compose down

echo.
echo Step 2: Starting containers with new persistent storage...
docker-compose up -d

echo.
echo Step 3: Waiting for database to be ready...
timeout /t 10 /nobreak

echo.
echo Step 4: Running migrations...
docker exec pictorts_app php artisan migrate --force

echo.
echo Step 5: Creating admin users...
docker exec pictorts_app php artisan db:seed --class=DatabaseSeeder --force

echo.
echo Step 6: Clearing cache...
docker exec pictorts_app php artisan config:clear
docker exec pictorts_app php artisan cache:clear

echo.
echo ========================================
echo RECOVERY COMPLETE!
echo ========================================
echo.
echo You can now login with:
echo.
echo ADMIN LOGIN:
echo Email: admin@pictorts.com
echo Password: admin123
echo.
echo USER LOGIN:
echo Email: user@pictorts.com  
echo Password: user123
echo.
echo Access URL: http://10.10.26.232:8000
echo.
echo Your data will now persist even if containers restart!
echo.
pause
