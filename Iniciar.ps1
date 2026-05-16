# =========================================================
# SISTEMA DE PASAJES - INICIAR PROYECTO EN RED LOCAL
# Laravel ejecutado con servidor nativo PHP, NO artisan serve.
# Abre WEB y MOBILE, busca puertos libres y permite acceso LAN.
# Ejecutar desde la raiz del proyecto donde esta artisan.
# =========================================================

$PuertoLaravelBase = 8085
$PuertoMobileBase  = 5175
$NombreBD          = "SISTEMA_PASAJE"

# Evita que warnings de Node/npx se muestren como NativeCommandError.
$env:NODE_OPTIONS = "--no-deprecation"

function Titulo {
    param([string]$Texto)
    Write-Host ""
    Write-Host "========================================================="
    Write-Host " $Texto"
    Write-Host "========================================================="
}

function OK {
    param([string]$Texto)
    Write-Host "[OK] $Texto" -ForegroundColor Green
}

function Aviso {
    param([string]$Texto)
    Write-Host "[AVISO] $Texto" -ForegroundColor Yellow
}

function ErrorSalir {
    param([string]$Texto)
    Write-Host "[ERROR] $Texto" -ForegroundColor Red
    Write-Host ""
    pause
    exit 1
}

function Set-EnvValue {
    param(
        [string]$Texto,
        [string]$Clave,
        [string]$Valor
    )

    $Patron = "(?m)^" + [regex]::Escape($Clave) + "=.*$"

    if ($Texto -match $Patron) {
        return [regex]::Replace($Texto, $Patron, "$Clave=$Valor")
    }

    return $Texto.TrimEnd() + "`r`n$Clave=$Valor`r`n"
}

function Buscar-ProyectoLaravel {
    $dir = Get-Location

    while ($null -ne $dir) {
        if (Test-Path (Join-Path $dir.Path "artisan")) {
            return $dir.Path
        }

        $dir = $dir.Parent
    }

    return $null
}

function Probar-Puerto {
    param(
        [string]$HostPrueba,
        [int]$Puerto
    )

    try {
        $cliente = New-Object System.Net.Sockets.TcpClient
        $resultado = $cliente.BeginConnect($HostPrueba, $Puerto, $null, $null)
        $espera = $resultado.AsyncWaitHandle.WaitOne(700, $false)

        if ($espera) {
            $cliente.EndConnect($resultado)
            $cliente.Close()
            return $true
        }

        $cliente.Close()
        return $false
    } catch {
        return $false
    }
}

function Obtener-PuertoLibre {
    param([int]$PuertoInicial)

    for ($p = $PuertoInicial; $p -le ($PuertoInicial + 100); $p++) {
        if (-not (Probar-Puerto "127.0.0.1" $p)) {
            return $p
        }
    }

    ErrorSalir "No se encontro un puerto libre desde $PuertoInicial."
}

function Obtener-IPv4Locales {
    $lista = @()

    try {
        $preferidas = Get-NetIPConfiguration -ErrorAction SilentlyContinue |
            Where-Object {
                $_.IPv4Address -and
                $_.NetAdapter.Status -eq "Up" -and
                $_.IPv4DefaultGateway -and
                $_.NetAdapter.InterfaceDescription -notmatch "Virtual|VMware|VirtualBox|Hyper-V|vEthernet|Loopback|Bluetooth|TAP|VPN|Docker|WSL"
            } |
            ForEach-Object {
                $_.IPv4Address.IPAddress
            } |
            Where-Object {
                $_ -match "^\d{1,3}(\.\d{1,3}){3}$" -and
                $_ -notlike "127.*" -and
                $_ -notlike "169.254.*"
            }

        if ($preferidas) {
            $lista += $preferidas
        }
    } catch {}

    try {
        $todas = Get-NetIPAddress -AddressFamily IPv4 -ErrorAction SilentlyContinue |
            Where-Object {
                $_.IPAddress -notlike "127.*" -and
                $_.IPAddress -notlike "169.254.*"
            } |
            Sort-Object InterfaceMetric |
            Select-Object -ExpandProperty IPAddress

        if ($todas) {
            $lista += $todas
        }
    } catch {}

    try {
        $wmi = Get-WmiObject Win32_NetworkAdapterConfiguration |
            Where-Object { $_.IPEnabled -eq $true } |
            ForEach-Object { $_.IPAddress } |
            Where-Object {
                $_ -match "^\d{1,3}(\.\d{1,3}){3}$" -and
                $_ -notlike "127.*" -and
                $_ -notlike "169.254.*"
            }

        if ($wmi) {
            $lista += $wmi
        }
    } catch {}

    $lista = $lista | Select-Object -Unique

    if (!$lista -or $lista.Count -eq 0) {
        return @("127.0.0.1")
    }

    return @($lista)
}

function Obtener-IPv4Local {
    $ips = Obtener-IPv4Locales

    if ($ips -and $ips.Count -gt 0) {
        return $ips[0]
    }

    return "127.0.0.1"
}

function Limpiar-BasuraRuntime {
    param([string]$Proyecto)

    $runtime = Join-Path $Proyecto "runtime"

    if (!(Test-Path $runtime)) {
        return
    }

    $patrones = @(
        "cmd_*_out.log",
        "cmd_*_err.log",
        "php_server.log",
        "mobile_server.log"
    )

    foreach ($patron in $patrones) {
        Get-ChildItem -Path $runtime -Filter $patron -ErrorAction SilentlyContinue | Remove-Item -Force -ErrorAction SilentlyContinue
    }

    OK "Runtime limpio. No se generaran logs basura."
}


