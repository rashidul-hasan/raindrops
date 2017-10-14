<?php
/**
 * Created by PhpStorm.
 * User: rashidul
 * Date: 14-Oct-17
 * Time: 8:15 PM
 */

namespace Rashidul\RainDrops\Crud;


trait Data
{

    /**
     * handle datatable server side
     */
    public function data()
    {

        $this->dataTableQuery = $this->model->select();

        // let user modify the query builder object to
        // further customize the data to be feed to the
        // datatable via ajax
        if (method_exists($this, 'querying'))
        {
            $this->querying();
        }

        // which actions will be shown for this
        // particular resource
        $actions = property_exists($this, 'actions') ? $this->actions : null;

        return $this->dataTable->eloquent($this->dataTableQuery)
            ->setTransformer(new $this->dataTransformer($actions))
            ->make(true);

    }

}