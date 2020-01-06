<?php

namespace Rashidul\RainDrops\Generator\Command;

use File;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Nwidart\Modules\Facades\Module;
use Illuminate\Support\Str;

class ScaffoldCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module
                            {entity : The name of the Crud.}
                            {--table= : Fields name for the form & migration.}
                            {--route= : Base route, default is table name.}
                            {--fields_from_file= : Fields from a json file.}
                            {--validations= : Validation details for the fields.}
                            {--controller-namespace= : Namespace of the controller.}
                            {--model-namespace= : Namespace of the model inside "app" dir.}
                            {--pk=id : The name of the primary key.}
                            {--pagination=25 : The amount of models per page for index pages.}
                            {--indexes= : The fields to add an index to.}
                            {--foreign-keys= : The foreign keys for the table.}
                            {--relationships= : The relationships for the model.}
                            {--module= : Specify which module this crud belongs to.}
                            {--route-group= : Prefix of the route group.}
                            {--view-path= : The name of the view path.}
                            {--localize=no : Allow to localize? yes|no.}
                            {--locales=en : Locales language type.}
                            {--fields= : Fields for the tables.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaffold basic CRUD components';

    /** @var string  */
    protected $routeName = '';

    /** @var string  */
    protected $controller = '';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $entity = $this->argument('entity');
        $tableName = ($this->option('table')) ? $this->option('table') : Str::plural(Str::snake($entity));
        $this->routeName = ($this->option('route')) ? $this->option('route') : $tableName;
        $fields = rtrim($this->option('fields'), ';');
        $migrationName = $tableName;

        $controllerNamespace = '';
        $modelNamespace = 'App\\';
        $migrationDirectory = '';

        // location of the routes file
        $routeFile = app_path('Http/routes.php');

        if (\App::VERSION() >= '5.3') {
            $routeFile = base_path('routes/web.php');
        }

        //$controllerNamespace = ($this->option('controller-namespace')) ? $this->option('controller-namespace') . '\\' : '';
        //$modelNamespace = ($this->option('model-namespace')) ? trim($this->option('model-namespace')) . '\\' : '';

        // if module option is provided, resolve the namespace for controllers, models
        // migration files and route file
        if ($this->option('module'))
        {
            // if nwidart/laravel-modules package is installed
            if (Config::has('modules')) {

                // get module instance by name
                $moduleName = null;
                try
                {
                    $moduleName = Module::find($this->option('module'));
                    if ($moduleName != null)
                    {
                        $moduleName = $moduleName->getStudlyName();
                    }
                    else
                    {
                        $this->error('Specified module not found!');
                        $this->error('Aborting...');
                        dd();
                    }
                }
                catch(\Exception $e)
                {
                    $this->error($e->getMessage());
                    $this->error('Aborting...');
                    dd();
                }
                $moduleConfigs = Config::get('modules');

                // controller namepsace
                $controllerNamespace = $moduleConfigs['namespace'] . '\\' . $moduleName . '\\'
                    . str_replace('/', '\\', $moduleConfigs['paths']['generator']['controller'])
                    . '\\';

                // model namespace
                $modelNamespace = $moduleConfigs['namespace'] . '\\' . $moduleName . '\\'
                    . str_replace('/', '\\', $moduleConfigs['paths']['generator']['model'])
                    . '\\';

                // migration directory
                $migrationDirectory = Module::find($this->option('module'))->getExtraPath($moduleConfigs['paths']['generator']['migration']);
                $migrationDirectory = str_replace('/', '\\', $migrationDirectory);
                // route file
                $routeFile = Module::find($this->option('module'))->getExtraPath('Http') . '/routes.php';
            }
            else
            {
                $this->error('Modules package isn\'t found!');
                $this->error('Aborting...');
                dd();
            }
        }

        $this->call('raindrops:controller', ['name' => $controllerNamespace . $entity . 'Controller', '--model-name' => $entity, '--model-namespace' => $modelNamespace]);
        $this->call('raindrops:model', ['name' => $modelNamespace . $entity, '--table' => $tableName, '--route' => $this->routeName, '--fields' => $fields]);
        $this->call('raindrops:migration', ['name' => $migrationName, '--schema' => $fields, '--path' => $migrationDirectory    /*, '--pk' => $primaryKey, '--indexes' => $indexes, '--foreign-keys' => $foreignKeys*/]);


        // For optimizing the class loader
        //$this->info('Optimizing class loader...');
        //$this->callSilent('optimize');

        // Updating the Http/routes.php file


        if (file_exists($routeFile)) {
            //$this->controller = ($controllerNamespace != '') ? $controllerNamespace . '\\' . $entity . 'Controller' : $entity . 'Controller';
            $this->controller = $entity . 'Controller';

            $isAdded = File::append($routeFile, "\n" . implode("\n", $this->addRoutes()));

            if ($isAdded) {
                $this->info('Crud/Resource route added to ' . $routeFile);
            } else {
                $this->info('Unable to add the route to ' . $routeFile);
            }
        }

        $this->info('You\'re Done! Yeee!');


    }

    /**
     * Add routes.
     *
     * @return  array
     */
    protected function addRoutes()
    {
        return ["Route::resource('" . $this->routeName . "', '" . $this->controller . "');"];
    }

    /**
     * Process the JSON Fields.
     *
     * @param  string $file
     *
     * @return string
     */
    protected function processJSONFields($file)
    {
        $json = File::get($file);
        $fields = json_decode($json);

        $fieldsString = '';
        foreach ($fields->fields as $field) {
            if ($field->type == 'select') {
                $fieldsString .= $field->name . '#' . $field->type . '#options=' . implode(',', $field->options) . ';';
            } else {
                $fieldsString .= $field->name . '#' . $field->type . ';';
            }
        }

        $fieldsString = rtrim($fieldsString, ';');

        return $fieldsString;
    }

    /**
     * Process the JSON Foreign keys.
     *
     * @param  string $file
     *
     * @return string
     */
    protected function processJSONForeignKeys($file)
    {
        $json = File::get($file);
        $fields = json_decode($json);

        if (! property_exists($fields, 'foreign_keys')) {
            return '';
        }

        $foreignKeysString = '';
        foreach ($fields->foreign_keys as $foreign_key) {
            $foreignKeysString .= $foreign_key->column . '#' . $foreign_key->references . '#' . $foreign_key->on;

            if (property_exists($foreign_key, 'onDelete')) {
                $foreignKeysString .= '#' . $foreign_key->onDelete;
            }

            if (property_exists($foreign_key, 'onUpdate')) {
                $foreignKeysString .= '#' . $foreign_key->onUpdate;
            }

            $foreignKeysString .= ',';
        }

        $foreignKeysString = rtrim($foreignKeysString, ',');

        return $foreignKeysString;
    }

    /**
     * Process the JSON Relationships.
     *
     * @param  string $file
     *
     * @return string
     */
    protected function processJSONRelationships($file)
    {
        $json = File::get($file);
        $fields = json_decode($json);

        if (!property_exists($fields, 'relationships')) {
            return '';
        }

        $relationsString = '';
        foreach ($fields->relationships as $relation) {
            $relationsString .= $relation->name . '#' . $relation->type . '#' . $relation->class . ';';
        }

        $relationsString = rtrim($relationsString, ';');

        return $relationsString;
    }

    /**
     * Process the JSON Validations.
     *
     * @param  string $file
     *
     * @return string
     */
    protected function processJSONValidations($file)
    {
        $json = File::get($file);
        $fields = json_decode($json);

        if (!property_exists($fields, 'validations')) {
            return '';
        }

        $validationsString = '';
        foreach ($fields->validations as $validation) {
            $validationsString .= $validation->field . '#' . $validation->rules . ';';
        }

        $validationsString = rtrim($validationsString, ';');

        return $validationsString;
    }
}