function Buscar-PHP {
    $cmd = Get-Command php -ErrorAction SilentlyContinue

    if ($cmd) {
        return $cmd.Source
    }

    $posibles = @(
        "C:\php\php.exe",
        "C:\Program Files\PHP\php.exe",
        "C:\Users\$env:USERNAME\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.4_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe",
        "C:\Users\$env:USERNAME\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.3_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe",
        "C:\Users\$env:USERNAME\AppData\Local\Programs\PHP\php.exe"
    )

    foreach ($ruta in $posibles) {
        if (Test-Path $ruta) {
            return $ruta
        }
    }

    return $null
}

function Es-Admin {
    $identity = [Security.Principal.WindowsIdentity]::GetCurrent()
    $principal = New-Object Security.Principal.WindowsPrincipal($identity)
    return $principal.IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)
}

function Abrir-Firewall {
    param(
        [int]$PuertoLaravel,
        [int]$PuertoMobile,
        [string]$PhpExe
    )

    if (-not (Es-Admin)) {
        Aviso "PowerShell no esta como administrador. Se omite configuracion de Firewall."
        Aviso "Si otro dispositivo no entra, ejecuta este Iniciar.ps1 como administrador una vez."
        return
    }

    try {
        netsh advfirewall firewall add rule name="SistemaPasaje Web $PuertoLaravel" dir=in action=allow protocol=TCP localport=$PuertoLaravel | Out-Null
        OK "Puerto WEB $PuertoLaravel abierto en Firewall."
    } catch {
        Aviso "No se pudo abrir el puerto WEB $PuertoLaravel."
    }

    try {
        netsh advfirewall firewall add rule name="SistemaPasaje Mobile $PuertoMobile" dir=in action=allow protocol=TCP localport=$PuertoMobile | Out-Null
        OK "Puerto MOBILE $PuertoMobile abierto en Firewall."
    } catch {
        Aviso "No se pudo abrir el puerto MOBILE $PuertoMobile."
    }

    try {
        netsh advfirewall firewall add rule name="SistemaPasaje PHP" dir=in action=allow program="$PhpExe" enable=yes | Out-Null
        OK "php.exe permitido en Firewall."
    } catch {
        Aviso "No se pudo permitir php.exe en Firewall."
    }

    try {
        netsh advfirewall firewall add rule name="SistemaPasaje Node" dir=in action=allow program="node.exe" enable=yes | Out-Null
    } catch {}
}

function Crear-PHPINI {
    param(
        [string]$Proyecto,
        [string]$PhpExe
    )

    $runtime = Join-Path $Proyecto "runtime"

    if (!(Test-Path $runtime)) {
        New-Item -ItemType Directory -Path $runtime -Force | Out-Null
    }

    $phpDir = Split-Path $PhpExe
    $extDir = Join-Path $phpDir "ext"
    $phpIni = Join-Path $runtime "php.ini"

    if (!(Test-Path $extDir)) {
        Aviso "No se encontro carpeta ext en: $extDir"
    }

    $contenido = @"
[PHP]
engine=On
short_open_tag=Off
precision=14
output_buffering=4096
zlib.output_compression=Off
implicit_flush=Off
serialize_precision=-1
disable_functions=
disable_classes=
zend.enable_gc=On
expose_php=Off

max_execution_time=180
max_input_time=180
memory_limit=512M
post_max_size=100M
upload_max_filesize=100M
max_file_uploads=50

default_charset="UTF-8"
date.timezone=America/Guayaquil

extension_dir="$extDir"

extension=curl
extension=fileinfo
extension=gd
extension=mbstring
extension=mysqli
extension=openssl
extension=pdo_mysql
extension=zip

[Session]
session.save_handler=files
session.use_strict_mode=0
session.use_cookies=1
session.use_only_cookies=1
session.name=PHPSESSID
session.auto_start=0

[opcache]
opcache.enable=0
"@

    Set-Content -Path $phpIni -Value $contenido -Encoding UTF8
    OK "php.ini preparado: $phpIni"

    return $phpIni
}

function Verificar-ExtensionPHP {
    param(
        [string]$PhpExe,
        [string]$PhpIni,
        [string]$Extension
    )

    $modulos = & $PhpExe -c $PhpIni -m 2>&1
    $existe = $modulos | Select-String -Pattern "^$Extension$"

    if ($existe) {
        OK "Extension $Extension activa."
        return $true
    }

    $existeFlexible = $modulos | Select-String -Pattern $Extension

    if ($existeFlexible) {
        OK "Extension $Extension activa."
        return $true
    }

    Aviso "Extension $Extension no aparece activa."
    return $false
}

