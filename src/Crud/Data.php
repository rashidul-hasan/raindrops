<?php
/**
 * Created by PhpStorm.
 * User: rashidul
 * Date: 14-Oct-17
 * Time: 8:15 PM
 */

namespace Rashidul\RainDrops\Crud;

use Rashidul\RainDrops\Model\ModelHelper;
use Rashidul\RainDrops\Table\Helper;


trait Data
{

    protected $helper;

    /**
     * handle datatable server side
     */
    public function data()
    {

        $tableActions = $this->crudAction->getTableActions();
        $index_fields = array_keys(ModelHelper::getIndexFields( $this->model ));
        $index_fields[] = 'action';
        $this->dataTableQuery = $this->model->select();
        $this->dataTableObject = $this->dataTable->eloquent($this->dataTableQuery)
            ->rawColumns($index_fields);
        $this->helper = new Helper();

        $this->editColumns();

        $this->addActionColumn($tableActions);

        // let user modify the query builder object to
        // further customize the data to be feed to the
        // datatable via ajax
        if (method_exists($this, 'querying'))
        {
            $this->querying();
        }

        return $this->dataTableObject->make(true);

    }

    protected function editColumns()
    {
        $fields = ModelHelper::getIndexFields( $this->model );

        foreach ($fields as $field => $options)
        {
            $dataType = $this->helper->getDataType($options);

            $this->dataTableObject->editColumn($field, function ($item) use($field, $options,$dataType){
                return $this->helper->get($item, $field, $options, $dataType);
            });
        }
    }

    protected function addActionColumn($tableActions)
    {
        $this->dataTableObject->editColumn('action', function ($item) use ($tableActions){

            $crudAction = new CrudAction($item);

            if (method_exists($this, 'getActions'))
            {
                $actions = $this->getActions($item);
            }
            else
            {
                $actions = $tableActions;
            }
            return $crudAction->render($crudAction->replaceRoutesInActions($actions));
        });
    }

}