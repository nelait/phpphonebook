<?php

// Front controller — Router for PHP built-in dev server
if (php_sapi_name() === 'cli-server') {
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $file = __DIR__ . $uri;
    if ($uri !== '/' && is_file($file)) {
        return false; // serve static files (CSS, images, etc.)
    }
}

require_once __DIR__ . '/../vendor/autoload.php';

use PhpGuru\Application;

(new Application())->start();