function Ejecutar-Comando {
    param(
        [string]$Archivo,
        [string]$Argumentos,
        [string]$Carpeta,
        [string]$Nombre,
        [int]$TimeoutSeg = 600,
        [bool]$MostrarSalida = $false
    )

    Write-Host "[CMD] $Nombre" -ForegroundColor Cyan

    try {
        $p = Start-Process `
            -FilePath $Archivo `
            -ArgumentList $Argumentos `
            -WorkingDirectory $Carpeta `
            -PassThru `
            -NoNewWindow

        $termino = $p.WaitForExit($TimeoutSeg * 1000)

        if (-not $termino) {
            try { $p.Kill() } catch {}
            ErrorSalir "Tiempo agotado en: $Nombre"
        }

        $codigoSalida = 0

        if ($null -ne $p.ExitCode) {
            $codigoSalida = [int]$p.ExitCode
        }

        if ($codigoSalida -ne 0) {
            Write-Host ""
            Write-Host "Fallo el comando: $Nombre" -ForegroundColor Red
            Write-Host "Codigo de salida: $codigoSalida" -ForegroundColor Red
            return $false
        }

        return $true
    } catch {
        Write-Host ""
        Write-Host "Fallo el comando: $Nombre" -ForegroundColor Red
        Write-Host $_.Exception.Message -ForegroundColor Red
        return $false
    }
}


function Crear-EnvSiNoExiste {
    param(
        [string]$Proyecto,
        [int]$PuertoLaravel,
        [string]$IpLan
    )

    $envPath = Join-Path $Proyecto ".env"
    $envExample = Join-Path $Proyecto ".env.example"

    if (!(Test-Path $envPath)) {
        if (Test-Path $envExample) {
            Copy-Item $envExample $envPath
            OK "Archivo .env creado desde .env.example."
        } else {
            New-Item -ItemType File -Path $envPath -Force | Out-Null
            OK "Archivo .env creado."
        }
    } else {
        OK "Archivo .env encontrado."
    }

    $envTexto = Get-Content $envPath -Raw

    $envTexto = Set-EnvValue $envTexto "APP_NAME" '"Sistema Pasaje"'
    $envTexto = Set-EnvValue $envTexto "APP_ENV" "local"
    $envTexto = Set-EnvValue $envTexto "APP_DEBUG" "true"

    # APP_URL queda en LAN para que otros dispositivos puedan consumir enlaces generados.
    $envTexto = Set-EnvValue $envTexto "APP_URL" "http://$IpLan`:$PuertoLaravel"

    # Forzar MySQL para evitar que Laravel use SQLite.
    $envTexto = Set-EnvValue $envTexto "DB_CONNECTION" "mysql"
    $envTexto = Set-EnvValue $envTexto "DB_HOST" "127.0.0.1"
    $envTexto = Set-EnvValue $envTexto "DB_PORT" "3306"
    $envTexto = Set-EnvValue $envTexto "DB_DATABASE" "$NombreBD"

    # No pisar usuario/clave si ya los configuraste.
    if ($envTexto -notmatch "(?m)^DB_USERNAME=") {
        $envTexto = $envTexto.TrimEnd() + "`r`nDB_USERNAME=root`r`n"
    }

    if ($envTexto -notmatch "(?m)^DB_PASSWORD=") {
        $envTexto = $envTexto.TrimEnd() + "`r`nDB_PASSWORD=`r`n"
    }

    $envTexto = Set-EnvValue $envTexto "SESSION_DRIVER" "file"
    $envTexto = Set-EnvValue $envTexto "SESSION_LIFETIME" "120"
    $envTexto = Set-EnvValue $envTexto "CACHE_STORE" "file"
    $envTexto = Set-EnvValue $envTexto "QUEUE_CONNECTION" "sync"

    $envTexto = Set-EnvValue $envTexto "MAIL_MAILER" "log"
    $envTexto = Set-EnvValue $envTexto "MAIL_FROM_ADDRESS" '"soporte@sistemapasaje.com"'
    $envTexto = Set-EnvValue $envTexto "MAIL_FROM_NAME" '"Sistema Pasaje"'
    $envTexto = Set-EnvValue $envTexto "VITE_APP_NAME" '"Sistema Pasaje"'

    Set-Content -Path $envPath -Value $envTexto -Encoding UTF8

    OK "Archivo .env revisado y corregido para MySQL."
    OK "APP_URL configurado para red local: http://$IpLan`:$PuertoLaravel"
}

function Crear-RouterPHPNativo {
    param([string]$Proyecto)

    $runtime = Join-Path $Proyecto "runtime"
    if (!(Test-Path $runtime)) {
        New-Item -ItemType Directory -Path $runtime -Force | Out-Null
    }

    $routerPath = Join-Path $runtime "router.php"

    $router = @'
<?php

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/');
$publicPath = realpath(__DIR__ . '/../public');

if ($publicPath === false) {
    http_response_code(500);
    echo 'No se encontro la carpeta public.';
    return true;
}

$file = $publicPath . DIRECTORY_SEPARATOR . ltrim(str_replace('/', DIRECTORY_SEPARATOR, $uri), DIRECTORY_SEPARATOR);

if ($uri !== '/' && is_file($file)) {
    return false;
}

require $publicPath . DIRECTORY_SEPARATOR . 'index.php';
'@

    Set-Content -Path $routerPath -Value $router -Encoding UTF8
    OK "Router PHP nativo creado: $routerPath"

    return $routerPath
}

function Crear-WebBasico {
    param([string]$Proyecto)

    $views = Join-Path $Proyecto "resources\views"
    $authViews = Join-Path $Proyecto "resources\views\auth"
    $routesDir = Join-Path $Proyecto "routes"
    $publicCss = Join-Path $Proyecto "public\assets\web\css"
    $publicJs = Join-Path $Proyecto "public\assets\web\js"

    New-Item -ItemType Directory -Path $views -Force | Out-Null
    New-Item -ItemType Directory -Path $authViews -Force | Out-Null
    New-Item -ItemType Directory -Path $routesDir -Force | Out-Null
    New-Item -ItemType Directory -Path $publicCss -Force | Out-Null
    New-Item -ItemType Directory -Path $publicJs -Force | Out-Null

    $indexPath = Join-Path $views "index.blade.php"
    $loginPath = Join-Path $authViews "login.blade.php"
    $cssPath = Join-Path $publicCss "login.css"
    $jsPath = Join-Path $publicJs "login.js"
    $routePath = Join-Path $routesDir "web.php"

    if (!(Test-Path $indexPath)) {
        Set-Content -Path $indexPath -Encoding UTF8 -Value @'
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Pasajes</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="0; url=/login">
</head>
<body>
    <p>Redirigiendo al login...</p>
</body>
</html>
'@
        OK "Creado resources/views/index.blade.php"
    }

    if (!(Test-Path $loginPath)) {
        Set-Content -Path $loginPath -Encoding UTF8 -Value @'
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Sistema de Pasajes</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('assets/web/css/login.css') }}">
</head>
<body>
    <main class="login-page">
        <section class="login-card">
            <div class="logo-box">
                <div class="bus-icon">🚌</div>
            </div>

            <h1>Sistema de Pasajes</h1>
            <p>Ingresa para gestionar o comprar boletos</p>

            <form id="formLoginWeb">
                <label>Correo electrónico</label>
                <input type="email" id="email" placeholder="correo@ejemplo.com" required>

                <label>Contraseña</label>
                <input type="password" id="password" placeholder="********" required>

                <button type="submit">Ingresar</button>
            </form>

            <div id="mensajeLogin" class="mensaje"></div>
        </section>
    </main>

    <script src="{{ asset('assets/web/js/login.js') }}"></script>
</body>
</html>
'@
        OK "Creado resources/views/auth/login.blade.php"
    }

    if (!(Test-Path $cssPath)) {
        Set-Content -Path $cssPath -Encoding UTF8 -Value @'
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial, Helvetica, sans-serif;
    background: linear-gradient(135deg, #0d47a1, #1976d2);
    min-height: 100vh;
}

.login-page {
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
}

.login-card {
    width: 100%;
    max-width: 430px;
    background: white;
    padding: 28px;
    border-radius: 22px;
    box-shadow: 0 18px 40px rgba(0,0,0,0.25);
}

.logo-box {
    display: flex;
    justify-content: center;
    margin-bottom: 12px;
}

.bus-icon {
    width: 80px;
    height: 80px;
    border-radius: 22px;
    background: #0d47a1;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 38px;
}

h1 {
    text-align: center;
    margin: 0;
    color: #111827;
}

p {
    text-align: center;
    color: #6b7280;
}

label {
    display: block;
    font-weight: bold;
    margin-top: 14px;
    color: #374151;
}

input {
    width: 100%;
    padding: 13px;
    border-radius: 10px;
    border: 1px solid #d1d5db;
    margin-top: 7px;
    font-size: 15px;
}

button {
    width: 100%;
    margin-top: 20px;
    padding: 13px;
    border: none;
    border-radius: 10px;
    background: #0d47a1;
    color: white;
    font-weight: bold;
    font-size: 15px;
    cursor: pointer;
}

button:hover {
    background: #083574;
}

.mensaje {
    margin-top: 14px;
    text-align: center;
    font-weight: bold;
}
'@
        OK "Creado public/assets/web/css/login.css"
    }

    if (!(Test-Path $jsPath)) {
        Set-Content -Path $jsPath -Encoding UTF8 -Value @'
document.getElementById("formLoginWeb").addEventListener("submit", async function (e) {
    e.preventDefault();

    const mensaje = document.getElementById("mensajeLogin");

    mensaje.textContent = "Validando acceso...";

    try {
        const respuesta = await fetch("/api/web/login", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json"
            },
            body: JSON.stringify({
                email: document.getElementById("email").value.trim(),
                password: document.getElementById("password").value.trim()
            })
        });

        const data = await respuesta.json();

        if (!respuesta.ok) {
            mensaje.textContent = data.mensaje || "Credenciales incorrectas.";
            return;
        }

        localStorage.setItem("TOKEN_APP", data.token || "");
        localStorage.setItem("USUARIO_APP", JSON.stringify(data.usuario || {}));

        mensaje.textContent = "Acceso correcto.";

        setTimeout(function () {
            window.location.href = "/dashboard";
        }, 700);
    } catch (error) {
        mensaje.textContent = "Servidor activo. Falta crear la API /api/web/login.";
    }
});
'@
        OK "Creado public/assets/web/js/login.js"
    }

    if (!(Test-Path $routePath)) {
        Set-Content -Path $routePath -Encoding UTF8 -Value @'
<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
})->name('index');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/dashboard', function () {
    return '<h1>Dashboard en construccion</h1><p>Login visual creado correctamente.</p>';
})->name('dashboard');
'@
        OK "Creado routes/web.php"
    } else {
        $rutas = Get-Content $routePath -Raw

        if ($rutas -notmatch "Route::get\('/login'") {
            Add-Content -Path $routePath -Value "`nRoute::get('/login', function () { return view('auth.login'); })->name('login');"
            OK "Ruta /login agregada a routes/web.php"
        }

        if ($rutas -notmatch "Route::get\('/dashboard'") {
            Add-Content -Path $routePath -Value "`nRoute::get('/dashboard', function () { return '<h1>Dashboard en construccion</h1>'; })->name('dashboard');"
            OK "Ruta /dashboard agregada a routes/web.php"
        }
    }
}

