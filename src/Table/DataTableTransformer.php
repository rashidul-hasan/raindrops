<?php
/**
 * Created by PhpStorm.
 * User: rashed
 * Date: 21-Apr-17
 * Time: 2:57 PM
 */

namespace Rashidul\RainDrops\Table;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use League\Fractal\TransformerAbstract;
use Rashidul\RainDrops\Crud\CrudAction;
use Rashidul\RainDrops\Model\ModelHelper;

class DataTableTransformer extends TransformerAbstract
{
    /**
     * @param Model $model
     * @return array
     */
    protected $model;

    protected $crudActions;

    protected $helper;

    /**
     * DataTableTransformer constructor.
     * @param null $crudActions
     * @internal param $actions
     * @internal param $model
     */
    public function __construct($crudActions = null)
    {
        $this->crudActions = $crudActions;
        $this->helper = new Helper();
    }

    public function transform(Model $model)
    {

        $this->model = $model;

        $data = [];

        $fields = ModelHelper::getIndexFields( $model );

        foreach ($fields as $field => $options)
        {

            $customTransform = $this->getTransformerMethodName($field);

            // 1. first decide how to show the data
            // function, determines data type by examining 'show' element
            $dataType = $this->helper->getDataType($options);

            if (method_exists($this, $customTransform))
            {
                $value = $this->{$customTransform}($this->model->{$field});
            }
            else
            {
                $value = $this->helper->get($this->model, $field, $options, $dataType);
            }

            // setup the key name of the data array
            if ($dataType == 'relation')
            {
                $fieldName = isset($options['options']) ? $options['options'][0] : $options['show'][0];
                $relatedColumnName = isset($options['options']) ? $options['options'][1] : $options['show'][1];

                $data[$fieldName] = [$relatedColumnName => $value];
            }
            else
            {
                $data[$field] = $value;
            }

        }

        // now add the actions column
        $crudAction = new CrudAction($this->model);
        $data['action'] =  $crudAction->render($crudAction->replaceRoutesInActions($this->crudActions));

        return $data;
    }

    private function getTransformerMethodName($field)
    {
        return 'show' . studly_case($field);
    }


}