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

        $table = DataTable::of(new $this->modelClass)
            ->setUrl($this->model->getDataUrl());

        // action buttons
        $buttons = $this->crudAction->renderIndexActions();

        $this->viewData = [
            'title' => $this->model->getEntityNamePlural(),
            'model' => $this->model,
            'table' => $table,
            'buttons' => $buttons,
            'view' => $this->indexView,
        ];

        $this->callHookMethod('indexing');

        return $this->responseBuilder->send($this->request, $this->viewData);

    }

}