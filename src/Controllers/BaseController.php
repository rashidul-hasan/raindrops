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
    protected $dataTable;
    protected $request;
    protected $responseBuilder;

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
    }

}