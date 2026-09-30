<?php
// server.php - Router for PHP built-in web server
$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? ''
);

// If file exists in public directory, serve it directly
$publicFile = __DIR__ . '/public' . $uri;
if ($uri !== '/' && file_exists($publicFile) && !is_dir($publicFile)) {
    return false;
}

// Otherwise route through public/index.php
require_once __DIR__ . '/public/index.php';

