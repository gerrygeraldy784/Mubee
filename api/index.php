<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

define('LARAVEL_START', microtime(true));

// 1. Buat folder temporary & database SQLite di /tmp (karena Vercel serverless bersifat Read-Only)
$dirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache'
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

if (!file_exists('/tmp/database.sqlite')) {
    touch('/tmp/database.sqlite');
}

// 2. Set environment variables untuk storage & SQLite di /tmp
putenv('APP_STORAGE=/tmp/storage');
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
putenv('DB_CONNECTION=sqlite');
putenv('DB_DATABASE=/tmp/database.sqlite');

// 3. Autoload Composer & Bootstrap Application
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

// 4. Paksa Laravel menggunakan storage di /tmp (Read-Only fix)
$app->useStoragePath('/tmp/storage');

// 5. Tangani Request HTTP
$app->handleRequest(\Illuminate\Http\Request::capture());