<?php
/**
 * Created by PhpStorm.
 * User: rashidul
 * Date: 4/6/2017
 * Time: 10:30 PM
 */

namespace Rashidul\RainDrops\Form;


use Collective\Html\FormFacade;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\MessageBag;
use Rashidul\RainDrops\Helper;

class Builder
{
    /**
     * @var string
     */
    protected $formType;

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var array
     */
    protected $fieldsOnly = [];

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var array
     */
    protected $fieldsExcept = [];

    /**
     * @var string
     */
    protected $templateName;

    /**
     * @var array
     */
    protected $fieldsModified = [];

    /**
     * @var array
     */
    protected $fieldsAdded = [];

    /**
     * @var string
     */
    protected $wrapperElements;

    protected $errors;

    /**
     * @var boolean
     */
    protected $formOptions = false;

    /**
     * @var int
     */
    protected $columns = 2;

    /**
     * @var array
     */
    protected $fields = [];

    /**
     * @var array
     */
    protected $hiddenFields = [];

    /**
     * @var array
     */
    protected $sections = [];

    /**
     * @var array
     */
    protected $submitButtonOptions = [
        'text' => 'Submit',
        'icon' => 'fa fa-save',
        'class' => 'btn btn-primary',
        'wrapper' => ''
    ];

    /**
     * Builder constructor.
     * @internal param $helper
     */
    public function __construct()
    {
        $this->helper = new Helper();
        $this->errors = $this->getErrorsFromRequest();
    }

    /**
     * Type of the form: create
     *
     * @param $model
     * @return $this
     * @throws Exception
     */
    public function create($model = null)
    {

        if ( !is_null($model) && !$model instanceof Model ){
            throw new \Exception("dafuq bro???");
        }

        $this->formType = 'create';
        $this->model = $model;

        return $this;
    }

    /**
     * Start building your form
     *
     * @param $model
     * @return $this
     * @throws Exception
     */
    public function build($model = null)
    {

        if ( !is_null($model) && !$model instanceof Model ){
            throw new \Exception("dafuq bro???");
        }

        $this->model = $model;

        return $this;
    }

    /**
     * Type of the form: edit
     *
     * @param $model
     * @return $this
     */
    public function edit($model)
    {
        $this->formType = 'edit';
        $this->model = $model;

        return $this;
    }

    public function section($name, $fields)
    {
        $this->sections[$name] = $fields;

        return $this;
    }

    /**
     *
     * Template name, defined in form config's 'templates' key
     *
     * @param $name
     * @return $this
     * @throws Exception
     */
    public function template($name)
    {

        $templateName = 'raindrops.form.templates.' . $name;

        if ( ! Config::has( $templateName ) )
            throw new \Exception('template doesn\'t exists in config file');

        $this->templateName = $templateName;

        return $this;
    }

    /**
     * Validation errors
     *
     * @param $errors
     * @return $this
     */
    public function errors($errors)
    {
        $this->errors = $errors;

        return $this;
    }

    /**
     * Add new fields to the form
     *
     * @param $field
     * @param $options
     * @return $this
     */
    public function add($field, $options)
    {
        $this->fieldsAdded[$field] = $options;

        return $this;
    }

    /**
     * Remove any field from the form
     *
     * @param $fields string | array
     * @return $this
     */
    public function remove($fields)
    {

        if (is_array($fields)) {
            $this->fieldsExcept = array_merge($this->fieldsExcept, $fields);
        } else {
            array_push($this->fieldsExcept, $fields);
        }

        return $this;
    }

    /**
     * Render only the given fields
     *
     * @param array $fields
     * @return $this
     */
    public function only($fields = [])
    {
        $this->fieldsOnly = $fields;

        return $this;
    }

    /**
     * Render all fields except the given ones
     *
     * @param array $fields
     * @return $this
     */
    /*public function except($fields = [])
    {
        $this->fieldsExcept = $fields;

        return $this;
    }*/

    /**
     * Add hidden fields to the form
     * @param $name
     * @param $value
     * @return $this
     */
    public function hidden($name, $value)
    {
        $this->hiddenFields[$name] = $value;

        return $this;
    }

    public function modify($field, $options)
    {
        $this->fieldsModified[$field] = $options;

        return $this;
    }


    /**
     * Determine the form options
     *
     * @return $this
     */
    public function form()
    {
        switch (func_num_args()){

            // if it's a single argument, then its either boolean
            // or an array containing form options ,
            case 1:

                $this->formOptions = func_get_arg(0);

                break;

            // first argument is the action value, and the second one is
            // the method
            case 2:

                $this->formOptions['action'] = func_get_arg(0);
                $this->formOptions['method'] = func_get_arg(1);

                break;

            default:

                $this->formOptions = false;

        }

        return $this;

    }

