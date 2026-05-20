@echo off
setlocal EnableExtensions EnableDelayedExpansion

cd /d "%~dp0"

echo.
echo ==========================================
echo   Cuchao - iniciar servidor y app movil
echo ==========================================
echo.

where php >nul 2>nul
if errorlevel 1 (
    echo [ERROR] No se encontro PHP en el PATH.
    echo Abre este script desde una terminal donde php artisan funcione.
    pause
    exit /b 1
)

where node >nul 2>nul
if errorlevel 1 (
    echo [ERROR] No se encontro Node.js en el PATH.
    pause
    exit /b 1
)

where npm >nul 2>nul
if errorlevel 1 (
    echo [ERROR] No se encontro npm en el PATH.
    pause
    exit /b 1
)

for /f "usebackq delims=" %%I in (`powershell -NoProfile -ExecutionPolicy Bypass -Command "$ip = Get-NetIPConfiguration | Where-Object { $_.IPv4DefaultGateway -and $_.IPv4Address.IPAddress -notlike '169.254*' } | Select-Object -First 1 -ExpandProperty IPv4Address | Select-Object -First 1 -ExpandProperty IPAddress; if (-not $ip) { $ip = (Get-NetIPAddress -AddressFamily IPv4 | Where-Object { $_.IPAddress -notlike '127.*' -and $_.IPAddress -notlike '169.254*' } | Select-Object -First 1 -ExpandProperty IPAddress) }; Write-Output $ip"`) do set "LOCAL_IP=%%I"

if "%LOCAL_IP%"=="" (
    echo [ERROR] No se pudo detectar la IP local.
    pause
    exit /b 1
)

for /f "usebackq delims=" %%P in (`powershell -NoProfile -ExecutionPolicy Bypass -Command "$ports = 8010..8099; foreach ($p in $ports) { $busy = Get-NetTCPConnection -LocalPort $p -ErrorAction SilentlyContinue; if (-not $busy) { Write-Output $p; break } }"`) do set "APP_PORT=%%P"

if "%APP_PORT%"=="" (
    echo [ERROR] No se encontro un puerto libre entre 8010 y 8099.
    pause
    exit /b 1
)

set "API_URL=http://%LOCAL_IP%:%APP_PORT%/api/mobile"

echo IP detectada: %LOCAL_IP%
echo Puerto Laravel: %APP_PORT%
echo API movil: %API_URL%
echo.

echo Actualizando mobile\App.js...
powershell -NoProfile -ExecutionPolicy Bypass -Command "$path = 'mobile\App.js'; $api = $env:API_URL; $content = Get-Content -Raw $path; $content = [regex]::Replace($content, 'const API_BASE_URL = ''[^'']+'';', 'const API_BASE_URL = ''' + $api + ''';'); Set-Content -Path $path -Value $content -NoNewline"
if errorlevel 1 (
    echo [ERROR] No se pudo actualizar mobile\App.js.
    pause
    exit /b 1
)

echo Ejecutando migraciones...
php artisan migrate
if errorlevel 1 (
    echo [ERROR] Fallo php artisan migrate.
    pause
    exit /b 1
)

echo.
echo Iniciando Laravel en una ventana aparte...
set "LARAVEL_RUNNER=%TEMP%\cuchao_laravel_api_%APP_PORT%.bat"
(
    echo @echo off
    echo cd /d "%~dp0"
    echo php artisan serve --host=0.0.0.0 --port=%APP_PORT%
) > "%LARAVEL_RUNNER%"
start "Cuchao Laravel API :%APP_PORT%" cmd /k "%LARAVEL_RUNNER%"

echo Esperando a que Laravel inicie...
timeout /t 3 /nobreak >nul

echo.
echo Preparando app movil...
cd /d "%~dp0mobile"

if not exist node_modules (
    echo Instalando dependencias npm...
    npm install
    if errorlevel 1 (
        echo [ERROR] Fallo npm install.
        pause
        exit /b 1
    )
)

echo.
echo ==========================================
echo   Expo abrira el QR para tu telefono
echo ==========================================
echo.
echo Si tu celular no conecta:
echo - Debe estar en la misma red WiFi que esta PC.
echo - Permite PHP/Node en el Firewall de Windows.
echo - Verifica que la app use: %API_URL%
echo.

npm run start

endlocal
