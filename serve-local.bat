@echo off
setlocal enabledelayedexpansion
title SIM-EVAL Local Server (0.0.0.0:8000)

echo ========================================================
echo             SIM-EVAL - LOCAL SERVER LAUNCHER
echo ========================================================
echo.

:: Ambil IP Address lokal aktif
set LOCAL_IP=
for /f "tokens=2 delims=:" %%A in ('ipconfig ^| findstr /r /c:"IPv4 Address" /c:"Alamat IPv4"') do (
    for /f "tokens=1" %%B in ("%%A") do (
        set LOCAL_IP=%%B
    )
)

if "%LOCAL_IP%"=="" set LOCAL_IP=127.0.0.1

echo [INFO] IP Jaringan Lokal Komputer: %LOCAL_IP%
echo.
echo ========================================================
echo   Akses dari Laptop ini : http://localhost:8000
echo   Akses dari HP / WiFi  : http://%LOCAL_IP%:8000
echo ========================================================
echo.
echo Menjalankan: php artisan serve --host=0.0.0.0 --port=8000
echo (Tekan CTRL + C untuk menghentikan server)
echo.

php artisan serve --host=0.0.0.0 --port=8000
pause
