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

        $buttons = '';

        $back_button = [
            'text' => 'Back',
            'url' => $this->model->getBaseUrl(),
            'class' => 'btn btn-default'
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
            'back_button' => $back_button,
            'include_view' => $viewRoot . '.' . 'show',
            'view' => $this->detailsView
        ];

        if (method_exists($this, 'showing'))
        {
            $this->showing();
        }

        return $this->responseBuilder->send($this->request, $this->viewData);
    }

}