function Crear-MobileBasico {
    param(
        [string]$Proyecto,
        [int]$PuertoLaravel,
        [string]$IpLan
    )

    $mobile = Join-Path $Proyecto "Mobile"
    $www = Join-Path $mobile "www"
    $auth = Join-Path $www "pages\auth"
    $cssAuth = Join-Path $www "css\auth"
    $jsAuth = Join-Path $www "js\auth"
    $jsHelpers = Join-Path $www "js\helpers"
    $assets = Join-Path $www "assets\img"

    New-Item -ItemType Directory -Path $auth -Force | Out-Null
    New-Item -ItemType Directory -Path $cssAuth -Force | Out-Null
    New-Item -ItemType Directory -Path $jsAuth -Force | Out-Null
    New-Item -ItemType Directory -Path $jsHelpers -Force | Out-Null
    New-Item -ItemType Directory -Path $assets -Force | Out-Null

    $packagePath = Join-Path $mobile "package.json"
    $capacitorPath = Join-Path $mobile "capacitor.config.json"
    $indexPath = Join-Path $www "index.html"
    $loginPath = Join-Path $auth "login.html"
    $appCssPath = Join-Path $www "css\app.css"
    $loginCssPath = Join-Path $cssAuth "login.css"
    $configJsPath = Join-Path $www "js\config.js"
    $storageJsPath = Join-Path $jsHelpers "storage.js"
    $apiJsPath = Join-Path $jsHelpers "api.js"
    $loginJsPath = Join-Path $jsAuth "login.js"
    $logoPath = Join-Path $assets "logo.svg"

    if (!(Test-Path $packagePath)) {
        Set-Content -Path $packagePath -Encoding UTF8 -Value @'
{
  "name": "sistema-pasaje-mobile-usuario",
  "version": "1.0.0",
  "description": "App mobile para usuarios que compran boletos",
  "scripts": {
    "start": "npx http-server www -a 0.0.0.0 -p 5175 -c-1",
    "cap:add:android": "npx cap add android",
    "cap:sync": "npx cap sync",
    "cap:open:android": "npx cap open android",
    "android": "npx cap sync android && npx cap open android"
  },
  "dependencies": {
    "@capacitor/android": "latest",
    "@capacitor/cli": "latest",
    "@capacitor/core": "latest",
    "http-server": "latest"
  }
}
'@
        OK "Creado Mobile/package.json"
    }

    if (!(Test-Path $capacitorPath)) {
        Set-Content -Path $capacitorPath -Encoding UTF8 -Value @'
{
  "appId": "ec.edu.uta.sistemapasajeusuario",
  "appName": "Pasajes Usuario",
  "webDir": "www",
  "server": {
    "androidScheme": "https"
  }
}
'@
        OK "Creado Mobile/capacitor.config.json"
    }

    if (!(Test-Path $indexPath)) {
        Set-Content -Path $indexPath -Encoding UTF8 -Value @'
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pasajes Usuario</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>
        window.location.href = "./pages/auth/login.html";
    </script>
</head>
<body>
    <p>Cargando...</p>
</body>
</html>
'@
        OK "Creado Mobile/www/index.html"
    }

    if (!(Test-Path $loginPath)) {
        Set-Content -Path $loginPath -Encoding UTF8 -Value @'
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login Mobile - Pasajes</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/app.css">
    <link rel="stylesheet" href="../../css/auth/login.css">
</head>
<body>
    <main class="pantalla-auth">
        <section class="tarjeta-auth">
            <img src="../../assets/img/logo.svg" class="logo-app" alt="Logo">

            <h1>Pasajes Ecuador</h1>
            <p>Compra tus boletos desde tu celular</p>

            <form id="formLoginMobile">
                <label>Correo electronico</label>
                <input type="email" id="email" placeholder="correo@ejemplo.com" required>

                <label>Contrasena</label>
                <input type="password" id="password" placeholder="********" required>

                <button type="submit">Ingresar</button>
            </form>

            <div id="mensajeLogin" class="mensaje"></div>
        </section>
    </main>

    <script src="../../js/config.js"></script>
    <script src="../../js/helpers/storage.js"></script>
    <script src="../../js/helpers/api.js"></script>
    <script src="../../js/auth/login.js"></script>
</body>
</html>
'@
        OK "Creado Mobile/www/pages/auth/login.html"
    }

    if (!(Test-Path $appCssPath)) {
        Set-Content -Path $appCssPath -Encoding UTF8 -Value @'
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial, Helvetica, sans-serif;
    background: #f4f6f9;
    color: #1f2937;
}

input,
button {
    width: 100%;
    padding: 13px;
    margin-top: 8px;
    margin-bottom: 14px;
    border-radius: 10px;
    border: 1px solid #d1d5db;
    font-size: 15px;
}

button {
    background: #0d47a1;
    color: white;
    border: none;
    font-weight: bold;
}
'@
    }

    if (!(Test-Path $loginCssPath)) {
        Set-Content -Path $loginCssPath -Encoding UTF8 -Value @'
.pantalla-auth {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 18px;
    background: linear-gradient(135deg, #0d47a1, #1976d2);
}

.tarjeta-auth {
    width: 100%;
    max-width: 430px;
    background: white;
    padding: 24px;
    border-radius: 22px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}

.logo-app {
    width: 88px;
    display: block;
    margin: 0 auto 12px auto;
}

.tarjeta-auth h1,
.tarjeta-auth p {
    text-align: center;
}

.mensaje {
    text-align: center;
    font-weight: bold;
}
'@
    }

    # IMPORTANTE:
    # En celular 127.0.0.1 apunta al celular, NO a la PC.
    # Por eso la API se configura con la IP LAN del servidor.
    Set-Content -Path $configJsPath -Encoding UTF8 -Value @"
const CONFIG_APP = {
    API_BASE_URL: localStorage.getItem("API_BASE_URL") || "http://$IpLan`:$PuertoLaravel",
    API_PATH: "/api"
};

function obtenerApiBase() {
    return CONFIG_APP.API_BASE_URL + CONFIG_APP.API_PATH;
}

function guardarServidorManual(url) {
    localStorage.setItem("API_BASE_URL", url);
    CONFIG_APP.API_BASE_URL = url;
}
"@
    OK "Mobile/www/js/config.js actualizado para LAN: http://$IpLan`:$PuertoLaravel"

    if (!(Test-Path $storageJsPath)) {
        Set-Content -Path $storageJsPath -Encoding UTF8 -Value @'
const StorageApp = {
    set(clave, valor) {
        localStorage.setItem(clave, JSON.stringify(valor));
    },

    get(clave) {
        const valor = localStorage.getItem(clave);

        if (!valor) {
            return null;
        }

        try {
            return JSON.parse(valor);
        } catch (error) {
            return valor;
        }
    },

    remove(clave) {
        localStorage.removeItem(clave);
    }
};
'@
    }

    if (!(Test-Path $apiJsPath)) {
        Set-Content -Path $apiJsPath -Encoding UTF8 -Value @'
async function apiRequest(ruta, opciones = {}) {
    const token = localStorage.getItem("TOKEN_APP");

    const headers = {
        "Accept": "application/json",
        ...(opciones.headers || {})
    };

    if (!(opciones.body instanceof FormData)) {
        headers["Content-Type"] = "application/json";
    }

    if (token) {
        headers["Authorization"] = "Bearer " + token;
    }

    const respuesta = await fetch(obtenerApiBase() + ruta, {
        ...opciones,
        headers
    });

    let data = null;

    try {
        data = await respuesta.json();
    } catch (error) {
        data = {
            mensaje: "Respuesta no valida del servidor"
        };
    }

    if (!respuesta.ok) {
        throw data;
    }

    return data;
}
'@
    }

    if (!(Test-Path $loginJsPath)) {
        Set-Content -Path $loginJsPath -Encoding UTF8 -Value @'
document.getElementById("formLoginMobile").addEventListener("submit", async function (e) {
    e.preventDefault();

    const mensaje = document.getElementById("mensajeLogin");

    mensaje.textContent = "Validando acceso...";

    try {
        const data = await apiRequest("/mobile/login", {
            method: "POST",
            body: JSON.stringify({
                email: document.getElementById("email").value.trim(),
                password: document.getElementById("password").value.trim()
            })
        });

        localStorage.setItem("TOKEN_APP", data.token || "");
        localStorage.setItem("USUARIO_APP", JSON.stringify(data.usuario || {}));

        mensaje.textContent = "Acceso correcto.";
    } catch (error) {
        mensaje.textContent = error.mensaje || "Servidor activo. Falta crear la API /api/mobile/login.";
    }
});
'@
    }

    if (!(Test-Path $logoPath)) {
        Set-Content -Path $logoPath -Encoding UTF8 -Value @'
<svg width="120" height="120" viewBox="0 0 120 120" xmlns="http://www.w3.org/2000/svg">
  <rect width="120" height="120" rx="24" fill="#0d47a1"/>
  <path d="M25 70 L95 70 L85 45 L35 45 Z" fill="#ffffff"/>
  <circle cx="42" cy="77" r="8" fill="#ffc107"/>
  <circle cx="78" cy="77" r="8" fill="#ffc107"/>
  <path d="M42 36 H78" stroke="#ffc107" stroke-width="8" stroke-linecap="round"/>
</svg>
'@
    }
}

