<?php
/**
 * Created by PhpStorm.
 * User: rashidul
 * Date: 14-Oct-17
 * Time: 8:17 PM
 */

namespace Rashidul\RainDrops\Crud;


use Rashidul\RainDrops\Facades\DetailsTable;

trait Show
{

    /**
     * Display the specified Resource.
     *
     * @param  int $id
     * @return Response
     * @internal param Request $request
     */
    public function show($id)
    {
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

        $table = DetailsTable::of($this->model);

        $buttons = $this->crudAction->renderViewActions($this->model);

        // if edit action is not present in the permitted actions list, remove it
        if (property_exists($this, 'actions') && !in_array('edit', $this->actions))
        {
            unset($buttons['edit']);
        }

        $this->viewData = [
            'title' => $this->model->getEntityName() . ' Details',
            'model' => $this->model,
            'success' => true,
            'table' => $table,
            'buttons' => $buttons,
            'view' => $this->detailsView
        ];

        $this->callHookMethod('showing');

        return $this->responseBuilder->send($this->request, $this->viewData);
    }

}