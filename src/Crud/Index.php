<?php
/**
 * Created by PhpStorm.
 * User: rashidul
 * Date: 14-Oct-17
 * Time: 8:08 PM
 */

namespace Rashidul\RainDrops\Crud;


use Rashidul\RainDrops\Facades\DataTable;

trait Index
{

    /**
     * Display a listing of the Resources.
     * @return Response
     * @internal param Request $request
     */
    public function index()
    {

        $this->crudAction->failIfNotPermitted('index');

        $ajax = property_exists($this, 'ajax') ? $this->ajax : 'all';

        // configuring the table
        $table = DataTable::of(new $this->modelClass)
            ->setUrl($this->model->getDataUrl());

        // action buttons
        $buttons = $this->crudAction->renderIndexActions();

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

        $this->callHookMethod('indexing');

        return $this->responseBuilder->send($this->request, $this->viewData);

    }

}