<?php
/**
 * Created by PhpStorm.
 * User: rashed
 * Date: 21-Jun-17
 * Time: 4:27 PM
 */

namespace Rashidul\RainDrops\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Rashidul\RainDrops\Crud\PerformCrudActions;
use Rashidul\RainDrops\Crud\ResponseBuilder;
use Yajra\Datatables\Datatables;

abstract class BaseController extends Controller
{
    use PerformCrudActions;

    protected $modelClass;
    protected $model;
    protected $dataTable;
    protected $request;
    protected $responseBuilder;

    // data that will be passed into the view
    protected $viewData;

    // query builder object used by datatable
    protected $dataTableQuery;

    // views
    protected $indexView = 'raindrops::crud.table';
    protected $createView = 'raindrops::crud.form';
    protected $detailsView = 'raindrops::crud.table';
    protected $editView = 'raindrops::crud.form';

    /**
     * BaseController constructor.
     * @internal param $formRequest
     * @internal param $dataTable
     */
    public function __construct()
    {
        $this->request = app(Request::class);
        $this->dataTable = app(Datatables::class);
        $this->responseBuilder = new ResponseBuilder();
        $this->model = new $this->modelClass;

        if (method_exists($this, 'setup'))
        {
            $this->setup();
        }
    }

}