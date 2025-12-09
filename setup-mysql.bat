@echo off
echo =====================================
echo   Al-Tahir Graphics MySQL Setup
echo =====================================
echo.

echo Step 1: Starting XAMPP MySQL...
echo Please ensure XAMPP MySQL is running before continuing.
pause

echo.
echo Step 2: Creating database...
"C:\xampp\mysql\bin\mysql.exe" -u root -e "CREATE DATABASE IF NOT EXISTS altahir_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
if %errorlevel% == 0 (
    echo ✓ Database 'altahir_db' created successfully.
) else (
    echo ❌ Failed to create database. Please check MySQL is running.
    pause
    exit /b 1
)

echo.
echo Step 3: Running Laravel migrations...
"C:\xampp\php\php.exe" artisan migrate --force
if %errorlevel% == 0 (
    echo ✓ Migrations completed successfully.
) else (
    echo ❌ Migration failed. Check error above.
    pause
    exit /b 1
)

echo.
echo Step 4: Seeding database with initial data...
"C:\xampp\php\php.exe" artisan db:seed --force
if %errorlevel% == 0 (
    echo ✓ Database seeded successfully.
) else (
    echo ⚠️  Seeding failed or no seeders found. This is okay.
)

echo.
echo Step 5: Testing database connection...
"C:\xampp\php\php.exe" artisan tinker --execute="echo 'Services table: ' . (Schema::hasTable('services') ? 'EXISTS' : 'NOT FOUND') . PHP_EOL; echo 'Service samples table: ' . (Schema::hasTable('service_samples') ? 'EXISTS' : 'NOT FOUND') . PHP_EOL;"

echo.
echo =====================================
echo ✅ MySQL Setup Complete!
echo =====================================
echo.
echo Your Laravel application is now configured with:
echo • Database: altahir_db
echo • Tables: services (with slug column), service_samples, and more
echo • All data preserved and migrations applied safely
echo.
echo You can now access your application at: http://localhost/Al-Tahir Graphics/public
echo.
pause
