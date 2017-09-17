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
use Rashidul\RainDrops\Model\ModelHelper;

class DataTableTransformer extends TransformerAbstract
{
    /**
     * @param Model $model
     * @return array
     */
    protected $model;

    protected $actions;

    protected $helper;

    /**
     * DataTableTransformer constructor.
     * @param $actions
     * @internal param $model
     */
    public function __construct($actions = null)
    {
        $this->actions = $actions;
        $this->helper = new Helper();
    }

    public function transform(Model $model)
    {

        $this->model = $model;

        $data = [];

        $fields = ModelHelper::getIndexFields( $model );

        foreach ($fields as $field => $value)
        {

            $customTransform = $this->getTransformerMethodName($field);

            if (method_exists($this, $customTransform))
            {
                $data[$field] = $this->{$customTransform}($this->model->{$field});
                continue;
            }
            // 1. first decide how to show the data
            // function, determines data type by examining 'show' element
            $dataType = $this->helper->getDataType($value);

            // setup the key name of the data array
            if ($dataType == 'relation')
            {
                $fieldName = isset($value['options']) ? $value['options'][0] : $value['show'][0];
                $relatedColumnName = isset($value['options']) ? $value['options'][1] : $value['show'][1];

                $data[$fieldName] = [$relatedColumnName => $this->helper->get($this->model, $field, $value, $dataType)];
            }
            else
            {
                $fieldName = $field;
                $data[$fieldName] = $this->helper->get($this->model, $field, $value, $dataType);
            }

        }

        // now add the actions column
        $data['action'] = ModelHelper::getActionLinks($model, null, $this->actions);

        return $data;
    }

    private function getTransformerMethodName($field)
    {
        return 'show' . studly_case($field);
    }


}