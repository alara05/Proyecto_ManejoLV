@echo off
title Instalar Mobile Usuario Capacitor

cd /d "%~dp0.."

echo ============================================
echo INSTALANDO APP MOBILE DE USUARIO
echo ============================================

where node >nul 2>nul
if errorlevel 1 (
    echo ERROR: Node.js no esta instalado.
    pause
    exit /b
)

where npm >nul 2>nul
if errorlevel 1 (
    echo ERROR: npm no esta instalado.
    pause
    exit /b
)

echo.
echo Instalando dependencias...
call npm install

echo.
echo Instalacion terminada.
echo.
echo Siguiente:
echo npm run cap:add:android
echo npm run android
echo.

pause
