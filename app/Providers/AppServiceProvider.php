<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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
        // 1. Paksa HTTPS di Vercel / Production
        if (!app()->isLocal()) {
            URL::forceScheme('https');
        }

        // 2. Proteksi N+1 Query & Strict Model di Development
        Model::shouldBeStrict(!app()->isProduction());
    }
}
