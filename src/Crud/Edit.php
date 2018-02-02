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

        $form = FormBuilder::build( $this->model );

        $this->viewData = [
            'title' => 'Edit ' . $this->model->getEntityName(),
            'model' => $this->model,
            'form' => $form,
            'view' => $this->editView,
        ];

        $this->callHookMethod('editing');

        return $this->responseBuilder->send($this->request, $this->viewData);
    }
}