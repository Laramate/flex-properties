<?php

namespace Laramate\FlexProperties\Providers;

use Illuminate\Support\ServiceProvider;

class FlexPropertyServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->registerPublishing();
            $this->loadMigrationsFrom(__DIR__ . '/../Migrations');
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../Config/Config.php', 'flex-properties');
    }

    /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    protected function registerPublishing()
    {
        $this->publishes([
            __DIR__ . '/../Config/Config.php' => config_path('flex-properties.php'),
        ], 'laravel-flex-property-config');

        $this->publishes(
            [__DIR__ . '/../Migrations' => database_path('migrations')],
            'laravel-flex-property-migrations'
        );
    }
}
