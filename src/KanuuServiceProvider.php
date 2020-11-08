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
    }

    protected function publishConfig()
    {
        $this->publishes([
            __DIR__ . '/../config/kanuu.php' => config_path('kanuu.php'),
        ], 'config');
    }
}
