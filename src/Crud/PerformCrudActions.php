<?php
/**
 * Created by PhpStorm.
 * User: rashidul
 * Date: 2/22/2017
 * Time: 12:19 AM
 */

namespace Rashidul\RainDrops\Crud;


use Illuminate\Database\QueryException;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Session;
use Rashidul\RainDrops\Facades\DataTable;
use Rashidul\RainDrops\Facades\DetailsTable;
use Rashidul\RainDrops\Facades\FormBuilder;
use Rashidul\RainDrops\Table\DataTableTransformer;

trait PerformCrudActions
{

    use ValidatesRequests;
    /**
     * Display a listing of the Resources.
     * @return Response
     * @internal param Request $request
     */
    public function index()
    {

        $ajax = property_exists($this, 'ajax') ? $this->ajax : 'all';

        // configuring the table
        $table = DataTable::of(new $this->modelClass)
            ->setUrl($this->modelClass->getDataUrl())
            ->setId('data-table');

        $data = [
            'url' => $this->modelClass->getBaseUrl(),
            'title' => $this->modelClass->getEntityNamePlural(),
            'entity' => $this->modelClass,
            'ajax' => $ajax,
            'table' => $table
        ];
        
        return view('raindrops::crud.index', $data);

    }

    /**
     * handle datatable server side
     */
    public function data()
    {

        $query = $this->modelClass->select();

        return $this->dataTable->eloquent($query)
            ->setTransformer(new DataTableTransformer())
            ->make(true);

    }

    /**
     * Show the form for creating a new Resource.
     * @return Response
     * @internal param Request $request
     */
    public function create()
    {
        // get resource obj
        $item = $this->modelClass;

        // if create form is being requested by ajax
        // generate the form and send it via ajax
        if($this->request->ajax()){

            // TODO.
            // need a way to let user modify the form markup for certain controllers
            $formMarkup = '<div class="box-body"><form action="%s" autocomplete="off" method="POST" enctype="multipart/form-data" class="form-create">%s</form></div>';
            $formFields = $item->createForm();

            return response()->json([
                'status' => 'success',
                'data' => sprintf($formMarkup, url($this->modelClass->getBaseUrl()), $formFields)
            ], 200);

        }

        // TODO feature: if we wanna inject any scripts into this
        // page, we do it here, first we check if there's any
        $view_path = $item->getBaseUrl() . '.' . 'create';

        $data = [
            'title' => 'Add New ' . $item->getEntityName(),
            'url' => $item->getBaseUrl(),
            'item' => $item,
            'back_url' => $item->getBaseUrl(),
            'view_path' => $view_path
        ];

        // check if we need to pass additional data to view
        // there will be a method 'creating', we'll pass the request object
        // and the $data array variable to it, it'll return $data after adding/modifying
        // it's elements
        if (method_exists($this, 'creating')){
            $data = $this->creating($this->formRequest, $data);
        }

        return view('core::cruds.create', $data);

    }

    /**
     * Store a newly created resource in storage.
     * @return Response
     * @internal param Request $request
     * @internal param Request $request
     */
    public function store()
    {

        $this->validate($this->request, $this->modelClass->getvalidationRules(), [], $this->modelClass->getFieldsWithLabels());

        $item = new $this->modelClass();

        $input = $this->request->except(['_token']);


        $item->fill($input);

        $status = null;
        $msg = null;
        $data = null;

        try{
            if ($item->save()){
                $status = 'success';
                $msg = $this->modelClass->getEntityName() . ' Created!';
                $data = $item;
            } else {
                $status = 'error';
                $msg = 'Something went wrong';
            }
        } catch (QueryException $e){
            $msg = $e->getMessage();
            $status = 'error';
        }

        if($this->request->ajax()){

            return response()->json([
                'status' => $status,
                'data' => $data,
                'msg' => $msg
            ], 200);

        }

        Session::flash($status, $msg);
        return $status === 'success' ? redirect($item->getShowUrl()) : redirect()->back();

    }

