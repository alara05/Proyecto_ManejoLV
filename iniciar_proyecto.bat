@echo off
setlocal enabledelayedexpansion

title Manejo Buses - Verificador e inicio

set "APP_URL=http://127.0.0.1:8001"
set "APP_PORT=8001"
set "PHP_BIN=php"

cd /d "%~dp0"

echo.
echo =====================================================
echo   Manejo Buses - Verificacion e inicio del proyecto
echo =====================================================
echo.

where php >nul 2>nul
if errorlevel 1 (
    if exist "C:\xampp\php\php.exe" (
        set "PHP_BIN=C:\xampp\php\php.exe"
        echo PHP encontrado en XAMPP: !PHP_BIN!
    ) else (
        echo PHP no esta instalado o no esta agregado al PATH.
        call :try_winget_install "PHP.PHP" "PHP"
        where php >nul 2>nul
        if errorlevel 1 (
            echo.
            echo No se pudo detectar PHP. Instala PHP o XAMPP y vuelve a ejecutar este archivo.
            pause
            exit /b 1
        )
    )
) else (
    echo PHP encontrado.
)

where composer >nul 2>nul
if errorlevel 1 (
    echo Composer no esta instalado o no esta agregado al PATH.
    call :try_winget_install "Composer.Composer" "Composer"
    where composer >nul 2>nul
    if errorlevel 1 (
        echo.
        echo No se pudo detectar Composer. Instala Composer y vuelve a ejecutar este archivo.
        pause
        exit /b 1
    )
) else (
    echo Composer encontrado.
)

where node >nul 2>nul
if errorlevel 1 (
    echo Node.js no esta instalado o no esta agregado al PATH.
    call :try_winget_install "OpenJS.NodeJS.LTS" "Node.js"
    where node >nul 2>nul
    if errorlevel 1 (
        echo.
        echo No se pudo detectar Node.js. Instala Node.js LTS y vuelve a ejecutar este archivo.
        pause
        exit /b 1
    )
) else (
    echo Node.js encontrado.
)

where npm >nul 2>nul
if errorlevel 1 (
    echo NPM no esta instalado o no esta agregado al PATH.
    echo Normalmente se instala junto con Node.js.
    pause
    exit /b 1
) else (
    echo NPM encontrado.
)

echo.
echo Instalando dependencias PHP si faltan...
if not exist "vendor\autoload.php" (
    call composer install
    if errorlevel 1 (
        echo Error ejecutando composer install.
        pause
        exit /b 1
    )
) else (
    echo Dependencias PHP ya instaladas.
)

echo.
echo Instalando dependencias Node si faltan...
if not exist "node_modules" (
    call npm install
    if errorlevel 1 (
        echo Error ejecutando npm install.
        pause
        exit /b 1
    )
) else (
    echo Dependencias Node ya instaladas.
)

echo.
echo Preparando archivo .env...
if not exist ".env" (
    if exist ".env.example" (
        copy ".env.example" ".env" >nul
        echo Archivo .env creado desde .env.example.
    ) else (
        echo No existe .env.example. No se puede crear .env automaticamente.
        pause
        exit /b 1
    )
) else (
    echo Archivo .env encontrado.
)

findstr /C:"APP_KEY=base64:" ".env" >nul 2>nul
if errorlevel 1 (
    echo Generando APP_KEY...
    "%PHP_BIN%" artisan key:generate
    if errorlevel 1 (
        echo Error generando APP_KEY.
        pause
        exit /b 1
    )
) else (
    echo APP_KEY ya configurada.
)

echo.
echo Limpiando cache de configuracion...
"%PHP_BIN%" artisan config:clear

echo.
echo Verificando conexion y base de datos...
call :prepare_database
if errorlevel 1 (
    echo.
    echo No se pudo preparar la base de datos.
    echo Revisa que MySQL este encendido y que las credenciales de .env sean correctas.
    pause
    exit /b 1
)

