<?php
/**
 * Created by PhpStorm.
 * User: rashed
 * Date: 19-May-17
 * Time: 5:38 PM
 */

namespace Rashidul\RainDrops\Facades;


use Illuminate\Support\Facades\Facade;

class DataTable extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'datatable-builder';
    }
}