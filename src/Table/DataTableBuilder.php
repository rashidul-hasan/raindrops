<?php
/**
 * Created by PhpStorm.
 * User: rashed
 * Date: 19-May-17
 * Time: 5:38 PM
 */

namespace Rashidul\RainDrops\Table;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Config;
use Rashidul\RainDrops\Html\Element;


class DataTableBuilder
{


    /**
     * @var
     */
    protected $model;

    /**
     * Holds the index fields for the model
     *
     * @var array
     */
    protected $indexFields = [];

    /**
     * @var array
     */
    private $config = array();

    /**
     * @var array
     */
    private $columns = array();

    /**
     * @var array
     */
    private $options = array();

    /**
     * @var array
     */
    private $callbacks = array();

    /**
     * Values to be sent to custom templates
     *
     * @var array
     */
    private $customValues = array();

    /**
     * @var array
     */
    private $data = array();

    /**
     * @var boolean Determines if the template should echo the javascript
     */
    private $noScript = false;

    /**
     * @var String The name of the id the table will have later
     */
    protected $idName;

    /**
     * @var String The name of the class the table will have later
     */
    protected $className;

    /**
     * @var String The view used to render the table
     */
    protected $table_view;

    /**
     * @var String The view used to render the javascript
     */
    protected $script_view;

    /**
     * @var boolean indicates if the mapping was already added to the options
     */
    private $createdMapping = true;

    /**
     * @var array name of mapped columns
     */
    private $aliasColumns = array();

    function __construct()
    {
        $this->config = Config::get('raindrops.table.index');

        $this->setId( $this->config['id'] );
        $this->setClass( $this->config['class'] );
        $this->setOptions( $this->config['options'] );
        $this->setCallbacks( $this->config['callbacks'] );

        $this->noScript = $this->config['noScript'];
        $this->table_view = $this->config['table_view'];
        $this->script_view = $this->config['script_view'];
    }

