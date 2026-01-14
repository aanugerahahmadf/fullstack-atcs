<?php

function get_mime_type($filename) {
    $parts = pathinfo($filename);
    $extension = isset($parts['extension']) ? strtolower($parts['extension']) : '';

    $mimes = array(
        'txt' => 'text/plain',
        'html' => 'text/html',
        'php' => 'text/html',
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'xml' => 'application/xml',
        'png' => 'image/png',
        'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'webp' => 'image/webp',
        'gif' => 'image/gif',
        'bmp' => 'image/bmp',
        'ico' => 'image/vnd.microsoft.icon',
        'svg' => 'image/svg+xml',
        'ttf' => 'application/x-font-ttf',
        'woff' => 'application/x-woff',
        'woff2' => 'font/woff2',
    );

    return isset($mimes[$extension]) ? $mimes[$extension] : 'application/octet-stream';
}

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

$file = __DIR__ . '/../public' . $uri;

if ($uri !== '/' && file_exists($file) && !is_dir($file)) {
    header('Content-Type: ' . get_mime_type($file));
    readfile($file);
    exit;
}

require __DIR__ . '/../public/index.php';
