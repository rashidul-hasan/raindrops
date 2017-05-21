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
        $fields = $this->fields;

        $indexFields = [];

        foreach ($fields as $field_name => $options){
            if (array_key_exists('index', $options) && $options['index']){
                //array_push($indexFields, $field);
                $indexFields[$field_name] = $options;
            }
        }

        return $indexFields;
    }

    public function getActionLinks($url = null)
    {
        if (!$url){
            $url = $this->getBaseUrl();
        }

        $linksTemplate = '<td>
                      <div class="btn-group">
                           %s
                           <a class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                           <span class="caret"></span>
                           <span class="sr-only">Toggle Dropdown</span>
                           </a>
                           <ul class="dropdown-menu" role="menu">
                                %s
                                %s
                                %s
                           </ul>
                      </div>
                  </td>';

        //$singleLinkTemplate = ''

        // first create basic view, edit, delete links
        $viewLink = sprintf('<a href="%s" class="btn btn-sm btn-default button-show">View</a>', url($url . '/' . $this->id));
        $editLink = sprintf('<li><a href="%s" class="button-edit">Edit</a></li>', url($url . '/' . $this->id . '/edit'));
        $deleteLink = sprintf('<li><a href="%s" class="button-delete">Delete</a></li>', url($url .'/'.$this->id));

        // if there's extra links defined in the model
        $extraLinks = '';

        // check if there's any extra action links defined in the model
        if (property_exists($this, 'actions')){
            $extraActions = $this->actions;
            $extraLinks .= '<li class="divider"></li>';
            $extraLinkTemplate = '<li><a href="%s" class="%s">%s</a></li>';

            foreach ($extraActions as $linkText => $linkParams){

                /*
                 * $linkParams will contain information about the link
                 * if its a string then its just the link url
                 * it can be an array of variable size, in array
                 * the first item is always the url
                 * 2nd: anchor tag's class
                 * 3rd: link target
                 */
                $linkUrl = '';
                $linkClass = '';

                // if its an array
                if (is_array($linkParams)){

                    // check length of array
                    switch(count($linkParams)){

                        case 2:
                            $linkUrl = $this->getReplacedUrl($linkParams[0], $url);
                            $linkClass = $linkParams[1];
                            break;

                        default:
                            continue;
                    }

                } else{
                    //else its just a string, link's url
                    $linkUrl = $this->getReplacedUrl($linkParams, $url);
                }
                $extraLinks .= sprintf($extraLinkTemplate, url($linkUrl), $linkClass, $linkText);
            }
        }

        return sprintf($linksTemplate, $viewLink, $editLink, $deleteLink, $extraLinks);

    }

    private function getReplacedUrl($linkUrl, $url)
    {
        $search = [
            '{url}',
            '{id}'
        ];

        $replace = [
            $url,
            $this->id
        ];

        return str_replace($search, $replace, $linkUrl);
    }



}