    public function of($model = null)
    {
        if ( !is_null($model) && !$model instanceof Model )
        {
            throw new \InvalidArgumentException("Argument 1 of build method must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        $this->model = $model;

        $this->indexFields = $this->getIndexFields();

        return $this;
    }


    /**
     * @return $this
     */
    public function addColumn()
    {
        foreach (func_get_args() as $title)
        {
            if(is_array($title))
            {
                foreach ($title as $mapping => $arrayTitle)
                {
                    $this->columns[] = $arrayTitle;
                    $this->aliasColumns[] = $mapping;
                    if(is_string($mapping))
                    {
                        $this->createdMapping = false;
                    }
                }
            }
            else
            {
                $this->columns[] = $title;
                $this->aliasColumns[] = count($this->aliasColumns)+1;
            }
        }
        return $this;
    }

    /**
     * @return int
     */
    public function countColumns()
    {
        return count($this->columns);
    }

    /**
     * @return $this
     */
    public function removeOption($key)
    {
        if(isset($this->options[$key])) unset($this->options[$key]);
        return $this;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function setOptions()
    {
        if(func_num_args() == 2)
        {
            $this->options[func_get_arg(0)] =func_get_arg(1);
        }
        else if(func_num_args() == 1 && is_array(func_get_arg(0)))
        {
            foreach (func_get_arg(0) as $key => $option)
            {
                $this->options[$key] = $option;
            }
        }
        else
            throw new Exception('Invalid number of options provided for the method "setOptions"');
        return $this;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function setOrder($order = array())
    {
        $_orders = array();
        foreach ($order as $number => $sort)
        {
            $_orders[] .= '[ ' . $number . ', "' . $sort . '" ]';
        }

        $_build = '[' . implode(', ', $_orders) . ']';

        $this->callbacks['aaSorting'] = $_build;
        return $this;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function setCallbacks()
    {
        if(func_num_args() == 2)
        {
            $this->callbacks[func_get_arg(0)] = func_get_arg(1);
        }
        else if(func_num_args() == 1 && is_array(func_get_arg(0)))
        {
            foreach (func_get_arg(0) as $key => $value)
            {
                $this->callbacks[$key] = $value;
            }
        }
        else
            throw new Exception('Invalid number of callbacks provided for the method "setCallbacks"');

        return $this;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function setCustomValues()
    {
        if(func_num_args() == 2)
        {
            $this->customValues[func_get_arg(0)] = func_get_arg(1);
        }
        else if(func_num_args() == 1 && is_array(func_get_arg(0)))
        {
            foreach (func_get_arg(0) as $key => $value)
            {
                $this->customValues[$key] = $value;
            }
        }
        else
            throw new Exception('Invalid number of custom values provided for the method "setCustomValues"');

        return $this;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @param $url
     * @return $this
     */
    public function setUrl($url)
    {
        $this->options['ajax'] = $url;
        $this->options['serverSide'] = true;
        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return array
     */
    public function getCallbacks()
    {
        return $this->callbacks;
    }

    /**
     * @return array
     */
    public function getCustomValues()
    {
        return $this->customValues;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param null $view
     * @return mixed
     */
    public function render($view = null)
    {
        if( ! is_null($view))
            $this->table_view = $view;

        if(!isset($this->options['ajax']))
        {
            $this->setUrl(Request::url());
        }

        // prepare the title row
        $headers = $this->prepareHeaderRow();

        // prepare the columns
        $this->options['columns'] = $this->prepareColumns();


        // create mapping for frontend
        if(!$this->createdMapping)
        {
            $this->createMapping();
        }

        return View::make($this->table_view,array(
            'options'   => $this->options,
            'callbacks' => $this->callbacks,
            'headers'   => $headers,
            'values'    => $this->customValues,
            'data'      => $this->data,
            'columns'   => array_combine($this->aliasColumns,$this->columns),
            'noScript'  => $this->noScript,
            'id'        => $this->idName,
            'class'     => $this->className,
        ));

    }

    /**
     * Instructs the table not to echo the javascript
     *
     * @return $this
     */
    public function noScript()
    {
        $this->noScript = true;
        return $this;
    }

    private function convertData($options) {
        $is_obj = false;
        $first = true;
        $data = "";
        foreach ($options as $k => $o) {
            if ($first == true) {
                if (!is_numeric($k)) {
                    $is_obj = true;
                }
                $first = false;
            } else {
                $data .= ",\n";
            }
            if (!is_numeric($k)) {
                $data .= json_encode($k) . ":";
            }
            if (is_string($o)) {
                if (@preg_match("#^\s*function\s*\([^\)]*#", $o)) {
                    $data .= $o;
                } else {
                    $data .= json_encode($o);
                }
            } else {
                if (is_array($o)) {
                    $data .= $this->convertData($o);
                } else {
                    $data .= json_encode($o);
                }
            }
        }

        if ($is_obj) {
            $data = "{ $data }";
        } else {
            $data = "[ $data ]";
        }

        return $data;
    }

    public function script($view = null)
    {
        if( ! is_null($view))
            $this->script_view = $view;

        // create mapping for frontend
        if(!$this->createdMapping)
        {
            $this->createMapping();
        }

        return View::make($this->script_view,array(
            'options' => $this->convertData(array_merge($this->options, $this->callbacks)),
            'id'        =>  $this->idName,
        ));
    }

    public function getId()
    {
        return $this->idName;
    }

    public function setId($id = '')
    {
        $this->idName = empty($id)? str_random(8) : $id;
        return $this;
    }

    public function getClass()
    {
        return $this->className;
    }

    public function setClass($class)
    {
        $this->className = $class;
        return $this;
    }


    public function addAfter($after, $fieldName, $label)
    {
        // if the after key doesn't exists, just add it at the end
        if (!array_key_exists($after, $this->indexFields))
        {
            $this->indexFields[$fieldName] = ['label' => $label];

            return $this;
        }
        $this->indexFields = \Rashidul\RainDrops\Helper::array_insert_after($after, $this->indexFields, $fieldName, ['label' => $label]);

        return $this;
    }

    public function setAliasMapping($value)
    {
        $this->createdMapping = !$value;
        return $this;
    }

    //--------------------PRIVATE FUNCTIONS

    private function createMapping()
    {
        // set options for better handling
        // merge with existing options
        if(!array_key_exists('aoColumns', $this->options))
        {
            $this->options['aoColumns'] = array();
        }
        $matching = array();
        $i = 0;
        foreach($this->aliasColumns as $name)
        {
            if(array_key_exists($i,$this->options['aoColumns']))
            {
                $this->options['aoColumns'][$i] = array_merge_recursive($this->options['aoColumns'][$i],array('mData' => $name));
            }
            else
            {
                $this->options['aoColumns'][$i] = array('mData' => $name);
            }
            $i++;
        }
        $this->createdMapping = true;
        //dd($matching);
        return $matching;
    }

    private function getIndexFields()
    {
        $indexFields = [];

        foreach ($this->model->getFields() as $field_name => $options){
            if (array_key_exists('index', $options) && $options['index']){
                $indexFields[$field_name] = $options;
            }
        }

        return $indexFields;
    }

    private function prepareHeaderRow()
    {
        $tr = Element::build('tr');

        // extra empty th for checkbox
        //$html .= '<th></th>';

        // build $trTitle, containing title
        foreach ($this->indexFields as $field){
            $th = Element::build('th')
                        ->text($field['label']);

            $tr->addChild($th);
        }
        // extra empty th for action column
        $tr->addChild(Element::build('th')->text('Action'));

        return $tr->render();
    }

    private function prepareColumns()
    {

        $columnsArray = [];

        /*$column_select['data'] = 'select';
        $column_select['name'] = 'select';
        $column_select['orderable'] = false;
        $column_select['searchable'] = false;
        //$column_select['className'] = 'datatable-select';
        array_push($columnsArray, $column_select);*/


        foreach ($this->indexFields as $fieldName => $options){
            $dataType = $this->getDataType($options);

            if ($dataType == 'relation'){
                $relationOptions = isset($options['options']) ? $options['options'] : $options['show'];
                $column['data'] = $fieldName . '.' . $relationOptions[1];
                $column['name'] = $fieldName . '.' . $relationOptions[1];
                $column['defaultContent'] = '';
                array_push($columnsArray, $column);
            } else {
                $column['data'] = $fieldName;
                $column['name'] = $fieldName;
                $column['defaultContent'] = '';
                array_push($columnsArray, $column);
            }
        }

        // add column for actions
        $column_action['data'] = 'action';
        $column_action['name'] = 'action';
        $column_action['orderable'] = false;
        $column_action['searchable'] = false;
        $column_action['className'] = 'datatable-action';
        array_push($columnsArray, $column_action);

        // finally return the array
        return $columnsArray;
    }

    /**
     * Get the type of data this row contains
     * @param $value
     * @return int|stringC
     */
    private function getDataType($value)
    {
        $type = 'string';

        if ( isset($value['show'] ) )
        {
            if (is_array($value['show'])){
                $type = count($value['show']) === 3 && $value['show'][2] === true ? 'relation-details' : 'relation';
            } else {
                $type = $value['show'];
            }

        } else {
            $type = $this->defaultDataTypeForField($value);
        }

        return $type;

    }

    /**
     * Predict default table row type if there's no `show`
     * attribute specified explicitly
     * @param $value
     * @return string
     */
    private function defaultDataTypeForField($value)
    {
        if (isset($value['type'])){

            switch($value['type']){
                case 'select' :
                    return 'enum';
                    break;

                case 'relation' :
                    return 'relation';
                    break;

                default:
                    return 'exact';
            }
        }

        return 'string';
    }



}