    /**
     * Any custom classes that should be added to the form element
     *
     * @param $classes
     * @return $this
     */
    public function classes($classes)
    {
        $this->formOptions['class'] = $classes;

        return $this;
    }


    /**
     * Custom id attributes for the form
     *
     * @param $ids string
     * @return $this
     */
    public function ids($ids)
    {
        $this->formOptions['id'] = $ids;

        return $this;
    }

    public function wrapper($elements)
    {
        $this->wrapperElements = $elements;

        return $this;
    }


    public function submit()
    {
        switch (func_num_args()){

            // if it's a single argument, then its either boolean
            // or an array containing form options ,
            case 1:

                $arg = func_get_arg(0);

                if ( is_array($arg) ){
                    $this->submitButtonOptions = array_merge($this->submitButtonOptions, $arg);
                } else {
                    $this->submitButtonOptions = $arg;
                }

                break;

            // first argument is the button text, second is
            // the icon class, third is button class
            case 3:

                $this->submitButtonOptions['text'] = func_get_arg(0);
                $this->submitButtonOptions['icon'] = func_get_arg(1);
                $this->submitButtonOptions['class'] = func_get_arg(2);

                break;

            default:

                $this->submitButtonOptions = false;

        }

        return $this;
    }

    /**
     * number of columns for the form
     * @param $columns
     * @return $this
     */
    public function columns($columns)
    {
        $this->columns = $columns;

        return $this;
    }



    /**
     * Renders the final form markup
     *
     * @return string
     */
    public function render()
    {

        // build the fields array
        $this->fields = $this->populateFieldsArray();

        //$open = FormFacade::open(['url' => 'foo/bar'])->toHtml();

        //$close = FormFacade::close();

        //$test = $this->model->exists;

        //$test = config('raindrops.form.name');

        $template = file_get_contents( config( $this->templateName ) );

        // build the form tag, if its needed
        // build all the fields
        // combine and return the form
        $data = '';


        foreach ($this->fields as $field => $options) {

            // form is false then abort
            if ( array_key_exists('form', $options) && !$options['form'] ){
                continue;
            }

            /*$html = '<div class="col-md-6">
                        <div class="form-group %s">
                            %s
							%s
							%s
					    </div>
					</div>';*/

            // build label
            // set error class
            // build element
            // set error text
            // replace all in templete
            // add it to $data

            //$required = isset($value['required']) && $value['required'] ? 'required' : '';

            $required = $this->isRequired($options);

            $unique = $this->isUnique($options);

            $label = $this->getLabel($options, $required, $unique);

            // if its unique field then add 'Unique' to label
            //$unique = isset($value['unique']) && $value['unique'] ? '(Unique)' : '';

            $error_class = '';

            /*if ( isset($value['required']) && $value['required'] ){
                $label = sprintf('<label class="control-label">%s %s<span class="required-field">*</span></label>', $value['label'], $unique);
            } else {
                $label = sprintf('<label class="control-label">%s %s</label>', $value['label'], $unique);
            }*/

            $element = '';
            $error_text = '';


            if ( $this->errors != null && $this->errors->any() ) {
                if ($this->errors->has($field)) {
                    $error_class = 'has-error';
                    $error_text = $this->errors->first($field);
                }
            }

            switch($options['type'])
            {
                case 'textarea':
                    $element = sprintf('<textarea name="%s" class="form-control"  rows="10" %s></textarea>', $field, $required);
                    break;

                case 'editor':
                    $element = sprintf('<textarea name="%s" class="form-control editor"  rows="10" %s></textarea>', $field, $required);
                    break;

                case 'select':
                    $element = sprintf('<select name="%s" class="form-control select2" %s>', $field, $required);

                    $element .= '<option value="" disabled selected>--Select One--</option>';
                    foreach ($options['options'] as $option_key => $option_value) {
                        $element .= sprintf('<option value="%s">%s</option>', $option_key, $option_value);
                    }
                    $element .= '</select>';
                    break;

                case 'select_db':
                    $table_data = DB::table($options['table'])->select($options['options'])->get();

                    $option_key = $options['options'][0];
                    //$option_value = $value['options'][1];
                    $element = sprintf('<select name="%s" class="form-control select2" %s>', $field, $required);

                    $element .= '<option value="" disabled selected>--Select One--</option>';
                    foreach ($table_data as $table_data_single) {
                        $option_value = count($options['options']) > 2 ? $table_data_single->{$options['options'][1]} . ' ' . $table_data_single->{$options['options'][2]} : $table_data_single->{$options['options'][1]};
                        $element .= sprintf('<option value="%s">%s</option>', $table_data_single->{$option_key}, $option_value);
                    }
                    $element .= '</select>';

                    break;

                case 'date':
                    $element = sprintf('<input type="text" name="%s" class="form-control datepicker" %s>', $field, $required);
                    break;

                case 'date_time':
                    $element = sprintf('<input type="text" name="%s" class="form-control datetimepicker" %s>', $field, $required);
                    break;

                case 'file':
                    $element = sprintf('<input type="%s"  name="%s" class="form-control" accept="%s" %s>', $options['type'], $field, $options['accept'], $required);
                    break;

                // TODO.
                // 1. implement a better timepicker instead of html5 default
                // 2. extract the element generation code to diffeerent methods,
                //    same method for both create & edit form
                // 3. improve image input field
                // 4. improve 2-column horizontal form layout (add row after each 2 columns)

                case 'time':
                    $element = sprintf('<input type="text" name="%s" class="form-control timepicker" %s>', $field, $required);
                    break;

                default:
                    $element = sprintf('<input type="%s" name="%s" class="form-control" %s>', $options['type'], $field, $required);

            }

            $placeholders = [
                '{error_class}' => $error_class,
                '{label_text}' => $label,
                '{element}' => $element,
                '{error_text}' => $error_text

            ];


            //$raw = sprintf($html, $error_class, $label, $element, $help_block);
            $raw = strtr($template, $placeholders);

            $data .= $raw;

        }

        $data .= $this->renderHiddenFields();

        $data .= csrf_field();

        $data .= $this->renderSubmitButton();

        if ( $this->formOptions ){
            $data = $this->wrapWithForm($data);
        }


        return $data;
    }

