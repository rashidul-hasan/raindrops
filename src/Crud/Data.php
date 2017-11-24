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

        $tableActions = $this->crudAction->getTableActions();
        $this->dataTableQuery = $this->model->select();
        $this->dataTableObject = $this->dataTable->eloquent($this->dataTableQuery)
            ->setTransformer(new $this->dataTransformer($tableActions));

        // let user modify the query builder object to
        // further customize the data to be feed to the
        // datatable via ajax
        if (method_exists($this, 'querying'))
        {
            $this->querying();
        }

        return $this->dataTableObject->make(true);

    }

}