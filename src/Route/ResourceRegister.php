<?php
/**
 * Created by PhpStorm.
 * User: rashed
 * Date: 07-Jul-17
 * Time: 3:45 PM
 */

namespace Rashidul\RainDrops\Route;


use Illuminate\Routing\ResourceRegistrar as OriginalRegistrar;

class ResourceRegister extends OriginalRegistrar
{

    /**
     * The default actions for a resourceful controller.
     *
     * @var array
     */
    protected $resourceDefaults = [
        'data',
        'import',
        'index',
        'create',
        'store',
        'show',
        'edit',
        'update',
        'destroy'
    ];
    /**
     * Add the data method for a resourceful route.
     *
     * @param  string  $name
     * @param  string  $base
     * @param  string  $controller
     * @param  array   $options
     * @return \Illuminate\Routing\Route
     */
    protected function addResourceData($name, $base, $controller, $options)
    {
        $uri = $this->getResourceUri($name).'/data';

        $action = $this->getResourceAction($name, $controller, 'data', $options);

        return $this->router->get($uri, $action);
    }


    /**
     * Add the post import method for a resourceful route.
     *
     * @param  string  $name
     * @param  string  $base
     * @param  string  $controller
     * @param  array   $options
     * @return \Illuminate\Routing\Route
     */
    protected function addResourceImport($name, $base, $controller, $options)
    {
        $uri = $this->getResourceUri($name).'/import';

        $action = $this->getResourceAction($name, $controller, 'import', $options);

        return $this->router->post($uri, $action);
    }




}