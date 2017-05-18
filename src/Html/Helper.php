<?php
/**
 * Created by PhpStorm.
 * User: rashed
 * Date: 16-May-17
 * Time: 11:13 AM
 */

namespace Rashidul\RainDrops\Html;


class Helper
{

    public function wrapWith($element, $content)
    {

    }

    /**
     * Generate an element from a string
     * @param $syntax
     */
    public function elementFromSyntax($syntax)
    {
        if ( !strlen($syntax) ) return;

        // split the string by period. first item is the element tag
        // the followings are the class names
        $arr = explode('.', $syntax);
        $tag = $arr[0];
        array_shift($arr);

        return Element::build($tag)
                    ->addClass( implode(" ", $arr) );

    }
}