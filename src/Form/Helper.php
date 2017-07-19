<?php
/**
 * Created by PhpStorm.
 * User: rashed
 * Date: 19-Jul-17
 * Time: 12:05 PM
 */

namespace Rashidul\RainDrops\Form;

class Helper
{


    /**
     * build html <option> tags from a given collection of models
     * @param $collection
     * @param array $indices
     * @return string
     */
    public static function collectionToOptions($collection, $indices = [], $selected = null)
    {
        $values = $collection->toArray();

        $option_key = $indices[0];
        $options = '';
        foreach ($values as $value) {
            $isSelected = $value['id'] === $selected ? 'selected' : '';
            $option_value = count($indices) > 2 ? $value[$indices[1]] . ' ' . $value[$indices[2]] : $value[$indices[1]];
            $options .= sprintf('<option value="%s" %s>%s</option>', $value[$option_key], $isSelected, $option_value);
        }

        return $options;
    }

}