function Esperar-Web {
    param(
        [string]$Url,
        [int]$Intentos = 40
    )

    for ($i = 1; $i -le $Intentos; $i++) {
        try {
            $r = Invoke-WebRequest -Uri $Url -UseBasicParsing -TimeoutSec 2
            if ($r.StatusCode -ge 200 -and $r.StatusCode -lt 500) {
                return $true
            }
        } catch {}

        Write-Host "Esperando servidor... intento $i de $Intentos"
        Start-Sleep -Seconds 1
    }

    return $false
}

# =========================================================
# INICIO
# =========================================================

Titulo "SISTEMA DE PASAJES - INICIO DEL PROYECTO"

$Proyecto = Buscar-ProyectoLaravel

if (!$Proyecto) {
    ErrorSalir "No se encontro artisan. Ejecuta este archivo dentro del proyecto Laravel."
}

Set-Location $Proyecto
OK "Proyecto detectado: $Proyecto"

$Runtime = Join-Path $Proyecto "runtime"
if (!(Test-Path $Runtime)) {
    New-Item -ItemType Directory -Path $Runtime -Force | Out-Null
}

Limpiar-BasuraRuntime $Proyecto

$IpsLan = Obtener-IPv4Locales
$IpLan = $IpsLan[0]
$PuertoLaravel = Obtener-PuertoLibre $PuertoLaravelBase
$PuertoMobile  = Obtener-PuertoLibre $PuertoMobileBase

