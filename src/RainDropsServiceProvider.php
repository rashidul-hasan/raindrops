<?php

namespace Rashidul\RainDrops;

use Illuminate\Support\ServiceProvider;
use Rashidul\RainDrops\Form\Builder;
use Rashidul\RainDrops\Table\DataTableBuilder;
use Rashidul\RainDrops\Table\DetailsTableBuilder;

class RainDropsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/views', 'raindrops');

        // publish configs
        $this->publishes([
            __DIR__ . '/configs' => config_path('raindrops'),
        ], 'raindrops');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        include __DIR__.'/routes/routes.php';

        // register form builder
        $this->app->singleton(Builder::class, function () {
            return new Builder();
        });
        $this->app->alias(Builder::class, 'formbuilder');

        // register datatable builder
        $this->app->singleton(DataTableBuilder::class, function () {
            return new DataTableBuilder();
        });
        $this->app->alias(DataTableBuilder::class, 'indexbuilder');

        // register details table builder
        $this->app->singleton(DetailsTableBuilder::class, function () {
            return new DetailsTableBuilder();
        });
        $this->app->alias(DetailsTableBuilder::class, 'detailstable');

        // load configs
        $this->mergeConfigFrom(
            __DIR__ . '/configs/form.php', 'raindrops.form'
        );

        $this->mergeConfigFrom(
            __DIR__ . '/configs/datatable.php', 'raindrops.datatable'
        );

        $this->mergeConfigFrom(
            __DIR__ . '/configs/crud.php', 'raindrops.crud'
        );

    }
}
