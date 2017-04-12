<?php
/**
 * Created by PhpStorm.
 * User: rashidul
 * Date: 4/6/2017
 * Time: 10:38 PM
 */

namespace Rashidul\RainDrops\Facades;


use Illuminate\Support\Facades\Facade;

class FormBuilder extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'formbuilder';
    }
}