OK "IP principal detectada: $IpLan"

Write-Host ""
Write-Host "IPs detectadas para probar desde otro dispositivo:" -ForegroundColor Cyan
foreach ($ip in $IpsLan) {
    Write-Host "  http://$ip`:$PuertoLaravel/login" -ForegroundColor Cyan
}

OK "Puerto WEB seleccionado: $PuertoLaravel"
OK "Puerto Mobile seleccionado: $PuertoMobile"

# =========================================================
# MYSQL
# =========================================================

Titulo "MYSQL"

$serviciosMysql = @("MySQL90", "MySQL80", "MySQL", "MariaDB")
$servicioEncontrado = $null

foreach ($s in $serviciosMysql) {
    $srv = Get-Service -Name $s -ErrorAction SilentlyContinue

    if ($srv) {
        $servicioEncontrado = $srv
        break
    }
}

if ($servicioEncontrado) {
    if ($servicioEncontrado.Status -ne "Running") {
        Aviso "Iniciando servicio MySQL: $($servicioEncontrado.Name)"
        try {
            Start-Service $servicioEncontrado.Name
            Start-Sleep -Seconds 3
        } catch {
            Aviso "No se pudo iniciar MySQL automaticamente. Ejecuta PowerShell como administrador o inicia MySQL manualmente."
        }
    }

    $srvActual = Get-Service -Name $servicioEncontrado.Name -ErrorAction SilentlyContinue
    if ($srvActual -and $srvActual.Status -eq "Running") {
        OK "Servicio MySQL activo: $($servicioEncontrado.Name)"
    }
} else {
    Aviso "No se encontro servicio MySQL registrado. Si MySQL esta abierto manualmente, puedes continuar."
}

if (Probar-Puerto "127.0.0.1" 3306) {
    OK "MySQL responde en 127.0.0.1:3306"
} else {
    Aviso "MySQL no responde en 127.0.0.1:3306. La pagina puede abrir, pero el login real fallara."
}

# =========================================================
# PHP
# =========================================================

Titulo "PHP"

$PhpExe = Buscar-PHP

if (!$PhpExe) {
    ErrorSalir "No se encontro PHP. Instala PHP 8.2 o superior."
}

OK "PHP encontrado: $PhpExe"
& $PhpExe -v

# =========================================================
# PHP.INI
# =========================================================

Titulo "PHP.INI"

$PhpIni = Crear-PHPINI $Proyecto $PhpExe
$PhpIniDir = Split-Path $PhpIni

$env:PHPRC = $PhpIniDir
$env:PHP_INI_SCAN_DIR = ""
$env:PATH = "$(Split-Path $PhpExe);$env:PATH"

