@echo off
echo ========================================
echo    PICTORTS Database Viewer
echo ========================================
echo.
echo Choose what you want to view:
echo 1. View All Users
echo 2. View Recent Documents
echo 3. View Financial Records
echo 4. View Todo Items
echo 5. Exit
echo.
set /p choice="Enter your choice (1-5): "
echo.

if "%choice%"=="1" goto users
if "%choice%"=="2" goto documents
if "%choice%"=="3" goto financial
if "%choice%"=="4" goto todos
if "%choice%"=="5" goto end
goto start

:users
echo.
echo ========================================
echo        ALL USERS
echo ========================================
echo.
docker exec pictorts_db mysql -u root -proot -e "SELECT id, name, email, role, office_id FROM pictorts.users ORDER BY id;"
echo.
pause
goto start

:documents
echo.
echo ========================================
echo    RECENT DOCUMENTS (Last 10)
echo ========================================
echo.
docker exec pictorts_db mysql -u root -proot -e "SELECT id, dts_number, picto_number, subject, document_type, status, created_at FROM pictorts.documents ORDER BY created_at DESC LIMIT 10;"
echo.
pause
goto start

:financial
echo.
echo ========================================
echo    FINANCIAL RECORDS
echo ========================================
echo.
docker exec pictorts_db mysql -u root -proot -e "SELECT id, type, description, pr_number, pr_amount, status, created_at FROM pictorts.financial_records ORDER BY created_at DESC LIMIT 10;"
echo.
pause
goto start

:todos
echo.
echo ========================================
echo       TODO ITEMS
echo ========================================
echo.
docker exec pictorts_db mysql -u root -proot -e "SELECT id, title, status, priority, assigned_to, due_date FROM pictorts.todos ORDER BY created_at DESC LIMIT 10;"
echo.
pause
goto start

:end
echo.
echo Goodbye!
pause
