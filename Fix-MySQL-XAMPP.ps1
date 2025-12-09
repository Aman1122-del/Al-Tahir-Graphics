# XAMPP MySQL Startup Fix Script (PowerShell)
# Run as Administrator for best results

Write-Host "=====================================`n    XAMPP MySQL Startup Fix`n=====================================" -ForegroundColor Green

# Step 1: Stop MySQL processes
Write-Host "`nStep 1: Stopping all MySQL processes..." -ForegroundColor Yellow
Get-Process -Name "mysqld" -ErrorAction SilentlyContinue | Stop-Process -Force
Get-Process -Name "mysql" -ErrorAction SilentlyContinue | Stop-Process -Force
Write-Host "MySQL processes stopped." -ForegroundColor Green

# Step 2: Check port conflicts
Write-Host "`nStep 2: Checking for port conflicts on 3306..." -ForegroundColor Yellow
$port3306 = Get-NetTCPConnection -LocalPort 3306 -ErrorAction SilentlyContinue
if ($port3306) {
    Write-Host "WARNING: Port 3306 is in use by another process." -ForegroundColor Red
    Write-Host "Process ID using port 3306:" -ForegroundColor Red
    $port3306 | Format-Table LocalAddress, LocalPort, OwningProcess
} else {
    Write-Host "Port 3306 is available." -ForegroundColor Green
}

# Step 3: Clean up lock files
Write-Host "`nStep 3: Cleaning up lock files..." -ForegroundColor Yellow
Remove-Item "C:\xampp\mysql\data\mysql.pid" -ErrorAction SilentlyContinue
Remove-Item "C:\xampp\mysql\data\mysqld.dmp" -ErrorAction SilentlyContinue
Write-Host "Lock files cleaned." -ForegroundColor Green

# Step 4: Fix file permissions
Write-Host "`nStep 4: Fixing file permissions..." -ForegroundColor Yellow
try {
    icacls "C:\xampp\mysql\data" /grant "Everyone:(OI)(CI)F" /T
    Write-Host "Permissions fixed." -ForegroundColor Green
} catch {
    Write-Host "Permission fix failed. Please run as Administrator." -ForegroundColor Red
}

# Step 5: Remove temporary files that might cause issues
Write-Host "`nStep 5: Removing temporary InnoDB files..." -ForegroundColor Yellow
Remove-Item "C:\xampp\mysql\data\ibtmp1" -ErrorAction SilentlyContinue
Write-Host "Temporary files cleaned." -ForegroundColor Green

Write-Host "`n=====================================" -ForegroundColor Green
Write-Host "NEXT STEPS:" -ForegroundColor Yellow
Write-Host "1. Open XAMPP Control Panel as Administrator" -ForegroundColor White
Write-Host "2. Click 'Start' next to MySQL" -ForegroundColor White
Write-Host "3. If it still fails, check the suggestions below" -ForegroundColor White
Write-Host "`nCOMMON SOLUTIONS:" -ForegroundColor Yellow
Write-Host "• Temporarily disable antivirus real-time scanning" -ForegroundColor White
Write-Host "• Close any backup software that might access MySQL files" -ForegroundColor White
Write-Host "• Restart your computer if the issue persists" -ForegroundColor White
Write-Host "• Check Windows Defender exclusions for C:\xampp folder" -ForegroundColor White
Write-Host "=====================================" -ForegroundColor Green

# Test MySQL after manual start
Write-Host "`nWaiting for you to start MySQL manually..." -ForegroundColor Yellow
Read-Host "Press Enter after starting MySQL from XAMPP Control Panel"

Write-Host "`nTesting MySQL connection..." -ForegroundColor Yellow
try {
    $result = & "C:\xampp\mysql\bin\mysql.exe" -u root -e "SELECT 'MySQL is working!' as status;" 2>$null
    if ($LASTEXITCODE -eq 0) {
        Write-Host "SUCCESS: MySQL is now running!" -ForegroundColor Green
    } else {
        Write-Host "MySQL connection test failed. Check XAMPP Control Panel." -ForegroundColor Red
    }
} catch {
    Write-Host "Could not test MySQL connection. Check if it's running in XAMPP Control Panel." -ForegroundColor Yellow
}
