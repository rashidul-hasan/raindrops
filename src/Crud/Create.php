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
            $data = $this->creating();

            if ($data instanceof Response)
            {
                return $data;
            }
        }

        return $this->responseBuilder->send($this->request, $this->viewData);

    }
}