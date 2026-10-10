@echo off
title Koperasi Laravel - Stop
echo Stop server dan database koperasi...
echo.

:: Matikan semua php artisan serve (server Laravel)
powershell -Command "Get-CimInstance Win32_Process -Filter 'Name=''php.exe''' | Where-Object { $_.CommandLine -match 'artisan serve' } | ForEach-Object { Stop-Process -Id $_.ProcessId -Force }"

:: Stop container database
docker stop koperasi-db 2>nul
if errorlevel 1 (
    echo     Docker daemon lagi gak jalan, skip stop container.
) else (
    echo     DB container berhenti.
)

:: Optional: matiin Docker Desktop biar hemat resource
:: Hapus "rem" di bawah kalau mau Docker ikut dimatiin tiap selesai dev
:: powershell -Command "Stop-Process -Name 'Docker Desktop' -Force -ErrorAction SilentlyContinue"

echo.
echo Beres! Aman buat shut down.
pause
