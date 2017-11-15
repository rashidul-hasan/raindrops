<?php
/**
 * Created by PhpStorm.
 * User: rashidul
 * Date: 14-Oct-17
 * Time: 8:18 PM
 */

namespace Rashidul\RainDrops\Crud;


use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Rashidul\RainDrops\Model\ModelHelper;

trait Update
{

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


}