echo.
echo Compilando assets...
call npm run build
if errorlevel 1 (
    echo Error ejecutando npm run build.
    pause
    exit /b 1
)

echo.
echo Ejecutando migraciones pendientes...
"%PHP_BIN%" artisan migrate --force
if errorlevel 1 (
    echo.
    echo No se pudieron ejecutar las migraciones.
    echo Revisa que MySQL este encendido y que las credenciales de .env sean correctas.
    pause
    exit /b 1
)

echo.
echo Revisando puerto %APP_PORT%...
netstat -ano | findstr ":%APP_PORT% " >nul 2>nul
if not errorlevel 1 (
    echo El puerto %APP_PORT% ya esta ocupado. Verificando si %APP_URL% responde...
    call :wait_for_url "%APP_URL%" 10
    if errorlevel 1 (
        echo.
        echo El puerto %APP_PORT% esta ocupado, pero la aplicacion no responde en %APP_URL%.
        echo Cierra el proceso que usa ese puerto o cambia APP_PORT en este archivo.
        pause
        exit /b 1
    )
    echo Aplicacion detectada. Abriendo navegador...
    start "" "%APP_URL%"
    echo.
    pause
    exit /b 0
)

echo.
echo Iniciando servidor Laravel en %APP_URL% ...
start "Manejo Buses - Laravel" /D "%CD%\public" cmd /k ""%PHP_BIN%" -S 127.0.0.1:%APP_PORT% ..\vendor\laravel\framework\src\Illuminate\Foundation\resources\server.php"

echo Esperando a que Laravel responda...
call :wait_for_url "%APP_URL%" 30
if errorlevel 1 (
    echo.
    echo El servidor se inicio, pero la aplicacion no respondio a tiempo.
    echo Revisa la ventana "Manejo Buses - Laravel" para ver el error.
    pause
    exit /b 1
)

start "" "%APP_URL%"

echo.
echo Proyecto iniciado correctamente.
echo URL: %APP_URL%
echo.
pause
exit /b 0

:try_winget_install
where winget >nul 2>nul
if errorlevel 1 (
    echo Winget no esta disponible para instalar %~2 automaticamente.
    exit /b 0
)

echo Intentando instalar %~2 con winget...
winget install --id %~1 --accept-package-agreements --accept-source-agreements
echo.
echo Si %~2 se instalo correctamente pero no se detecta, cierra esta ventana y ejecuta nuevamente el .bat.
exit /b 0

:prepare_database
"%PHP_BIN%" "scripts\prepare_database.php"
if not errorlevel 1 exit /b 0

echo.
echo No se pudo conectar a MySQL con las credenciales actuales.
echo Si tu usuario root tiene clave, escribela para guardarla en .env.
powershell -NoProfile -ExecutionPolicy Bypass -Command "$p = Read-Host 'Clave MySQL para root (Enter si no tiene)'; $c = Get-Content -Raw '.env'; if ($c -match '(?m)^DB_PASSWORD=') { $c = $c -replace '(?m)^DB_PASSWORD=.*', ('DB_PASSWORD=' + $p) } else { $c += [Environment]::NewLine + 'DB_PASSWORD=' + $p + [Environment]::NewLine }; Set-Content -NoNewline -Path '.env' -Value $c"
"%PHP_BIN%" artisan config:clear >nul 2>nul
"%PHP_BIN%" "scripts\prepare_database.php"
exit /b %errorlevel%

:wait_for_url
set "WAIT_URL=%~1"
set "WAIT_SECONDS=%~2"
for /l %%I in (1,1,%WAIT_SECONDS%) do (
    powershell -NoProfile -ExecutionPolicy Bypass -Command "try { $r = Invoke-WebRequest -UseBasicParsing -Uri '%WAIT_URL%' -TimeoutSec 2; if ($r.StatusCode -lt 500) { exit 0 }; exit 1 } catch { exit 1 }" >nul 2>nul
    if not errorlevel 1 exit /b 0
    timeout /t 1 /nobreak >nul
)
exit /b 1
