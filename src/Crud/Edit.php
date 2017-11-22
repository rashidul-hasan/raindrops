<?php
/**
 * Created by PhpStorm.
 * User: rashidul
 * Date: 14-Oct-17
 * Time: 8:18 PM
 */

namespace Rashidul\RainDrops\Crud;


use Rashidul\RainDrops\Facades\FormBuilder;

trait Edit
{

    /**
     * Show the form for editing the specified Resource.
     *
     * @param  int $id
     * @return Response
     * @internal param Request $request
     */
    public function edit($id)
    {

        $this->crudAction->failIfNotPermitted('edit');

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

        $buttons = '';

        $back_button = [
            'text' => 'Back',
            'url' => $this->model->getBaseUrl(),
            'class' => 'btn btn-default'
        ];

        $viewRoot = property_exists($this, 'viewRoot')
            ? $this->viewRoot
            : $this->model->getBaseUrl(false);

        $this->viewData = [
            'title' => 'Edit ' . $this->model->getEntityName(),
            'item' => $this->model,
            'back_button' => $back_button,
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
}