$pdoOk = Verificar-ExtensionPHP $PhpExe $PhpIni "pdo_mysql"
$fileinfoOk = Verificar-ExtensionPHP $PhpExe $PhpIni "fileinfo"

if (-not $pdoOk) {
    ErrorSalir "No se pudo activar pdo_mysql. Verifica que exista php_pdo_mysql.dll en la carpeta ext de PHP."
}

if (-not $fileinfoOk) {
    ErrorSalir "No se pudo activar fileinfo. Verifica que exista php_fileinfo.dll en la carpeta ext de PHP."
}

# =========================================================
# FIREWALL
# =========================================================

Titulo "FIREWALL"

Abrir-Firewall $PuertoLaravel $PuertoMobile $PhpExe

# =========================================================
# ARCHIVOS BASE
# =========================================================

Titulo "ARCHIVOS BASE"

Crear-EnvSiNoExiste $Proyecto $PuertoLaravel $IpLan
Crear-WebBasico $Proyecto
Crear-MobileBasico $Proyecto $PuertoLaravel $IpLan
$RouterPath = Crear-RouterPHPNativo $Proyecto

# =========================================================
# COMPOSER / LARAVEL
# =========================================================

Titulo "COMPOSER / LARAVEL"

$composer = Get-Command composer -ErrorAction SilentlyContinue

if (!$composer) {
    ErrorSalir "Composer no esta instalado o no esta en PATH."
}

$vendorAutoload = Join-Path $Proyecto "vendor\autoload.php"

if (!(Test-Path $vendorAutoload)) {
    Aviso "vendor/autoload.php no existe. Ejecutando composer install..."

    $okComposer = Ejecutar-Comando `
        -Archivo "cmd.exe" `
        -Argumentos "/c composer install --no-dev --prefer-dist --no-progress --no-interaction" `
        -Carpeta $Proyecto `
        -Nombre "composer install" `
        -TimeoutSeg 900 `
        -MostrarSalida $true

    if (-not $okComposer) {
        Aviso "composer install fallo. Reintentando sin scripts..."

        $okComposer2 = Ejecutar-Comando `
            -Archivo "cmd.exe" `
            -Argumentos "/c composer install --no-dev --prefer-dist --no-progress --no-interaction --no-scripts" `
            -Carpeta $Proyecto `
            -Nombre "composer install --no-scripts" `
            -TimeoutSeg 900 `
            -MostrarSalida $true

        if (-not $okComposer2) {
            ErrorSalir "No se pudieron instalar dependencias de Composer."
        }
    }

    OK "Dependencias Composer listas."
} else {
    OK "vendor/autoload.php encontrado."
}

$envText = Get-Content (Join-Path $Proyecto ".env") -Raw

if ($envText -match "APP_KEY=\s*$" -or $envText -match "APP_KEY=$" -or $envText -notmatch "APP_KEY=base64:") {
    Aviso "Generando APP_KEY..."

    $okKey = Ejecutar-Comando `
        -Archivo $PhpExe `
        -Argumentos "-c `"$PhpIni`" artisan key:generate --force" `
        -Carpeta $Proyecto `
        -Nombre "php artisan key:generate" `
        -TimeoutSeg 120 `
        -MostrarSalida $true

    if (-not $okKey) {
        ErrorSalir "No se pudo generar APP_KEY."
    }
} else {
    OK "APP_KEY ya existe."
}

Ejecutar-Comando -Archivo $PhpExe -Argumentos "-c `"$PhpIni`" artisan config:clear" -Carpeta $Proyecto -Nombre "config:clear" -TimeoutSeg 120 | Out-Null
Ejecutar-Comando -Archivo $PhpExe -Argumentos "-c `"$PhpIni`" artisan cache:clear"  -Carpeta $Proyecto -Nombre "cache:clear"  -TimeoutSeg 120 | Out-Null
Ejecutar-Comando -Archivo $PhpExe -Argumentos "-c `"$PhpIni`" artisan route:clear"  -Carpeta $Proyecto -Nombre "route:clear"  -TimeoutSeg 120 | Out-Null
Ejecutar-Comando -Archivo $PhpExe -Argumentos "-c `"$PhpIni`" artisan view:clear"   -Carpeta $Proyecto -Nombre "view:clear"   -TimeoutSeg 120 | Out-Null

OK "Caches de Laravel limpiadas."

# =========================================================
# PRUEBA RAPIDA
# =========================================================

Titulo "PRUEBA DE LARAVEL"

$okRoutes = Ejecutar-Comando `
    -Archivo $PhpExe `
    -Argumentos "-c `"$PhpIni`" artisan route:list" `
    -Carpeta $Proyecto `
    -Nombre "php artisan route:list" `
    -TimeoutSeg 120 `
    -MostrarSalida $false

if (-not $okRoutes) {
    ErrorSalir "Laravel no pudo cargar las rutas. Revisa routes/web.php."
}

OK "Rutas de Laravel cargadas correctamente."

# =========================================================
# SERVIDOR WEB CON PHP NATIVO EN LAN
# =========================================================

Titulo "SERVIDOR WEB PHP NATIVO"

$PublicPath = Join-Path $Proyecto "public"

$UrlWebLocal = "http://127.0.0.1:$PuertoLaravel/login"
$UrlsWebLan = @()
foreach ($ip in $IpsLan) {
    $UrlsWebLan += "http://$ip`:$PuertoLaravel/login"
}
$UrlWebLan = $UrlsWebLan[0]

$EchoWebUrls = ""
foreach ($url in $UrlsWebLan) {
    $EchoWebUrls += " & echo Red:   $url"
}

