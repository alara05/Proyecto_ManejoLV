<?php

$envPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env';

if (! is_file($envPath)) {
    fwrite(STDERR, "No se encontro el archivo .env.\n");
    exit(1);
}

$env = parse_ini_file($envPath, false, INI_SCANNER_RAW);

if ($env === false) {
    fwrite(STDERR, "No se pudo leer el archivo .env.\n");
    exit(1);
}

$connection = strtolower(trimValue($env['DB_CONNECTION'] ?? ''));

if ($connection !== 'mysql') {
    echo "La conexion no es MySQL. Se omite preparacion automatica.\n";
    exit(0);
}

$database = trimValue($env['DB_DATABASE'] ?? 'manejo_buses');
$host = trimValue($env['DB_HOST'] ?? '127.0.0.1');
$port = trimValue($env['DB_PORT'] ?? '3306');
$username = trimValue($env['DB_USERNAME'] ?? 'root');
$password = trimValue($env['DB_PASSWORD'] ?? '');

try {
    $pdo = new PDO(
        "mysql:host={$host};port={$port}",
        $username,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $safeDatabase = str_replace('`', '``', $database);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$safeDatabase}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

    echo "Base de datos lista: {$database}\n";
    exit(0);
} catch (Throwable $exception) {
    fwrite(STDERR, "No se pudo conectar a MySQL: {$exception->getMessage()}\n");
    exit(1);
}

function trimValue(string $value): string
{
    return trim(trim($value), "\"'");
}
