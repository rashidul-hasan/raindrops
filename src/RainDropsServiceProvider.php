<?php

namespace Rashidul\RainDrops;

use Illuminate\Support\ServiceProvider;
use Rashidul\RainDrops\Form\Builder;
use Rashidul\RainDrops\Hook\Events;
use Rashidul\RainDrops\Route\ResourceRegister;
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
        // load views
        $this->loadViewsFrom(__DIR__.'/views', 'raindrops');

        // publish configs
        $this->publishes([
            __DIR__ . '/configs' => config_path('raindrops'),
        ], 'raindrops');

        // register new resource route methods
        $registrar = new ResourceRegister($this->app['router']);

        $this->app->bind('Illuminate\Routing\ResourceRegistrar', function () use ($registrar) {
            return $registrar;
        });

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
        $this->app->singleton('formbuilder', function ($app) {
            return new Builder();
        });

        // register datatable builder
        $this->app->singleton('indexbuilder', function () {
            return new DataTableBuilder();
        });

        // register details table builder
        $this->app->singleton('detailstable', function () {
            return new DetailsTableBuilder();
        });
        //$this->app->alias(DetailsTableBuilder::class, 'detailstable');

        // register hook service providers
        $this->app->singleton('eventy', function ($app) {
            return new Events();
        });

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