$cmdServidorPhp = "title Sistema Pasaje WEB & color 0A & echo ========================================================= & echo SERVIDOR WEB PHP ACTIVO & echo Local: $UrlWebLocal$EchoWebUrls & echo. & echo NO CIERRES ESTA VENTANA & echo ========================================================= & cd /d `"$Proyecto`" & set `"PHPRC=$PhpIniDir`" & set `"PHP_INI_SCAN_DIR=`" & set `"PATH=$(Split-Path $PhpExe);%PATH%`" & `"$PhpExe`" -c `"$PhpIni`" -S 0.0.0.0:$PuertoLaravel -t `"$PublicPath`" `"$RouterPath`""

Start-Process -FilePath "cmd.exe" -WorkingDirectory $Proyecto -ArgumentList "/k", $cmdServidorPhp

Aviso "Esperando servidor WEB en $UrlWebLocal"

if (Esperar-Web $UrlWebLocal 45) {
    OK "WEB activo localmente: $UrlWebLocal"
    Write-Host ""
    Write-Host "Prueba desde tu celular o laptop con estas URLs:" -ForegroundColor Cyan
    foreach ($url in $UrlsWebLan) {
        Write-Host "  $url" -ForegroundColor Cyan
    }
    Start-Process $UrlWebLocal
} else {
    ErrorSalir "No se pudo iniciar el servidor WEB."
}

# =========================================================
# MOBILE PREVIEW EN LAN
# =========================================================

Titulo "MOBILE PREVIEW"

$MobilePath = Join-Path $Proyecto "Mobile"
$MobileWww = Join-Path $MobilePath "www"

$UrlMobileLocal = "http://127.0.0.1:$PuertoMobile/pages/auth/login.html"
$UrlsMobileLan = @()
foreach ($ip in $IpsLan) {
    $UrlsMobileLan += "http://$ip`:$PuertoMobile/pages/auth/login.html"
}
$UrlMobileLan = $UrlsMobileLan[0]

if (Test-Path $MobileWww) {
    $npm = Get-Command npm -ErrorAction SilentlyContinue

    if ($npm) {
        if (!(Test-Path (Join-Path $MobilePath "node_modules"))) {
            Aviso "Instalando dependencias Mobile..."

            $okNpm = Ejecutar-Comando `
                -Archivo "cmd.exe" `
                -Argumentos "/c set NODE_OPTIONS=--no-deprecation&& npm install --no-fund --no-audit" `
                -Carpeta $MobilePath `
                -Nombre "npm install mobile" `
                -TimeoutSeg 900 `
                -MostrarSalida $true

            if (-not $okNpm) {
                Aviso "npm install fallo. Se abrira Mobile como archivo local."
                Start-Process (Join-Path $MobileWww "pages\auth\login.html")
            }
        }

        $EchoMobileUrls = ""
        foreach ($url in $UrlsMobileLan) {
            $EchoMobileUrls += " & echo Red:   $url"
        }

        $cmdMobile = "title Sistema Pasaje MOBILE & color 0B & echo ========================================================= & echo SERVIDOR MOBILE ACTIVO & echo Local: $UrlMobileLocal$EchoMobileUrls & echo. & echo API configurada contra: http://$IpLan`:$PuertoLaravel/api & echo NO CIERRES ESTA VENTANA & echo ========================================================= & cd /d `"$MobilePath`" & set NODE_OPTIONS=--no-deprecation & npx http-server www -a 0.0.0.0 -p $PuertoMobile -c-1"

        Start-Process -FilePath "cmd.exe" -WorkingDirectory $MobilePath -ArgumentList "/k", $cmdMobile

        Aviso "Esperando Mobile en $UrlMobileLocal"

        if (Esperar-Web $UrlMobileLocal 30) {
            OK "Mobile activo localmente: $UrlMobileLocal"
            Write-Host ""
            Write-Host "Mobile preview desde otro dispositivo:" -ForegroundColor Cyan
            foreach ($url in $UrlsMobileLan) {
                Write-Host "  $url" -ForegroundColor Cyan
            }
            Start-Process $UrlMobileLocal
        } else {
            Aviso "Mobile no respondio por servidor. Abriendo archivo local."
            Start-Process (Join-Path $MobileWww "pages\auth\login.html")
        }
    } else {
        Aviso "npm no encontrado. Abriendo mobile como archivo local."
        Start-Process (Join-Path $MobileWww "pages\auth\login.html")
    }
} else {
    Aviso "No existe Mobile/www. Se omitio Mobile."
}

# =========================================================
# FINAL
# =========================================================

Titulo "LISTO"

Write-Host "WEB local:       $UrlWebLocal"
Write-Host ""
Write-Host "WEB en red para celulares/laptops:" -ForegroundColor Cyan
foreach ($url in $UrlsWebLan) {
    Write-Host "  $url" -ForegroundColor Cyan
}

Write-Host ""
Write-Host "Mobile local:    $UrlMobileLocal"
Write-Host ""
Write-Host "Mobile en red:" -ForegroundColor Cyan
foreach ($url in $UrlsMobileLan) {
    Write-Host "  $url" -ForegroundColor Cyan
}

Write-Host ""
Write-Host "IMPORTANTE:" -ForegroundColor Yellow
Write-Host "1. El celular debe estar en la misma red WiFi que esta PC."
Write-Host "2. Usa una de las URLs de WEB en red."
Write-Host "3. Si no abre desde el celular, ejecuta Iniciar.ps1 como ADMINISTRADOR para abrir Firewall."
Write-Host "4. Si tu red es publica, Windows puede bloquear conexiones entrantes."
Write-Host ""
Write-Host "Si el login abre pero no valida usuarios, falta crear:"
Write-Host "  /api/web/login"
Write-Host "  /api/mobile/login"
Write-Host ""
Write-Host "Base de datos en .env:"
Write-Host "  DB_DATABASE=$NombreBD"
Write-Host "  DB_USERNAME=TU_USUARIO"
Write-Host "  DB_PASSWORD=TU_CONTRASENA"
Write-Host ""

pause
