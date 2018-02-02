<?php
/**
 * Created by PhpStorm.
 * User: rashidul
 * Date: 14-Oct-17
 * Time: 8:09 PM
 */

namespace Rashidul\RainDrops\Crud;


use Rashidul\RainDrops\Facades\FormBuilder;
use Symfony\Component\HttpFoundation\Response;

trait Create
{

    /**
     * Show the form for creating a new Resource.
     * @return Response
     * @internal param Request $request
     */
    public function create()
    {
        $this->crudAction->failIfNotPermitted('add');

        $form = FormBuilder::build($this->model);

        $this->viewData = [
            'title' => 'Add New ' . $this->model->getEntityName(),
            'form' => $form,
            'buttons' => '',
            'model' => $this->model,
            'view' => $this->createView,
            'success' => true
        ];

        $this->callHookMethod('creating');

        return $this->responseBuilder->send($this->request, $this->viewData);
    }
}