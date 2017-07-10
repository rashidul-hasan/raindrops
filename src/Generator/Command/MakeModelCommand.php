<?php

namespace Rashidul\RainDrops\Generator\Command;

use Illuminate\Console\GeneratorCommand;
use Rashidul\RainDrops\Generator\Helper;

class MakeModelCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'raindrops:model
                            {name : The name of the model.}
                            {--table= : The name of the table.}
                            {--route= : Base route for the model.}
                            {--fields= : Database fields.}
                            {--fillable= : The names of the fillable columns.}
                            {--relationships= : The relationships for the model}
                            {--pk=id : The name of the primary key.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a new model';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Model';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return config('raindrops.crud.generator.custom_template')
            ? config('raindrops.crud.generator.stubs') . '/model.stub'
            : __DIR__ . '/../stubs/model.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace;
    }

    /**
     * Build the model class with the given name.
     *
     * @param  string  $name
     *
     * @return string
     */
    protected function buildClass($entity)
    {
        $stub = $this->files->get($this->getStub());

        $fields = $this->generateFieldsArray(rtrim($this->option('fields'), ';'));

        $table = $this->option('table') ? $this->option('table') : str_plural(snake_case($this->option('name')));
        $fillable = array_keys($fields);
        $route = ($this->option('route')) ? $this->option('route') : $table;


        $ret = $this->replaceNamespace($stub, $entity)
            ->replaceFields($stub, $fields)
            ->replaceTable($stub, $table)
            ->replaceFillable($stub, $fillable)
            ->replaceEntityName($stub, $this->argument('name'))
            ->replaceRoute($stub, $route);

        return $ret->replaceClass($stub, $entity);
    }

    /**
     * Replace the table for the given stub.
     *
     * @param  string  $stub
     * @param  string  $table
     *
     * @return $this
     */
    protected function replaceTable(&$stub, $table)
    {
        $stub = str_replace(
            '{{table}}', $table, $stub
        );

        return $this;
    }

    /**
     * Replace the fillable for the given stub.
     *
     * @param  string  $stub
     * @param  string  $fillable
     *
     * @return $this
     */
    protected function replaceFillable(&$stub, $fillable)
    {
        $helper =  new Helper();
        $stub = str_replace(
            '{{fillable}}', $helper->arrayAsString($fillable), $stub
        );

        return $this;
    }

    /**
     * REplace baseUrl
     * @param $stub
     * @param $route
     * @return $this
     */
    protected function replaceRoute(&$stub, $route)
    {
        $stub = str_replace('{{route}}', $route, $stub);
        return $this;
    }

    protected function replaceEntityName(&$stub, $entity)
    {
        $stub = str_replace('{{entityNamePlural}}', str_plural($entity), $stub);
        return $this;
    }

    protected function replaceFields(&$stub, $fields)
    {
       // $file = file_put_contents(__DIR__ . '/../stubs/fields.stub', var_export($fields, true));
//        $fields = file_get_contents(__DIR__ . '/../stubs/fields.stub');
        $helper =  new Helper();
        $fields = $helper->arrayAsString($fields);
        $stub = str_replace('{{fields}}', $fields, $stub);
        return $this;
    }

    protected function generateFieldsArray($option)
    {
        $fields = explode(';', $option);


        $data = [];

        if ($fields) {
            foreach ($fields as $field) {

                $fieldArray = explode('#', $field);

                $fieldOptionsArray = [];

                $fieldName = trim($fieldArray[0]);

                // build options array
                //label
                $fieldOptionsArray['label'] = ucwords(str_replace("_", " ", $fieldName));

                // type
                if (isset($fieldArray[1]))
                {
                    $fieldOptionsArray['type'] = trim($fieldArray[1]);
                }

                // select options
                // syntax: name#select#option1,option2,option3
                if ( isset($fieldArray[1]) && $fieldArray[1] === 'select')
                {
                    $fieldOptionsArray['type'] = trim($fieldArray[1]);

                    $fieldOptionsArray['options'] = [];
                    // if options are provided for the select type
                    // those will be in the 3rd key
                    if (isset($fieldArray[2]))
                    {
                        $options = [];
                        $optionArray = explode(',', $fieldArray[2]);
                        foreach ($optionArray as $option)
                        {
                            $options[$option] = str_replace('_', ' ', ucwords($option));
                        }
                        $fieldOptionsArray['options'] = $options;
                    }
                }


                $data[$fieldName] = $fieldOptionsArray;

            }
        }

        return $data;

    }
}
