<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// 1. Siapkan folder storage di /tmp untuk Vercel Read-Only Filesystem
$dirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache'
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
}

putenv('APP_STORAGE=/tmp/storage');
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');

// Cek ketersediaan driver PostgreSQL di Vercel Serverless
if (!extension_loaded('pdo_pgsql') && (getenv('DB_CONNECTION') === 'pgsql' || ($_ENV['DB_CONNECTION'] ?? '') === 'pgsql')) {
    $sqlitePath = '/tmp/database.sqlite';
    if (!file_exists($sqlitePath)) {
        @touch($sqlitePath);
    }
    putenv('DB_CONNECTION=sqlite');
    putenv("DB_DATABASE={$sqlitePath}");
    $_ENV['DB_CONNECTION'] = 'sqlite';
    $_ENV['DB_DATABASE'] = $sqlitePath;
    $_SERVER['DB_CONNECTION'] = 'sqlite';
    $_SERVER['DB_DATABASE'] = $sqlitePath;
}

// 2. Autoload & Bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

if (method_exists($app, 'useStoragePath')) {
    $app->useStoragePath('/tmp/storage');
}

// Register Service Providers secara aman agar service 'view' & 'db' selalu terdaftar
if (!$app->bound('view')) {
    $app->register(new \Illuminate\View\ViewServiceProvider($app));
}
if (!$app->bound('db')) {
    $app->register(new \Illuminate\Database\DatabaseServiceProvider($app));
}

try {
    $kernel = $app->make(Kernel::class);

    $response = $kernel->handle(
        $request = Request::capture()
    );

    $response->send();

    $kernel->terminate($request, $response);
} catch (\Throwable $e) {
    // Apabila terjadi error internal, tampilkan rincian error alih-alih error 500 umum
    http_response_code(500);
    header('Content-Type: text/html; charset=utf-8');
    echo "<!DOCTYPE html><html><head><title>Mubee - Server Error</title>";
    echo "<style>body{font-family:sans-serif;background:#0f172a;color:#f8fafc;padding:2rem;} h1{color:#ef4444;} pre{background:#1e293b;padding:1rem;border-radius:0.5rem;overflow-x:auto;}</style></head><body>";
    echo "<h1>Mubee Deployment Error (500)</h1>";
    echo "<p><strong>Pesan Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . " (Baris " . $e->getLine() . ")</p>";
    echo "<h3>Stack Trace:</h3>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</body></html>";
}