<?php
/**
 * Created by PhpStorm.
 * User: rashed
 * Date: 21-May-17
 * Time: 11:45 AM
 */

namespace Rashidul\RainDrops\Model;


trait RainDropsSupport
{

    public function getBaseUrl($isUrl = true)
    {
        // if $isUrl is false then just return the $baseUrl field
        // by default, return the complete url (wrapped in url function)
        if (property_exists($this, 'baseUrl') )
        {
            return ($isUrl) ? url($this->baseUrl) : $this->baseUrl;
        }
        return null;
    }

    /**
     * Generate show details url for this model
     * @param $baseUrl
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function getShowUrl($baseUrl = null)
    {
        $baseUrl = !$baseUrl ? $this->getBaseUrl() : $baseUrl;

        return url($baseUrl . '/' . $this->getKey());
    }

    /**
     * edit url
     * @param $baseUrl
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function getEditUrl($baseUrl = null)
    {
        $baseUrl = !$baseUrl ? $this->getBaseUrl() : $baseUrl;

        return url($baseUrl . '/' . $this->getKey() . '/edit');
    }

    /**
     * Get the form to Create a new item url
     * @param $baseUrl
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function getCreateUrl($baseUrl = null)
    {
        $baseUrl = !$baseUrl ? $this->getBaseUrl() : $baseUrl;

        return url($baseUrl . '/create');
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function getCheckBoxFields()
    {
        return ModelHelper::getCheckBoxFields($this);
    }

    public function getDataUrl($baseUrl = null)
    {
        $baseUrl = !$baseUrl ? $this->getBaseUrl() : $baseUrl;

        return url($baseUrl . '/data');
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
                    $replacer = $item ? ',' . $item->getKey() : '';
                    $rule = str_replace('{id}', $replacer, $rule);

                }
                $rules[$field_name] = $rule;


            }
        }

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

    /**
     * Returns all file type fields
     * @return array
     */
    public function getFileFields()
    {
        return ModelHelper::getFileFields($this);
    }

    public function getActionLinks($url = null)
    {
        return ModelHelper::getActionLinks($this, $url);
    }

    /**
     * Fill up the model with data from request object
     * @param $request
     */
    public function fillWithRequestData($request)
    {
        return ModelHelper::fillWithRequestData($this, $request);
    }





}