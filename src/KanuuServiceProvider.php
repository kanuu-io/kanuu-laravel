<?php

namespace Kanuu\Laravel;

use Illuminate\Support\ServiceProvider;
use Kanuu\Laravel\Commands\KanuuPublishCommand;
use Kanuu\Laravel\Exceptions\KanuuApiKeyMissingException;

class KanuuServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishConfig();
            $this->commands([
                KanuuPublishCommand::class,
            ]);
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/kanuu.php', 'kanuu');

        $this->app->singleton(Kanuu::class, function () {
            return $this->resolveKanuuManager();
        });

        $this->app->bind('kanuu', Kanuu::class);
    }

    protected function publishConfig()
    {
        $this->publishes([
            __DIR__ . '/../config/kanuu.php' => config_path('kanuu.php'),
        ], ['config', 'kanuu-config']);
    }

    protected function resolveKanuuManager(): Kanuu
    {
        return new Kanuu(config('kanuu.api_key'), config('kanuu.base_url'));
    }
}
