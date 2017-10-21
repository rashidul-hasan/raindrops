<?php
/**
 * Created by PhpStorm.
 * User: rashed
 * Date: 20-Jun-17
 * Time: 4:31 PM
 */

namespace Rashidul\RainDrops\Model;


class ModelHelper
{

    public static function getIndexFields( $model )
    {
        $indexFields = [];

        $fields = $model->getFields();

        foreach ($fields as $field_name => $options){
            if (array_key_exists('index', $options) && $options['index']){
                $indexFields[$field_name] = $options;
            }
        }

        return $indexFields;
    }

    public static function getFormFields( $model )
    {
        $formFields = [];

        $fields = $model->getFields();

        foreach ($fields as $field_name => $options){
            if (array_key_exists('form', $options) && !$options['form']){
                continue;
            }
            $formFields[$field_name] = $options;
        }

        return $formFields;
    }

    public static function getActionLinks( $model, $url = null, $actions = null)
    {
        if (!$url){
            $url = $model->getBaseUrl();
        }

        $permitted_actions = static::getPermittedActions($model, $actions);

        $html = '';

        // generate basic action buttons
        if (in_array('view', $permitted_actions))
        {
            $html .= sprintf('<a href="%s" class="btn btn-xs btn-primary button-show" data-toggle="tooltip" title="View"><i class="fa fa-eye"></i></a>', url($url . '/' . $model->getKey()));
        }

        if (in_array('edit', $permitted_actions))
        {
            $html .= sprintf('<a href="%s" class="btn btn-xs btn-primary button-edit" data-toggle="tooltip" title="Edit"><i class="fa fa-edit"></i></a>', url($url . '/' . $model->getKey() . '/edit'));
        }

        if (in_array('delete', $permitted_actions))
        {
            $html .= sprintf('<a href="%s" class="btn btn-xs btn-danger button-delete" data-method="delete" data-confirm="Are you sure?" data-toggle="tooltip" title="Delete"><i class="fa fa-trash-o"></i></a>', url($url .'/'.$model->getKey()));
        }

        /*$linksTemplate = '<td>
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
                  </td>';*/

        //$singleLinkTemplate = ''

        // first create basic view, edit, delete links
        /*$viewLink = sprintf('<a href="%s" class="btn btn-sm btn-default button-show">View</a>', url($url . '/' . $model->getKey()));
        if ($actions != null && !in_array('view', $actions))
        {
            $viewLink = '';
        }

        $editLink = sprintf('<li><a href="%s" class="button-edit">Edit</a></li>', url($url . '/' . $model->getKey() . '/edit'));
        if ($actions != null && !in_array('edit', $actions))
        {
            $editLink = '';
        }

        $deleteLink = sprintf('<li><a href="%s" class="button-delete" data-method="delete" data-confirm="Are you sure?">Delete</a></li>', url($url .'/'.$model->getKey()));
        if ($actions != null && !in_array('delete', $actions))
        {
            $deleteLink = '';
        }*/

        // if there's extra links defined in the model
        /*$extraLinks = '';

        // check if there's any extra action links defined in the model
        if (property_exists($model, 'actions')){
            $extraActions = $model->actions;
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
                /*$linkUrl = '';
                $linkClass = '';

                // if its an array
                if (is_array($linkParams)){

                    // check length of array
                    switch(count($linkParams)){

                        case 2:
                            $linkUrl = static::getReplacedUrl($linkParams[0], $url, $model);
                            $linkClass = $linkParams[1];
                            break;

                        default:
                            continue;
                    }

                } else{
                    //else its just a string, link's url
                    $linkUrl = static::getReplacedUrl($linkParams, $url, $model);
                }
                $extraLinks .= sprintf($extraLinkTemplate, url($linkUrl), $linkClass, $linkText);
            }
        }

        return sprintf($linksTemplate, $viewLink, $editLink, $deleteLink, $extraLinks);*/

        return $html;
    }

    private static function getReplacedUrl($linkUrl, $url, $model)
    {
        $search = [
            '{url}',
            '{id}'
        ];

        $replace = [
            $url,
            $model->id
        ];

        return str_replace($search, $replace, $linkUrl);
    }

    public static function getFileFields( $model )
    {
        $fields = [];
        $fileTypes = ['file', 'image'];

        foreach ($model->getFields() as $field_name => $options){
            if (array_key_exists('type', $options) && in_array($options['type'], $fileTypes)){
                $fields[$field_name] = $options;
            }
        }

        return $fields;
    }

    public static function getCheckBoxFields($model)
    {
        $fields = [];
        foreach ($model->getFields() as $field_name => $options){
            if (array_key_exists('type', $options) && $options['type'] === 'checkbox'){
                $fields[$field_name] = $options;
            }
        }

        return $fields;
    }

    public static function fillWithRequestData($model, $request)
    {

        $fields = $model->getFormFields();


        foreach ($fields as $field => $options)
        {
            // skip fields who's `form` option is set to `method`
            if (array_key_exists('form', $options) && $options['form'] == 'method') continue;

            // save the data according to the field type
            switch ($options['type'])
            {
                case 'checkbox':

                    if ($request->has($field) && $request->get($field) === 'on')
                    {
                        $model->{$field} = 1;
                    }
                    else
                    {
                        $model->{$field} = 0;
                    }

                    break;

                case 'file':
                case 'image':

                    if ($request->hasFile($field))
                    {
                        $model->{$field} = static::handleFileUpload($request, $field, $options);
                    }
                    break;

                default:
                    $model->{$field} = $request->has($field) ? $request->get($field) : null;
            }
        }

        return $model;
    }

    private static function getPermittedActions($model, $actions)
    {
        if ($actions != null)
        {
            return $actions;
        }
        return ['view', 'edit', 'delete'];
    }

    private static function handleFileUpload($request, $field, $options)
    {
        $disk = isset($options['disk'])
            ? $options['disk']
            : config('raindrops.crud.disk');

        $path = isset($options['path']) ? $options['path'] : $field;

        $request->file($field)->store($path, $disk);

        return $request->file($field)->hashName();

    }


}