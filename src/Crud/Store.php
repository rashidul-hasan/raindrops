<?php
/**
 * Created by PhpStorm.
 * User: rashidul
 * Date: 14-Oct-17
 * Time: 8:16 PM
 */

namespace Rashidul\RainDrops\Crud;


use Illuminate\Database\QueryException;
use Rashidul\RainDrops\Model\ModelHelper;

trait Store
{

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

}