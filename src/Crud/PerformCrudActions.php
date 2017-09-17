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
use Rashidul\RainDrops\Facades\DataTable;
use Rashidul\RainDrops\Facades\DetailsTable;
use Rashidul\RainDrops\Facades\FormBuilder;
use Rashidul\RainDrops\Model\ModelHelper;
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
            ->setUrl($this->model->getDataUrl())
            ->setId('data-table');

        // action buttons
        $buttons = [
            'add' => [
                'text' => 'Add',
                'url' => $this->model->getCreateUrl(),
                'class' => 'btn btn-primary'
            ]
        ];

        // if add action is not present in the permitted actions list, remove it
        if (property_exists($this, 'actions') && !in_array('add', $this->actions))
        {
            unset($buttons['add']);
        }

        $viewRoot = property_exists($this, 'viewRoot')
            ? $this->viewRoot
            : $this->model->getBaseUrl(false);

        $this->viewData = [
            'url' => $this->model->getBaseUrl(),
            'title' => $this->model->getEntityNamePlural(),
            'entity' => $this->model,
            'ajax' => $ajax,
            'table' => $table,
            'buttons' => $buttons,
            'view' => $this->indexView,
            'include_view' => $viewRoot . '.' . 'index'

        ];

        if (method_exists($this, 'indexing'))
        {
            $this->indexing();
        }

        return $this->responseBuilder->send($this->request, $this->viewData);

    }

    /**
     * handle datatable server side
     */
    public function data()
    {

        $this->dataTableQuery = $this->model->select();

        // let user modify the query builder object to
        // further customize the data to be feed to the
        // datatable via ajax
        if (method_exists($this, 'querying'))
        {
            $this->querying();
        }

        // which actions will be shown for this
        // particular resource
        $actions = property_exists($this, 'actions') ? $this->actions : null;

        return $this->dataTable->eloquent($this->dataTableQuery)
            ->setTransformer(new $this->dataTransformer($actions))
            ->make(true);

    }

    /**
     * Show the form for creating a new Resource.
     * @return Response
     * @internal param Request $request
     */
    public function create()
    {

        // generate form
        $form = FormBuilder::build($this->model);

        // action buttons
        $buttons = [
            'back' => [
                'text' => 'Back',
                'url' => $this->model->getBaseUrl(),
                'class' => 'btn btn-default'
            ]
        ];

        $viewRoot = property_exists($this, 'viewRoot')
            ? $this->viewRoot
            : $this->model->getBaseUrl(false);

        $this->viewData = [
            'title' => 'Add New ' . $this->model->getEntityName(),
            'back_url' => $this->model->getBaseUrl(),
            'form' => $form,
            'buttons' => $buttons,
            'view' => $this->createView,
            'success' => true,
            'include_view' => $viewRoot . '.' . 'create'
        ];

        // check if we need to pass additional data to view
        // there will be a method 'creating', we'll pass the request object
        // and the $data array variable to it, it'll return $data after adding/modifying
        // it's elements
        if (method_exists($this, 'creating'))
        {
            $this->creating();
        }

        return $this->responseBuilder->send($this->request, $this->viewData);

    }

    /**
     * Store a newly created resource in storage.
     * @return Response
     * @internal param Request $request
     * @internal param Request $request
     */
    public function store()
    {

        $this->validate($this->request, $this->model->getvalidationRules(), [], $this->model->getFieldsWithLabels());

        // fill the model with data from request
        $this->model = ModelHelper::fillWithRequestData($this->model, $this->request);

        // let user do any modfications on the inputs before storing
        if (method_exists($this, 'storing'))
        {
            $this->storing();
        }

        try{
            if ($this->model->save()){
                $this->viewData['success'] = true;
                $this->viewData['message'] = $this->model->getEntityName() . ' Created!';
                $this->viewData['item'] = $this->model;

                if (method_exists($this, 'stored'))
                {
                    $this->stored();
                }

            } else {
                $this->viewData['success'] = false;
                $this->viewData['message'] = 'Something went wrong';
            }
        } catch (QueryException $e){
            $this->viewData['message'] = $e->getMessage();
            $this->viewData['success'] = false;
        }

        // set redirect url
        if ( $this->viewData['success'] )
        {
            $this->viewData['redirect'] = $this->model->getShowUrl();
        }

        return $this->responseBuilder->send($this->request, $this->viewData);

    }

    /**
     * Display the specified Resource.
     *
     * @param  int $id
     * @return Response
     * @internal param Request $request
     */
    public function show($id)
    {

        // get item obj by id
        try
        {
            $this->model = $this->model->findOrFail($id);
        }
        catch (\Exception $e)
        {
            $this->viewData['success'] = false;
            $this->viewData['message'] = $e->getMessage();
            return $this->responseBuilder->send($this->request, $this->viewData);
        }

        // prepare table object
        $table = DetailsTable::of($this->model);

        // action buttons
        $buttons = [
            'edit' => [
                'text' => 'Edit',
                'url' => $this->model->getEditUrl(),
                'class' => 'btn btn-default'
            ],

            'back' => [
                'text' => 'Back',
                'url' => $this->model->getBaseUrl(),
                'class' => 'btn btn-default'
            ]
        ];

        $viewRoot = property_exists($this, 'viewRoot')
            ? $this->viewRoot
            : $this->model->getBaseUrl(false);

        // if edit action is not present in the permitted actions list, remove it
        if (property_exists($this, 'actions') && !in_array('edit', $this->actions))
        {
            unset($buttons['edit']);
        }

        $this->viewData = [
            'title' => $this->model->getEntityName() . ' Details',
            'item' => $this->model,
            'success' => true,
            'back_url' => $this->model->getBaseUrl(),
            'table' => $table,
            'buttons' => $buttons,
            'include_view' => $viewRoot . '.' . 'show',
            'view' => $this->detailsView
        ];

        if (method_exists($this, 'showing'))
        {
            $this->showing();
        }

        return $this->responseBuilder->send($this->request, $this->viewData);
    }

    /**
     * Show the form for editing the specified Resource.
     *
     * @param  int $id
     * @return Response
     * @internal param Request $request
     */
    public function edit($id)
    {

        // get item obj by id
        try
        {
            $this->model = $this->model->findOrFail($id);
        }
        catch (\Exception $e)
        {
            $data['success'] = false;
            $data['message'] = $e->getMessage();
            return $this->responseBuilder->send($this->request, $data);
        }

        // prepare the form
        $form = FormBuilder::build( $this->model );

        // action buttons
        $buttons = [
            [
                'name' => 'back',
                'text' => 'Back',
                'url' => $this->model->getBaseUrl(),
                'class' => 'btn btn-default'
            ]
        ];

        $viewRoot = property_exists($this, 'viewRoot')
            ? $this->viewRoot
            : $this->model->getBaseUrl(false);

        $this->viewData = [
            'title' => 'Edit ' . $this->model->getEntityName(),
            'item' => $this->model,
            'success' => true,
            'url' => $this->model->getBaseurl(),
            'back_url' => $this->model->getShowUrl(),
            'form' => $form,
            'buttons' => $buttons,
            'view' => $this->editView,
            'include_view' => $viewRoot . '.' . 'edit'
        ];

        // check if we need to pass additional data to view
        // there will be a method 'creating', we'll pass the request object
        // and the $data array variable to it, it'll return $data after adding/modifying
        // it's elements
        if (method_exists($this, 'editing'))
        {
            $this->editing();
        }

        return $this->responseBuilder->send($this->request, $this->viewData);
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
        try
        {
            $this->model = $this->model->findOrFail($id);
        }
        catch (\Exception $e)
        {
            $data['success'] = false;
            $data['message'] = $e->getMessage();
            return $this->responseBuilder->send($this->request, $data);
        }

        $this->validate($request, $this->model->getValidationRules(), [], $this->model->getFieldsWithLabels());

        $this->model = ModelHelper::fillWithRequestData($this->model, $this->request);

        if (method_exists($this, 'updating'))
        {
            $this->updating();
        }

        try{
            if ($this->model->update()){
                $this->viewData['success'] = true;
                $this->viewData['message'] = $this->model->getEntityName() . ' Updated!';
                $this->viewData['item'] = $this->model;

                if (method_exists($this, 'updated'))
                {
                    $this->updated();
                }

            } else {
                $this->viewData['success'] = false;
                $this->viewData['message'] = 'Something went wrong';
            }
        } catch (QueryException $e){
            $this->viewData['message'] = $e->getMessage();
            $this->viewData['success'] = false;
        }

        // set redirect url
        if ( $this->viewData['success'] )
        {
            $this->viewData['redirect'] = $this->model->getShowUrl();
        }

        return $this->responseBuilder->send($this->request, $this->viewData);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     * @internal param Request $request
     */
    public function destroy($id)
    {
        // get item obj by id
        try
        {
            $this->model = $this->model->findOrFail($id);
        }
        catch (\Exception $e)
        {
            $data['success'] = false;
            $data['message'] = $e->getMessage();
            return $this->responseBuilder->send($this->request, $data);
        }

        // let the user do something before destroying the item
        if (method_exists($this, 'deleting'))
        {
            $this->deleting();
        }

        try{
            if ($this->model->delete()){
                $this->viewData['success'] = true;
                $this->viewData['message'] = $this->model->getEntityName() . ' Deleted!';
            } else {
                $this->viewData['success'] = false;
                $this->viewData['message'] = 'Something went wrong';
            }
        } catch (QueryException $e){
            $this->viewData['message'] = $e->getMessage();
            $this->viewData['success'] = false;
        }

        return $this->responseBuilder->send($this->request, $this->viewData);

    }


}