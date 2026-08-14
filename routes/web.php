<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\TvController;

// Utility Setup Route untuk Vercel / Supabase Database Migration
Route::get('/setup-db', function () {
    try {
        $app = app();
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

        return "<div style='font-family:sans-serif;background:#0f172a;color:#4ade80;padding:2rem;border-radius:1rem;'><h1>✅ Migrasi Database Supabase Sukses!</h1><p>Seluruh tabel (users, my_lists, watch_history, dll) dan user <strong>admin@mubee.com</strong> telah berhasil dibuat di Supabase PostgreSQL.</p><a href='/login' style='background:#3b82f6;color:white;padding:0.75rem 1.5rem;text-decoration:none;border-radius:0.5rem;display:inline-block;'>Ke Halaman Login</a></div>";
    } catch (\Throwable $e) {
        return "<div style='font-family:sans-serif;background:#0f172a;color:#ef4444;padding:2rem;'><h1>❌ Gagal Migrasi:</h1><p>" . htmlspecialchars($e->getMessage()) . "</p><pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre></div>";
    }
});

// Auth Routes (Guest Only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update');
});

// Logout Route (Auth Only)
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Netflix-style Streaming Routes
Route::middleware('auth')->group(function () {

    // ------------------------------------------------------------------
    //  Mubee K-Drama Home Dashboard
    // ------------------------------------------------------------------
    Route::get('/', [DashboardController::class, 'index']);
    Route::get('/search', [DashboardController::class, 'search'])->name('search');
    Route::get('/shows/{type}/{id}', [DashboardController::class, 'show']);
    Route::get('/api/shows/{type}/{id}/resume-url', [DashboardController::class, 'getResumeUrl'])->name('shows.resume-url');

    // Custom Navigation Pages
    Route::get('/dramas', [DashboardController::class, 'dramas'])->name('dramas');
    Route::get('/movies', [DashboardController::class, 'movies'])->name('movies.index');
    Route::get('/genres', [DashboardController::class, 'genres'])->name('genres');
    Route::get('/actors', [DashboardController::class, 'actors'])->name('actors');

    // My List
    Route::get('/my-list', [DashboardController::class, 'myList'])->name('mylist');
    Route::post('/my-list/toggle', [DashboardController::class, 'toggleMyList'])->name('mylist.toggle');

    // Settings
    Route::get('/settings', [DashboardController::class, 'settings'])->name('settings');
    Route::post('/settings/update', [DashboardController::class, 'updateSettings'])->name('settings.update');

    // ------------------------------------------------------------------
    //  Movies (TMDB + VidSrc Embed)
    // ------------------------------------------------------------------
    Route::get('/movies', [MovieController::class, 'index'])->name('movies.index');
    Route::get('/movies/{id}', [MovieController::class, 'show'])->name('movies.show');

    // ------------------------------------------------------------------
    //  TV Series / Dramas (TMDB + VidSrc Embed)
    // ------------------------------------------------------------------
    Route::get('/tv/{id}', [TvController::class, 'show'])->name('tv.show');
    Route::get('/tv/{id}/watch/{season}/{episode}', [TvController::class, 'watchEpisode'])->name('tv.watch');

    // ------------------------------------------------------------------
    //  Wireframe & High-Fidelity UI Views
    // ------------------------------------------------------------------
    Route::get('/wireframe', function() {
        return response()->file(public_path('wireframe.html'));
    })->name('wireframe');

    Route::get('/tampilan', function() {
        return response()->file(public_path('tampilan.html'));
    })->name('tampilan');
});