    private function populateFieldsArray()
    {
        /*$fields = [];*/

        $defaults = $this->model ? $this->model->fields : [];

        // added fields
        $fields = array_merge($defaults, $this->fieldsAdded);

        // removed fields
        if ( !empty($this->fieldsExcept) ){
            $fields = array_diff_key($fields, array_flip($this->fieldsExcept));
        }

        // if $fieldsOnly field is set, keep only those and discard others
        if ( !empty($this->fieldsOnly) ){

            $new_array = [];

            foreach ($this->fieldsOnly as $item) {

                if ( array_key_exists($item, $fields) ){

                    $new_array[$item] = $fields[$item];

                }
            }

            $fields = $new_array;

        }

        // do any modifications needed
        $fields = $this->doModifications($fields);


        return $fields;

    }

    /**
     * Do the required modifications
     *
     * @param $fields
     * @return mixed
     */
    private function doModifications($fields)
    {
        if ( empty($this->fieldsModified) ){
            return $fields;
        }

        foreach ($this->fieldsModified as $field => $options){

            if ( array_key_exists($field, $fields)){

                $new_options = array_replace($fields[$field], $options);

                $fields[$field] = $new_options;
            }
        }

        return $fields;
    }

    /**
     * Render hidden input fields, if any specified
     *
     * @return string
     */
    private function renderHiddenFields()
    {
        $data = '';
        $stub = '<input type="hidden" name="%s" value="%s"/>';

        if ( !empty($this->hiddenFields) ){

            foreach ($this->hiddenFields as $field => $value) {
                $data .= sprintf($stub, $field, $value);
            }
        }

        return $data;
    }

    private function renderSubmitButton()
    {
        $stub = '<button type="submit" class="%s">%s <i class="%s"></i></button>';

        if ( !$this->submitButtonOptions ){
            return '';
        }

        /*if ( is_array($this->submitButtonOptions) ){
            $btn_class = array_key_exists('class') ? $this->submitButtonOptions['class'] : 'btn btn-primary';
        }*/

        // TODO.
        // implement wrapper element
        return sprintf($stub,
            $this->submitButtonOptions['class'],
            $this->submitButtonOptions['text'],
            $this->submitButtonOptions['icon']);


    }

    private function wrapWithForm($data)
    {

        $stub = '<form action="%s" method="%s" enctype="multipart/form-data">%s</form>';

        $action = $this->helper->returnIfExists($this->formOptions, 'action');
        $method = $this->helper->returnIfExists($this->formOptions, 'method');

        return sprintf($stub, $action, $method, $data);
    }

    private function isRequired($options)
    {
        return isset($options['validations']) && str_contains($options['validations'], 'required')
            ? true
            : false;
    }

    private function isUnique($options)
    {
        return isset($options['validations']) && str_contains($options['validations'], 'unique')
            ? true
            : false;
    }

    private function getLabel($options, $required, $unique)
    {
        $required = $required ? ' <span class="required-field">*</span>' : '';

        $unique = $unique ? ' (Unique)' : '';

        return $options['label'] . $unique . $required;
    }

    private function getErrorsFromRequest()
    {

        $request = resolve(Request::class);

        if ( $request->session()->exists('errors') )
        {
            return $request->session()->get('errors')->getBag('default');
        }

        return null;
    }


}