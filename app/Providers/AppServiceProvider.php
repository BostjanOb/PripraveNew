<?php

namespace App\Providers;

use App\Auth\LegacyMd5UserProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
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
        Model::unguard();

        Auth::provider('legacy-eloquent', function ($app, array $config) {
            return new LegacyMd5UserProvider($app['hash'], $config['model']);
        });
    }
}
