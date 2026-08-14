<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS URLs, routes, forms, and redirects on production & Vercel
        if ($this->app->environment('production') || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) || getenv('VERCEL')) {
            URL::forceScheme('https');
        }
    }
}
