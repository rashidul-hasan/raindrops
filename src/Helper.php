<?php
/**
 * Created by PhpStorm.
 * User: rashed
 * Date: 16-May-17
 * Time: 12:01 PM
 */

namespace Rashidul\RainDrops;


class Helper
{

    public function returnIfExists($array, $key)
    {
        return array_key_exists($key, $array) ? $array[$key] : '';
    }
}