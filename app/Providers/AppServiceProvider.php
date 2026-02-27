<?php

namespace App\Providers;

use App\Auth\LegacyMd5UserProvider;
use App\Services\Browse\BrowseSearchService;
use App\Services\Browse\MeilisearchBrowseSearchService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Number;
use Illuminate\Support\ServiceProvider;
use Meilisearch\Client;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Client::class, function (): Client {
            return new Client(
                config('scout.meilisearch.host'),
                config('scout.meilisearch.key')
            );
        });

        $this->app->bind(BrowseSearchService::class, MeilisearchBrowseSearchService::class);
    }

    public function boot(): void
    {
        Model::unguard();
        Number::useLocale('sl');

        Auth::provider('legacy-eloquent', function ($app, array $config) {
            return new LegacyMd5UserProvider($app['hash'], $config['model']);
        });
    }
}
