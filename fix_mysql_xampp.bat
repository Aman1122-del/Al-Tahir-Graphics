@echo off
echo =====================================
echo     XAMPP MySQL Startup Fix
echo =====================================
echo.

echo Step 1: Stopping all MySQL processes...
taskkill /F /IM mysqld.exe 2>NUL
taskkill /F /IM mysql.exe 2>NUL
echo MySQL processes stopped.

echo.
echo Step 2: Checking for port conflicts on 3306...
netstat -ano | findstr :3306
if %errorlevel% == 0 (
    echo WARNING: Port 3306 is in use by another process.
    echo Please identify and stop the process using port 3306.
    pause
)

echo.
echo Step 3: Cleaning up lock files...
del "C:\xampp\mysql\data\mysql.pid" 2>NUL
del "C:\xampp\mysql\data\mysqld.dmp" 2>NUL
echo Lock files cleaned.

echo.
echo Step 4: Fixing file permissions...
icacls "C:\xampp\mysql\data" /grant Everyone:(OI)(CI)F /T 2>NUL
echo Permissions fixed.

echo.
echo Step 5: Starting MySQL via XAMPP Control Panel...
echo Please manually start MySQL from XAMPP Control Panel now.
echo.
echo If MySQL still fails to start, try the following:
echo 1. Run this script as Administrator
echo 2. Temporarily disable antivirus real-time scanning
echo 3. Check if any backup software is running
echo 4. Restart your computer and try again
echo.

echo Step 6: Testing MySQL connection...
timeout /t 5 >nul
"C:\xampp\mysql\bin\mysql.exe" -u root -e "SELECT 'MySQL is working!' as status;" 2>NUL
if %errorlevel% == 0 (
    echo SUCCESS: MySQL is now running!
) else (
    echo MySQL connection test failed. Please check XAMPP Control Panel.
)

echo.
echo =====================================
echo Fix script completed. 
echo Check XAMPP Control Panel to verify MySQL status.
echo =====================================
pause
