<?php
/**
 * Created by PhpStorm.
 * User: rashed
 * Date: 21-Jun-17
 * Time: 4:27 PM
 */

namespace Rashidul\RainDrops\Controllers;

use Illuminate\Container\Container;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use Rashidul\RainDrops\Crud\Create;
use Rashidul\RainDrops\Crud\CrudAction;
use Rashidul\RainDrops\Crud\Data;
use Rashidul\RainDrops\Crud\Destroy;
use Rashidul\RainDrops\Crud\Edit;
use Rashidul\RainDrops\Crud\Index;
use Rashidul\RainDrops\Crud\ResponseBuilder;
use Rashidul\RainDrops\Crud\Show;
use Rashidul\RainDrops\Crud\Store;
use Rashidul\RainDrops\Crud\Update;
use Rashidul\RainDrops\Table\DataTableTransformer;
use Yajra\Datatables\Datatables;

abstract class BaseController extends Controller
{
    use ValidatesRequests, Index, Create, Show, Edit,
        Update, Data, Store, Destroy;

    protected $modelClass;
    protected $model;
    protected $dataTable;
    protected $request;
    protected $responseBuilder;

    // data that will be passed into the view
    protected $viewData;

    // query builder object used by datatable
    protected $dataTableQuery;
    protected $dataTableObject;

    // transformer class to be used by datatble
    protected $dataTransformer = DataTableTransformer::class;

    // views
    protected $indexView = 'raindrops::crud.table';
    protected $createView = 'raindrops::crud.form';
    protected $detailsView = 'raindrops::crud.table';
    protected $editView = 'raindrops::crud.form';

    // class to handle crud actions
    protected $crudAction;

    protected $container;

    /**
     * BaseController constructor.
     * @internal param $formRequest
     * @internal param $dataTable
     */
    public function __construct()
    {
        $this->dataTable = app(Datatables::class);
        $this->responseBuilder = new ResponseBuilder();
        $this->model = new $this->modelClass;
        $this->crudAction = new CrudAction($this->model);
        $this->container = Container::getInstance();

        if (property_exists($this, 'inject')){
            foreach ($this->inject as $property_name => $class){
                if (class_exists($class)){
                    $this->{$property_name} = app($class);
                }
            }
        }

        $this->middleware(function ($request, $next) {
            $this->request = $request;

            $this->callHookMethod('setup');

            return $next($request);
        });
    }

    protected function callHookMethod($name)
    {
        if (method_exists($this, $name))
        {
            $this->container->call([$this, $name]);
        }
    }

}