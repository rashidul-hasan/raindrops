<?php

namespace Rashidul\RainDrops;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Rashidul\RainDrops\Form\Builder;
use Rashidul\RainDrops\Hook\Events;
use Rashidul\RainDrops\JavaScript\LaravelViewBinder;
use Rashidul\RainDrops\JavaScript\PHPToJavaScriptTransformer;
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

        // publish views
        $this->publishes([
            __DIR__.'/views' => resource_path('views/vendor/raindrops'),
        ], 'raindrops');

        // publish configs
        $this->publishes([
            __DIR__ . '/configs' => config_path('raindrops'),
        ], 'raindrops');

        // publish stub files for the generator
        $this->publishes([
            __DIR__ . '/Generator/stubs/' => base_path('resources/raindrops/'),
        ], 'raindrops');

        // for js vars
        // https://github.com/laracasts/PHP-Vars-To-Js-Transformer
        AliasLoader::getInstance()->alias(
            'JavaScript',
            'Rashidul\RainDrops\Facades\JavaScript'
        );

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
        $this->app->bind('formbuilder', function ($app) {
            return new Builder();
        });

        // register datatable builder
        $this->app->bind('datatable-builder', function () {
            return new DataTableBuilder();
        });

        // register details table builder
        $this->app->bind('detailstable', function () {
            return new DetailsTableBuilder();
        });
        //$this->app->alias(DetailsTableBuilder::class, 'detailstable');

        // register hook service providers
        $this->app->singleton('eventy', function ($app) {
            return new Events();
        });

        // register js vars stuffs
        // https://github.com/laracasts/PHP-Vars-To-Js-Transformer
        $this->app->singleton('JavaScript', function ($app) {
            $view = 'raindrops::scripts.php-to-js';
            $namespace = 'raindrops';

            $binder = new LaravelViewBinder($app['events'], $view);

            return new PHPToJavaScriptTransformer($binder, $namespace);
        });

        // load configs
        $this->mergeConfigFrom(
            __DIR__ . '/configs/form.php', 'raindrops.form'
        );

        $this->mergeConfigFrom(
            __DIR__ . '/configs/table.php', 'raindrops.table'
        );

        $this->mergeConfigFrom(
            __DIR__ . '/configs/crud.php', 'raindrops.crud'
        );

        // register console commands
        $this->commands(
            'Rashidul\RainDrops\Generator\Command\ScaffoldCommand',
            'Rashidul\RainDrops\Generator\Command\MakeControllerCommand',
            'Rashidul\RainDrops\Generator\Command\MakeModelCommand',
            'Rashidul\RainDrops\Generator\Command\MakeMigrationCommand'
        );

    }
}
