<?php
/**
 * Created by PhpStorm.
 * User: rashed
 * Date: 21-May-17
 * Time: 11:45 AM
 */

namespace Rashidul\RainDrops\Model;


trait EloquentHelperTrait
{

    /**
     * Generate show details url for this model
     * @param $baseUrl
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function getShowUrl($baseUrl = null)
    {
        $baseUrl = !$baseUrl ? $this->getBaseUrl() : $baseUrl;

        return url($baseUrl . '/' . $this->id);
    }

    /**
     * edit url
     * @param $baseUrl
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function getEditUrl($baseUrl)
    {
        return url($baseUrl . '/' . $this->id . '/edit');
    }

    /**
     * Get the form to Create a new item url
     * @param $baseUrl
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function getCreateUrl($baseUrl)
    {
        return url($baseUrl . '/create');
    }

    /**
     * Returned the validation rules
     * @param null $item
     * @return array
     * @internal param null $request
     */
    public function getValidationRules($item = null)
    {

        $fields = $this->fields;

        $rules = [];

        foreach ($fields as $field_name => $options){
            if (array_key_exists('validations', $options) && $options['validations'] != ''){

                $rule = $options['validations'];
                if ( str_contains($options['validations'], '{id}') ){
                    $replacer = $item ? ',' . $item->id : '';
                    $rule = str_replace('{id}', $replacer, $rule);

                }
                $rules[$field_name] = $rule;


            }
        }

        // check the request method, if PUT or PATCH
        // update the unique validation rule
//        if( $request && $item){
//
//            if ($request->method() === 'PUT' || $request->method() === 'PATCH'){
//                foreach()
//            }
//        }

        return $rules;
    }

    /**
     * Returned the label of the provided field
     */
    public function getLabel($field_name)
    {

        $fields = $this->fields;

        return $fields[$field_name]['label'];
    }

    public function getEntityName()
    {
        return $this->entityName;
    }

    public function getEntityNamePlural()
    {
        return property_exists($this, 'entityNamePlural')
            ? $this->entityNamePlural
            : str_plural($this->getEntityName());
    }

    public function getBaseUrl()
    {
        return property_exists($this, 'baseUrl') ? $this->baseUrl : null;
    }

    public function getRelations()
    {
        return property_exists($this, 'related') ? $this->related : null;
    }


    /**
     * Return field names with their label
     * to be used on validation error messages
     */
    public function getFieldsWithLabels()
    {

        $fields = $this->fields;

        $data = [];

        foreach ($fields as $field => $attributes) {
            $data[$field] = $attributes['label'];
        }

        return $data;
    }

    public function getIndexFields()
    {
        return ModelHelper::getIndexFields($this->fields);
    }

    public function getActionLinks($url = null)
    {
        return ModelHelper::getActionLinks($this, $url);
    }





}