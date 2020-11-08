<?php

namespace Kanuu\Laravel;

use Illuminate\Support\ServiceProvider;

class KanuuServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishConfig();
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/kanuu.php', 'kanuu');

        $this->app->singleton(Kanuu::class, function () {
            return new Kanuu(config('kanuu.api_key'), config('kanuu.base_url'));
        });

        $this->app->bind('kanuu', Kanuu::class);
    }

    protected function publishConfig()
    {
        $this->publishes([
            __DIR__ . '/../config/kanuu.php' => config_path('kanuu.php'),
        ], 'config');
    }
}
