@echo off
title Koperasi Laravel - Launcher
setlocal

echo ============================================
echo   KOPERASI LARAVEL - START
echo ============================================
echo.

:: 1. Pastikan Docker service jalan
sc query com.docker.service | find "RUNNING" >nul
if errorlevel 1 (
    echo [1/4] Nyalain Docker service, butuh izin admin...
    powershell -Command "Start-Process sc.exe -ArgumentList 'start','com.docker.service' -Verb RunAs -Wait"
)

:: 2. Start Docker Desktop & tunggu daemon siap
echo [2/4] Nyalain Docker Desktop, tunggu bentar...
start "" "C:\Program Files\Docker\Docker\Docker Desktop.exe"
set /a tries=0
:waitdocker
docker ps >nul 2>&1
if errorlevel 1 (
    set /a tries+=1
    if %tries% GEQ 60 (
        echo     GAGAL: Docker daemon gak mau nyala. Cek Docker Desktop manual.
        pause
        exit /b 1
    )
    ping -n 4 127.0.0.1 >nul
    goto waitdocker
)
echo     Docker siap!

:: 3. Start container database
echo [3/4] Nyalain database koperasi-db...
docker start koperasi-db >nul 2>&1
ping -n 6 127.0.0.1 >nul
docker ps --format "  DB container: {{.Names}} [{{.Status}}]" | find "koperasi-db"

:: 4. Jalankan server Laravel
echo [4/4] Nyalain Laravel server...
echo.
echo ============================================
echo   BUKA DI BROWSER:  http://127.0.0.1:8000
echo   (Ctrl+C di jendela ini buat stop server)
echo ============================================
echo.
set "PATH=%PATH%;C:\php84"
cd /d "%~dp0"
php artisan serve --host=127.0.0.1 --port=8000

endlocal
