<?php
/**
 * Created by PhpStorm.
 * User: rashidul
 * Date: 4/6/2017
 * Time: 10:30 PM
 */

namespace Rashidul\RainDrops\Form;


class Builder
{
    protected $formType;
    protected $modelClass;
    protected $fieldsOnly;
    protected $fieldsExcept;
    protected $errors;
    protected $columns = 2;
    protected $fields = [];

    public function create($modelClass)
    {
        $this->formType = 'create';
        $this->modelClass = $modelClass;

        return $this;
    }

    public function edit($modelClass)
    {
        $this->formType = 'edit';
        $this->modelClass = $modelClass;

        return $this;
    }

    public function errors($errors)
    {
        $this->errors = $errors;

        return $this;
    }

    public function columns($columns)
    {
        $this->columns = $columns;

        return $this;
    }

    public function render()
    {
        $data = '';

        $this->fields = $this->modelClass->fields;

        foreach ($this->fields as $key => $value) {

            // form is false then abort
            if (array_key_exists('form', $value) && !$value['form']){
                continue;
            }

            $html = '<div class="col-md-6">
                        <div class="form-group %s">
                            %s
							%s
							%s
					    </div>
					</div>';

            $required = $value['required'] ? 'required' : '';

            // if its unique field then add 'Unique' to label
            $unique = isset($value['unique']) && $value['unique'] ? '(Unique)' : '';

            $error_class = '';
            if ($value['required'] ){
                $label = sprintf('<label class="control-label">%s %s<span class="required-field">*</span></label>', $value['label'], $unique);
            } else {
                $label = sprintf('<label class="control-label">%s %s</label>', $value['label'], $unique);
            }

            $element = '';
            $help_block = '';


            if ( $this->errors != null && $this->errors->any() ) {
                if ($this->errors->has($key)) {
                    $error_class = 'has-error';
                    $help_block = $this->errors->first($key, '<p class="help-block">:message</p>');
                }
            }

            switch($value['type'])
            {
                case 'textarea':
                    $element = sprintf('<textarea name="%s" class="form-control"  rows="10" %s></textarea>', $key, $required);
                    break;

                case 'editor':
                    $element = sprintf('<textarea name="%s" class="form-control editor"  rows="10" %s></textarea>', $key, $required);
                    break;

                case 'select':
                    $element = sprintf('<select name="%s" class="form-control select2" %s>', $key, $required);

                    $element .= '<option value="" disabled selected>--Select One--</option>';
                    foreach ($value['options'] as $option_key => $option_value) {
                        $element .= sprintf('<option value="%s">%s</option>', $option_key, $option_value);
                    }
                    $element .= '</select>';
                    break;

                case 'select_db':
                    $table_data = DB::table($value['table'])->select($value['options'])->get();

                    $option_key = $value['options'][0];
                    //$option_value = $value['options'][1];
                    $element = sprintf('<select name="%s" class="form-control select2" %s>', $key, $required);

                    $element .= '<option value="" disabled selected>--Select One--</option>';
                    foreach ($table_data as $table_data_single) {
                        $option_value = count($value['options']) > 2 ? $table_data_single->{$value['options'][1]} . ' ' . $table_data_single->{$value['options'][2]} : $table_data_single->{$value['options'][1]};
                        $element .= sprintf('<option value="%s">%s</option>', $table_data_single->{$option_key}, $option_value);
                    }
                    $element .= '</select>';

                    break;

                case 'date':
                    $element = sprintf('<input type="text" name="%s" class="form-control datepicker" %s>', $key, $required);
                    break;

                case 'date_time':
                    $element = sprintf('<input type="text" name="%s" class="form-control datetimepicker" %s>', $key, $required);
                    break;

                case 'file':
                    $element = sprintf('<input type="%s"  name="%s" class="form-control" accept="%s" %s>', $value['type'], $key, $value['accept'], $required);
                    break;

                // TODO.
                // 1. implement a better timepicker instead of html5 default
                // 2. extract the element generation code to diffeerent methods,
                //    same method for both create & edit form
                // 3. improve image input field
                // 4. improve 2-column horizontal form layout (add row after each 2 columns)

                case 'time':
                    $element = sprintf('<input type="text" name="%s" class="form-control timepicker" %s>', $key, $required);
                    break;

                default:
                    $element = sprintf('<input type="%s" name="%s" class="form-control" %s>', $value['type'], $key, $required);

            }


            $raw = sprintf($html, $error_class, $label, $element, $help_block);

            $data .= $raw;

        }

        return $data;
    }
}