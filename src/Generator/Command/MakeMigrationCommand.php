<?php

namespace Rashidul\RainDrops\Generator\Command;

use Illuminate\Console\GeneratorCommand;

class MakeMigrationCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'raindrops:migration
                            {name : The name of the migration.}
                            {--schema= : The name of the schema.}
                            {--indexes= : The fields to add an index too.}
                            {--foreign-keys= : Foreign keys.}
                            {--pk=id : The name of the primary key.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a new migration';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Migration';

    /**
     *  Migration column types collection.
     *
     * @var array
     */
    protected $typeLookup = [
        'date' => 'date',
        'datetime' => 'dateTime',
        'time' => 'time',
        'text' => 'string',
        'textarea' => 'mediumText',
        'richtext' => 'longText',
        'jsonb' => 'jsonb',
        'binary' => 'binary',
        'number' => 'integer',
        'integer' => 'integer',
        'bigint' => 'bigInteger',
        'mediumint' => 'mediumInteger',
        'tinyint' => 'tinyInteger',
        'smallint' => 'smallInteger',
        'boolean' => 'boolean',
        'decimal' => 'decimal',
        'double' => 'double',
        'float' => 'float',
        'select' => 'enum',
    ];
    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return config('raindrops.crud.generator.custom_template')
        ? config('raindrops.crud.generator.stubs') . '/migration.stub'
        : __DIR__ . '/../stubs/migration.stub';
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     *
     * @return string
     */
    protected function getPath($name)
    {
        $name = str_replace($this->laravel->getNamespace(), '', $name);
        $datePrefix = date('Y_m_d_His');

        return database_path('/migrations/') . $datePrefix . '_create_' . $name . '_table.php';
    }

    /**
     * Build the model class with the given name.
     *
     * @param  string  $name
     *
     * @return string
     */
    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());

        $tableName = $this->argument('name');
        $className = 'Create' . str_replace(' ', '', ucwords(str_replace('_', ' ', $tableName))) . 'Table';

        $fieldsToIndex = trim($this->option('indexes')) != '' ? explode(',', $this->option('indexes')) : [];
        $foreignKeys = trim($this->option('foreign-keys')) != '' ? explode(',', $this->option('foreign-keys')) : [];

        $schema = rtrim($this->option('schema'), ';');
        $fields = explode(';', $schema);

        $data = array();

        if ($schema) {
            $x = 0;
            foreach ($fields as $field) {
                $fieldArray = explode('#', $field);
                $data[$x]['name'] = trim($fieldArray[0]);
                $data[$x]['type'] = isset($fieldArray[1]) ? trim($fieldArray[1]) : '';

                // for select type
                if ( isset($fieldArray[1]) && $fieldArray[1] === 'select')
                {
                    $data[$x]['type'] = trim($fieldArray[1]);

                    $data[$x]['options'] = [];
                    // if options are provided for the select type
                    // those will be in the 3rd key
                    if (isset($fieldArray[2]))
                    {
                        $options = [];
                        $optionArray = explode(',', $fieldArray[2]);
                        /*foreach ($optionArray as $option)
                        {
                            $options[$option] = str_replace('_', ' ', ucwords($option));
                        }*/
                        $data[$x]['options'] = $optionArray;
                    }
                }

                $data[$x]['modifier'] = '';

                $modifierLookup = [
                    'comment',
                    'default',
                    'first',
                    'nullable',
                    'unsigned',
                ];

                if (isset($fieldArray[2]) && in_array(trim($fieldArray[2]), $modifierLookup)) {
                    $data[$x]['modifier'] = "->" . trim($fieldArray[2]) . "()";
                }

                $x++;
            }
        }

        $tabIndent = '    ';

        $schemaFields = '';
        foreach ($data as $item) {
            if (isset($this->typeLookup[$item['type']]))
            {

                // for select type, there should be a 3rd array item called options
                // build an array for enum options
                if ($item['type'] === 'select')
                {
                    $type = $this->typeLookup[$item['type']];
                    $optionsArray = '';
                    if (isset($item['options']) )
                    {
                        $optionsArray = $this->arrayToString($item['options']);
                    }
                    $schemaFields .= "\$table->" . $type . "('" . $item['name'] . "', ". $optionsArray .")";
                }
                else
                {
                    $type = $this->typeLookup[$item['type']];

                    $schemaFields .= "\$table->" . $type . "('" . $item['name'] . "')";
                }

            }


            else
            {
                $schemaFields .= "\$table->string('" . $item['name'] . "')";
            }



            // Append column modifier
            $schemaFields .= $item['modifier'];
            $schemaFields .= ";\n" . $tabIndent . $tabIndent . $tabIndent;
        }

        // add indexes and unique indexes as necessary
        foreach ($fieldsToIndex as $fldData) {
            $line = trim($fldData);

            // is a unique index specified after the #?
            // if no hash present, we append one to make life easier
            if (strpos($line, '#') === false) {
                $line .= '#';
            }

            // parts[0] = field name (or names if pipe separated)
            // parts[1] = unique specified
            $parts = explode('#', $line);
            if (strpos($parts[0], '|') !== 0) {
                $fieldNames = "['" . implode("', '", explode('|', $parts[0])) . "']"; // wrap single quotes around each element
            } else {
                $fieldNames = trim($parts[0]);
            }

            if (count($parts) > 1 && $parts[1] == 'unique') {
                $schemaFields .= "\$table->unique(" . trim($fieldNames) . ")";
            } else {
                $schemaFields .= "\$table->index(" . trim($fieldNames) . ")";
            }

            $schemaFields .= ";\n" . $tabIndent . $tabIndent . $tabIndent;
        }

        // foreign keys
        foreach ($foreignKeys as $fk) {
            $line = trim($fk);

            $parts = explode('#', $line);

            // if we don't have three parts, then the foreign key isn't defined properly
            // --foreign-keys="foreign_entity_id#id#foreign_entity#onDelete#onUpdate"
            if (count($parts) == 3) {
                $schemaFields .= "\$table->foreign('" . trim($parts[0]) . "')"
                . "->references('" . trim($parts[1]) . "')->on('" . trim($parts[2]) . "')";
            } elseif (count($parts) == 4) {
                $schemaFields .= "\$table->foreign('" . trim($parts[0]) . "')"
                . "->references('" . trim($parts[1]) . "')->on('" . trim($parts[2]) . "')"
                . "->onDelete('" . trim($parts[3]) . "')" . "->onUpdate('" . trim($parts[3]) . "')";
            } elseif (count($parts) == 5) {
                $schemaFields .= "\$table->foreign('" . trim($parts[0]) . "')"
                . "->references('" . trim($parts[1]) . "')->on('" . trim($parts[2]) . "')"
                . "->onDelete('" . trim($parts[3]) . "')" . "->onUpdate('" . trim($parts[4]) . "')";
            } else {
                continue;
            }

            $schemaFields .= ";\n" . $tabIndent . $tabIndent . $tabIndent;

        }

        $primaryKey = $this->option('pk');

        $schemaUp =
            "Schema::create('" . $tableName . "', function(Blueprint \$table) {
            \$table->increments('" . $primaryKey . "');
            " . $schemaFields . "\$table->timestamps();
        });";

        $schemaDown = "Schema::drop('" . $tableName . "');";

        return $this->replaceSchemaUp($stub, $schemaUp)
            ->replaceSchemaDown($stub, $schemaDown)
            ->replaceClass($stub, $className);
    }

    /**
     * Replace the schema_up for the given stub.
     *
     * @param  string  $stub
     * @param  string  $schemaUp
     *
     * @return $this
     */
    protected function replaceSchemaUp(&$stub, $schemaUp)
    {
        $stub = str_replace(
            '{{schema_up}}', $schemaUp, $stub
        );

        return $this;
    }

    /**
     * Replace the schema_down for the given stub.
     *
     * @param  string  $stub
     * @param  string  $schemaDown
     *
     * @return $this
     */
    protected function replaceSchemaDown(&$stub, $schemaDown)
    {
        $stub = str_replace(
            '{{schema_down}}', $schemaDown, $stub
        );

        return $this;
    }

    protected function arrayToString($explode)
    {
        $string = "[";
        foreach ($explode as $item)
        {
            $string .= "'$item',";
        }
        $string = rtrim($string, ',');

        return $string . ']';
    }
}
