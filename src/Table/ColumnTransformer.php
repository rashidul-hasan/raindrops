<?php
/**
 * Created by PhpStorm.
 * User: rashed
 * Date: 03-Jul-17
 * Time: 3:01 PM
 */

namespace Rashidul\RainDrops\Table;


class ColumnTransformer
{

    /**
     * ColumnTransformer constructor.
     */
    public function __construct()
    {
    }

    public function string($model, $field)
    {
        $row_data = '';
        if ($model->{$field}){
            $row_data = ucwords( $model->{$field} );
        }
        return $row_data;
    }

    public function detailsLink($model, $field, $value)
    {
        $row_data = '';
        if ($model->{$field}){
            $field_value = $model->{$field};
            $row_data = "<a href='{$model->getShowUrl()}'>{$field_value}</a>";
        }
        return $row_data;
    }

    public function exact($model, $field, $value)
    {
        if ($model->{$field}){
            return $model->{$field};
        }
        return '';
    }

    public function enum($model, $field, $value)
    {
        $enumOptionsArray = $value['options'];
        if ($model->{$field}){
            $option = $model->getOriginal($field);
            return $enumOptionsArray[$option];
        }

        return '';
    }

    public function url($model, $field, $value)
    {
        if ($model->{$field}){
            $field_value = $model->{$field};
            return "<a href='{$field_value}' target='_blank'>{$field_value}</a>";
        }

        return '';
    }

    public function phoneNumber($model, $field, $value)
    {
        if ($model->{$field}){
            $field_value = $model->{$field};
            return "<a href='tel://{$field_value}'>{$field_value}</a>";
        }

        return '';
    }

    public function email($model, $field, $value)
    {
        if ($model->{$field}){
            $field_value = $model->{$field};
            return "<a href='mailto:{$field_value}'>{$field_value}</a>";
        }

        return '';
    }

    public function image($model, $field, $value)
    {
        $path = $model->paths["$field"];
        if ($model->{$field}){
            $filename = $model->{$field};
            $url = url($path . '/' . $filename);
            return sprintf('<img class="img-thumb img-responsive ui small rounded image" src="%s" alt="%s">', $url, $value['label']);
        }

        return '';
    }
}