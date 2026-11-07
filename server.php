<?php
// server.php - Router for PHP built-in web server
$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? ''
);

// If file exists in public directory, serve it directly
$publicFile = __DIR__ . '/public' . $uri;
if ($uri !== '/' && file_exists($publicFile) && !is_dir($publicFile)) {
    // If running with -t public, return false allows built-in server to serve directly
    if (isset($_SERVER['DOCUMENT_ROOT']) && realpath($_SERVER['DOCUMENT_ROOT']) === realpath(__DIR__ . '/public')) {
        return false;
    }

    // Otherwise serve file directly with proper headers
    $extension = strtolower(pathinfo($publicFile, PATHINFO_EXTENSION));
    $mimes = [
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'svg' => 'image/svg+xml',
        'css' => 'text/css',
        'js' => 'application/javascript',
    ];
    $contentType = $mimes[$extension] ?? (mime_content_type($publicFile) ?: 'application/octet-stream');
    header('Content-Type: ' . $contentType);
    header('Content-Length: ' . filesize($publicFile));
    readfile($publicFile);
    exit;
}

// Otherwise route through public/index.php
require_once __DIR__ . '/public/index.php';

