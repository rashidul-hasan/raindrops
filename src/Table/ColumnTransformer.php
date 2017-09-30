<?php
/**
 * Created by PhpStorm.
 * User: rashed
 * Date: 03-Jul-17
 * Time: 3:01 PM
 */

namespace Rashidul\RainDrops\Table;


use Illuminate\Database\Eloquent\Model;

class ColumnTransformer
{

    protected $helper;

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

            $html = $enumOptionsArray[$option];
            if (isset($value['labels']))
            {
                $configLabels = config('raindrops.crud.labels');
                $labelName = $value['labels'][$model->{$field}];
                $labelHtml = (new \Rashidul\RainDrops\Html\Helper())->elementFromSyntax($configLabels[$labelName]);
                $html = $labelHtml->text($html)->render();
            }

            return $html;
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
        $path = isset($value['path']) ? $value['path'] : '';
        $root = config('raindrops.crud.filesystem_root');

        if ($model->{$field}){
            $filename = $model->{$field};
            $url = url( $root . '/' . $path .  '/' . $filename);
            return sprintf('<img class="img-thumb img-responsive ui small rounded image" src="%s" alt="%s">', $url, $value['label']);
        }

        return '';
    }

    public function checkbox($model, $field, $value)
    {
        $pos = 'Yes';
        $neg = 'No';
        if (isset($value['options']))
        {
            $pos = $value['options'][0];
            $neg = $value['options'][1];
        }

        $html = $model->{$field} ? $pos : $neg;

        if (isset($value['labels']))
        {
            $configLabels = config('raindrops.crud.labels');
            $labelName = $value['labels'][$model->{$field}];
            $labelHtml = (new \Rashidul\RainDrops\Html\Helper())->elementFromSyntax($configLabels[$labelName]);
            $html = $labelHtml->text($html)->render();
        }

        return $html;
    }

    public function relation($model, $field, $value, $helper)
    {

        $html = '';
        $relatedModel = null;
        $columnName = '';

        /*if ( !$model->{$field} )
        {
            return $html;
        }*/

        if (isset($value['options']))
        {
            $relatedModel = $model->{$value['options'][0]};
            $columnName = $value['options'][1];
        }

        if (isset($value['show']))
        {
            $relatedModel = $model->{$value['show'][0]};
            $columnName = $value['show'][1];
        }

        if ( $relatedModel == null || !($relatedModel instanceof Model))
        {
            return $html;
        }

        // we first check if there's a fields array defined in this related model, if it is
        // then we show it according to the configuration of that fields array,
        if (method_exists($relatedModel, 'getFields') && $relatedModel->getFields() != null)
        {
            $relatedModelFields = $relatedModel->getFields();
            $type = $helper->getDataType($relatedModelFields[$columnName]);
            $html = $helper->get($relatedModel, $columnName, $relatedModelFields[$columnName], $type);
        }
        else
        {
            // otherwise just return the field's value directly
            $html = $relatedModel->{$columnName};
        }

        if (isset($value['linkable']) && $value['linkable'])
        {
            $html = sprintf('<a href="%s">%s</a>', $relatedModel->getShowUrl(), $html);
        }

        return $html;

        /*if ($model->{$field})
        {
            $relatedModel = $model->{$value['options'][0]};
            // TODO.
            // 1. check if returned related model is actually a subclass of eloquent
            // 2. handle relationship more than 2 levels
            if ($relatedModel)
            {

                return $relatedModel->{$value['options'][1]};
                /*array_shift($showArray); // remove the first element of the array
                foreach ($showArray as $item) {
                    $row_data .= $relatedModel->{$item} . ' ';
                }*//*

            }

        }
        elseif (isset($value['show']))
        {
            $relatedModel = $model->{$value['show'][0]};

            if ($relatedModel && $relatedModel instanceof Model)
            {
                // linkable
                if (isset($value['linkable']) && $value['linkable'])
                {
                    return sprintf('<a href="%s">%s</a>', $relatedModel->getShowUrl(), $relatedModel->{$value['show'][1]});
                }
                // we first check if there's a fields array defined in this related model, if it is
                // then we show it according to the configuration of that fields array,
                if (method_exists($relatedModel, 'getFields') && $relatedModel->getFields() != null)
                {
                    $relatedModelFields = $relatedModel->getFields();
                    return $this->helper->get($relatedModel, $value['show'][1], $relatedModelFields[$value['show'][1]]);
                }
                else
                {
                    // otherwise just return the field's value directly
                    return $relatedModel->{$value['show'][1]};
                }

            }
        }
        else
        {
            return '';
        }

        return '';*/

    }

    public function currency($model, $field, $value)
    {
        if ($model->{$field}){
            return $model->{$field};
        }
        return '';
    }

}