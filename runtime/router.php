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
