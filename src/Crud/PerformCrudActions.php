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

        // action buttons
        $buttons = [
            [
                'name' => 'add',
                'text' => 'Add',
                'url' => $this->modelClass->getCreateUrl(),
                'class' => 'btn btn-primary'
            ]
        ];

        $data = [
            'url' => $this->modelClass->getBaseUrl(),
            'title' => $this->modelClass->getEntityNamePlural(),
            'entity' => $this->modelClass,
            'ajax' => $ajax,
            'table' => $table,
            'buttons' => $buttons,
            'include_view' => $this->modelClass->getBaseUrl() . '.' . 'index'

        ];

        if (method_exists($this, 'indexing'))
        {
            $data = $this->indexing($this->request, $data);
        }

        return view('raindrops::crud.table', $data);

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

        // generate form
        $form = FormBuilder::build( $item );

        // action buttons
        $buttons = [
            [
                'name' => 'back',
                'text' => 'Back',
                'url' => $item->getBaseUrl(),
                'class' => 'btn btn-default'
            ]
        ];

        $data = [
            'title' => 'Add New ' . $item->getEntityName(),
            'back_url' => $item->getBaseUrl(),
            'form' => $form,
            'buttons' => $buttons,
            'view' => 'raindrops::crud.form',
            'include_view' => $this->modelClass->getBaseUrl() . '.' . 'create'
        ];

        // check if we need to pass additional data to view
        // there will be a method 'creating', we'll pass the request object
        // and the $data array variable to it, it'll return $data after adding/modifying
        // it's elements
        if (method_exists($this, 'creating'))
        {
            $data = $this->creating($this->request, $data);
        }

        return $this->responseBuilder->send($this->request, $data);

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

        // handle files uploads
        $fileInputs = $item->getFileFields();
        foreach ($fileInputs as $field => $options)
        {
            if ($this->request->hasFile($field))
            {
                $input[$field] = $this->request->file($field)->store($field, 'public');
            }
        }
        $item->fill($input);

        try{
            if ($item->save()){
                $data['success'] = true;
                $data['message'] = $this->modelClass->getEntityName() . ' Created!';
                $data['item'] = $item;
            } else {
                $data['success'] = false;
                $data['message'] = 'Something went wrong';
            }
        } catch (QueryException $e){
            $data['message'] = $e->getMessage();
            $data['success'] = false;
        }

        // set redirect url
        if ( $data['success'] )
        {
            $data['redirect'] = $item->getShowUrl();
        }

        return $this->responseBuilder->send($this->request, $data);

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
        try
        {
            $item = $this->modelClass->findOrFail($id);
        }
        catch (\Exception $e)
        {
            $data['success'] = false;
            $data['message'] = $e->getMessage();
            return $this->responseBuilder->send($this->request, $data);
        }

        // prepare table object
        $table = DetailsTable::of($item);

        // action buttons
        $buttons = [
            [
                'name' => 'edit',
                'text' => 'Edit',
                'url' => $item->getEditUrl(),
                'class' => 'btn btn-default'
            ],

            [
                'name' => 'back',
                'text' => 'Back',
                'url' => $item->getBaseUrl(),
                'class' => 'btn btn-default'
            ]
        ];

        $data = [
            'title' => $this->modelClass->getEntityName() . ' Details',
            'item' => $item,
            'success' => true,
            'back_url' => $this->modelClass->getBaseUrl(),
            'table' => $table,
            'buttons' => $buttons,
            'include_view' => $this->modelClass->getBaseUrl() . '.' . 'show',
            'view' => 'raindrops::crud.table'
        ];

        if (method_exists($this, 'showing'))
        {
            $data = $this->showing($this->request, $data);
        }

        return $this->responseBuilder->send($this->request, $data);
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
        try
        {
            $item = $this->modelClass->findOrFail($id);
        }
        catch (\Exception $e)
        {
            $data['success'] = false;
            $data['message'] = $e->getMessage();
            return $this->responseBuilder->send($this->request, $data);
        }

        // prepare the form
        $form = FormBuilder::build( $item );

        // action buttons
        $buttons = [
            [
                'name' => 'back',
                'text' => 'Back',
                'url' => $item->getBaseUrl(),
                'class' => 'btn btn-default'
            ]
        ];

        $data = [
            'title' => 'Edit ' . $this->modelClass->getEntityName(),
            'item' => $item,
            'success' => true,
            'url' => $this->modelClass->getBaseurl(),
            'back_url' => $item->getShowUrl(),
            'form' => $form,
            'buttons' => $buttons,
            'view' => 'raindrops::crud.form',
            'include_view' => $this->modelClass->getBaseUrl() . '.' . 'edit'
        ];

        // check if we need to pass additional data to view
        // there will be a method 'creating', we'll pass the request object
        // and the $data array variable to it, it'll return $data after adding/modifying
        // it's elements
        if (method_exists($this, 'editing'))
        {
            $data = $this->editing($this->request, $data);
        }

        return $this->responseBuilder->send($this->request, $data);
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
        // get item obj by id
        try
        {
            $item = $this->modelClass->findOrFail($id);
        }
        catch (\Exception $e)
        {
            $data['success'] = false;
            $data['message'] = $e->getMessage();
            return $this->responseBuilder->send($this->request, $data);
        }

        $this->validate($request, $this->modelClass->getvalidationRules($item), [], $this->modelClass->getFieldsWithLabels());

        $input = $request->except(['_token', '_method']);

        $item->fill($input);

        try{
            if ($item->update()){
                $data['success'] = true;
                $data['message'] = $this->modelClass->getEntityName() . ' Updated!';
                $data['item'] = $item;
            } else {
                $data['success'] = false;
                $data['message'] = 'Something went wrong';
            }
        } catch (QueryException $e){
            $data['message'] = $e->getMessage();
            $data['success'] = false;
        }

        // set redirect url
        if ( $data['success'] )
        {
            $data['redirect'] = $item->getShowUrl();
        }

        return $this->responseBuilder->send($this->request, $data);

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
        // get item obj by id
        try
        {
            $item = $this->modelClass->findOrFail($id);
        }
        catch (\Exception $e)
        {
            $data['success'] = false;
            $data['message'] = $e->getMessage();
            return $this->responseBuilder->send($this->request, $data);
        }

        try{
            if ($item->delete()){
                $data['success'] = true;
                $data['message'] = $this->modelClass->getEntityName() . ' Deleted!';
            } else {
                $data['success'] = false;
                $data['message'] = 'Something went wrong';
            }
        } catch (QueryException $e){
            $data['message'] = $e->getMessage();
            $data['success'] = false;
        }

        return $this->responseBuilder->send($this->request, $data);

    }


}