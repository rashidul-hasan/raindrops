<?php

namespace Rashidul\RainDrops;

use Illuminate\Support\ServiceProvider;
use Rashidul\RainDrops\Form\Builder;

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
            __DIR__ . '/config' => config_path('raindrops'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        include __DIR__.'/routes.php';

        // register form builder
        $this->app->singleton(Builder::class, function () {
            return new Builder();
        });
        $this->app->alias(Builder::class, 'formbuilder');

        // load configs
        $this->mergeConfigFrom(
            __DIR__ . '/config/form.php', 'raindrops.form'
        );

    }
}
