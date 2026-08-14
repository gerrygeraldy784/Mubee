<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// 1. Siapkan semua folder storage yang dibutuhkan di /tmp (Vercel Read-Only Filesystem Fix)
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

// 2. Cek Driver Database (Fallback ke SQLite jika pdo_pgsql tidak tersedia di Vercel)
$isNewDb = false;
if (!extension_loaded('pdo_pgsql') && (getenv('DB_CONNECTION') === 'pgsql' || ($_ENV['DB_CONNECTION'] ?? '') === 'pgsql')) {
    $sqlitePath = '/tmp/database.sqlite';
    $isNewDb = !file_exists($sqlitePath) || filesize($sqlitePath) === 0;
    if ($isNewDb) {
        @touch($sqlitePath);
    }
    putenv('DB_CONNECTION=sqlite');
    putenv("DB_DATABASE={$sqlitePath}");
    $_ENV['DB_CONNECTION'] = 'sqlite';
    $_ENV['DB_DATABASE'] = $sqlitePath;
    $_SERVER['DB_CONNECTION'] = 'sqlite';
    $_SERVER['DB_DATABASE'] = $sqlitePath;
}

// 3. Autoload & Inisialisasi Aplikasi Laravel
require __DIR__ . '/../vendor/autoload.php';

/** @var \Illuminate\Foundation\Application $app */
$app = require_once __DIR__ . '/../bootstrap/app.php';

if (method_exists($app, 'useStoragePath')) {
    $app->useStoragePath('/tmp/storage');
}

// Jika SQLite baru dibuat di /tmp, jalankan migrasi secara otomatis
if ($isNewDb) {
    try {
        /** @var \Illuminate\Contracts\Console\Kernel $consoleKernel */
        $consoleKernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
        $consoleKernel->call('migrate', ['--force' => true]);
        $consoleKernel->call('db:seed', ['--force' => true]);
    } catch (\Throwable $e) {
        // Abaikan error migrasi jika tabel sudah ada
    }
}

// 4. Handle HTTP Request menggunakan mekanisme standar Laravel 11
$app->handleRequest(Request::capture());