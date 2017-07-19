<?php

namespace Rashidul\RainDrops\Facades;

use Illuminate\Support\Facades\Facade;

class JavaScript extends Facade
{
    /**
     * The name of the binding in the IoC container.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'JavaScript';
    }
}