    /**
     * Display the specified Resource.
     *
     * @param Request $request
     * @param  int $id
     * @return Response
     */
    public function show(Request $request, $id)
    {

        // get item obj by id
        $item = $this->modelClass->findOrFail($id);

        if($request->ajax()){

            $tableMarkup = '<div class="box-body"><table class="table table-borderless table-show"><tbody>%s</tbody></table></div>';
            $tableRows = $item->detailsTable();

            return response()->json([
                'status' => 'success',
                'data' => sprintf($tableMarkup, $tableRows)
            ], 200);

        }

        // prepare table object
        $table = DetailsTable::of($item);

        $data = [
            'title' => $this->modelClass->getEntityName() . ' Details',
            'item' => $item,
            'url' => $this->modelClass->getBaseUrl(),
            'back_url' => $this->modelClass->getBaseUrl(),
            'table' => $table
        ];

        return view('raindrops::crud.show', $data);
    }

    /**
     * Show the form for editing the specified Resource.
     *
     * @param Request $request
     * @param  int $id
     * @return Response
     */
    public function edit(Request $request, $id)
    {

        // get item obj by id
        $item = $this->modelClass->findOrFail($id);

        /**
         * if edit is requested via ajax, generate the form with
         * current values and send it via json response
         *
         * json response object structure:
         *
         * responseObj = {
         *      status: success/error,
         *      data: <the form in html>
         * }
         */
        if($request->ajax()){

            $formMarkup = '<div class="box-body"><form action="%s" autocomplete="off" method="POST" enctype="multipart/form-data" class="form-create">%s</form></div>';
            $formFields = $item->editForm(null);

            return response()->json([
                'status' => 'success',
                'data' => sprintf($formMarkup, $item->getShowUrl(), $formFields)
            ], 200);

        }

        // prepare the form
        $form = FormBuilder::build( $item )
            ->form([
                'action' => 'clients/' .$item->id,
                'method' => 'PUT'
            ]);

        //$view_path = $this->modelClass->getBaseUrl() . '.' . 'edit';

        $data = [
            'title' => 'Edit ' . $this->modelClass->getEntityName(),
            'item' => $item,
            'url' => $this->modelClass->getBaseurl(),
            'back_url' => $item->getShowUrl(),
            'form' => $form,
            //'view_path' => $view_path
        ];

        // check if we need to pass additional data to view
        // there will be a method 'creating', we'll pass the request object
        // and the $data array variable to it, it'll return $data after adding/modifying
        // it's elements
        if (method_exists($this, 'editing')){
            $data = $this->creating($this->formRequest, $data);
        }

        return view('raindrops::crud.edit', $data);
    }

    /**
     * Update the specified Resource in storage.
     *
     * @param Request $request
     * @param  int $id
     * @return Response
     * @internal param Request|UpdateSimCardRequest $request
     */
    public function update(Request $request, $id)
    {
        $item =  $this->modelClass->findOrFail($id);

        $this->validate($request, $this->modelClass->getvalidationRules($item), [], $this->modelClass->getFieldsWithLabels());

        $input = $request->except(['_token', '_method']);

        $item->fill($input);

        $data = null;

        try{
            if ($item->update($input)){
                $success = true;
                $msg = $this->modelClass->getEntityName() . ' Updated!';
                $data = $item;
            } else {
                $success = false;
                $msg = 'Something went wrong';
            }
        } catch (QueryException $e){
            $msg = $e->getMessage();
            $success = false;
        }

        if($request->ajax()){

            return response()->json([
                'success' => $success,
                'msg' => $msg,
                'data' => $data
            ], 200);

        }

        $status = $success ? 'success' : 'error';

        Session::flash($status, $msg);
        return $success ? redirect($item->getShowUrl()) : redirect()->back();

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param  int $id
     * @return Response
     */
    public function destroy(Request $request, $id)
    {
        $item =  $this->modelClass->findOrFail($id);

        try{
            if ($item->delete()){
                $success = true;
                $msg = $this->modelClass->getEntityName() . ' Deleted!';
            } else {
                $success = false;
                $msg = 'Something went wrong';
            }
        } catch (QueryException $e){
            $code = $e->getCode();
            $msg = $code == '23000' ? 'Duplicate entry in the Database.Please check the unique fields for duplicate data' : 'Something went wrong!';
            $success = false;
        }

        if($request->ajax()){

            return response()->json([
                'success' => $success,
                'msg' => $msg,
            ], 200);

        }

        $status = $success ? 'success' : 'error';

        Session::flash($status, $msg);
        return redirect()->back();

    }


}