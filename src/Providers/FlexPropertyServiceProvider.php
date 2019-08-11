<?php

namespace Laramate\FlexProperties\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Laramate\StructuredDocument\Models\Block;
use Laramate\StructuredDocument\Models\Document;
use Laramate\StructuredDocument\Models\Layer;

class FlexPropertyServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->bootMorphMap();

        if ($this->app->runningInConsole()) {
            $this->registerPublishing();
            $this->loadMigrationsFrom(__DIR__ . '/../Migrations');
        }
    }

    /**
     * Create morph maps for the structured document models.
     */
    protected function bootMorphMap()
    {
        $config = Config::get('flex-properties.types');

        $map = collect($config)->mapWithKeys(function ($item, $key) {
            return ['flex_' . $key => $item];
        });

        Relation::morphMap($map->toArray());
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
