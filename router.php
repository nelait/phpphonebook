<?php
// Router script for PHP built-in development server
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Serve static files from public/
if ($uri !== '/' && file_exists(__DIR__ . '/public' . $uri)) {
    return false;
}

// Route all other requests through the front controller
require __DIR__ . '/public/index.php';
