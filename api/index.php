<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// 1. Siapkan semua folder storage & bootstrap cache di /tmp (Vercel Read-Only Filesystem Fix)
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
$_ENV['APP_STORAGE'] = '/tmp/storage';
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';
$_SERVER['APP_STORAGE'] = '/tmp/storage';
$_SERVER['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';

// 2. Fallback ke SQLite jika driver PostgreSQL (pdo_pgsql) tidak tersedia di Vercel Serverless
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

// 3. Autoload & Inisialisasi Aplikasi Laravel
require __DIR__ . '/../vendor/autoload.php';

/** @var \Illuminate\Foundation\Application $app */
$app = require_once __DIR__ . '/../bootstrap/app.php';

if (method_exists($app, 'useStoragePath')) {
    $app->useStoragePath('/tmp/storage');
}
if (method_exists($app, 'useBootstrapPath')) {
    $app->useBootstrapPath('/tmp/bootstrap');
}

// Overwrite instansiasi PackageManifest agar manifestPath menggunakan /tmp/bootstrap/cache/packages.php
$app->instance(\Illuminate\Foundation\PackageManifest::class, new \Illuminate\Foundation\PackageManifest(
    new \Illuminate\Filesystem\Filesystem,
    $app->basePath(),
    '/tmp/bootstrap/cache/packages.php'
));

// Registrasi Service Provider inti (files & view) secara eksplisit untuk Serverless Vercel
$app->register(Illuminate\Filesystem\FilesystemServiceProvider::class);
$app->register(Illuminate\View\ViewServiceProvider::class);

// Paksa seluruh konfigurasi path penyimpanan internal Laravel mengarah ke /tmp & Auto-Migrate Database
$app->booted(function () use ($app) {
    $app['config']->set('view.compiled', '/tmp/storage/framework/views');
    $app['config']->set('session.files', '/tmp/storage/framework/sessions');
    $app['config']->set('cache.stores.file.path', '/tmp/storage/framework/cache/data');
    $app['config']->set('logging.channels.single.path', '/tmp/storage/logs/laravel.log');

    // Otomatis jalankan migrasi & seeder via native Migrator jika database belum memiliki tabel "users"
    try {
        if (!\Illuminate\Support\Facades\Schema::hasTable('users')) {
            /** @var \Illuminate\Database\Migrations\Migrator $migrator */
            $migrator = $app->make('migrator');
            if (!$migrator->repositoryExists()) {
                $migrator->getRepository()->createRepository();
            }
            $migrator->run([database_path('migrations')], ['force' => true]);

            if (!\App\Models\User::where('email', 'admin@mubee.com')->exists()) {
                \App\Models\User::create([
                    'name' => 'Admin Mubee',
                    'email' => 'admin@mubee.com',
                    'password' => bcrypt('password'),
                ]);
            }
        }
    } catch (\Throwable $e) {
        // Abaikan jika database bermasalah saat dikueri awal
    }
});

// 4. Handle HTTP Request menggunakan mekanisme standar Laravel 11
$app->handleRequest(Request::capture());