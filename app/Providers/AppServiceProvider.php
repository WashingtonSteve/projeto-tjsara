<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
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
     *
     * Event listeners (e.g. App\Listeners\LogTaskCompletion) are picked up
     * automatically by Laravel's event auto-discovery from their handle()
     * method's type-hint - no explicit Event::listen() needed.
     */
    public function boot(): void
    {
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Scramble's API docs (/docs/api) are restricted to the "local"
        // environment by default. This is a public portfolio API with no
        // real user data behind it, so the docs are left open everywhere -
        // that's the whole point of publishing them.
        Gate::define('viewApiDocs', fn (?object $user = null): bool => true);
    }
}
