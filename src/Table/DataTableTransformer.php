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

            $dataType = $this->helper->getDataType($options);

            if (method_exists($this, $customTransform))
            {
                $value = $this->{$customTransform}($this->model->{$field});
            }
            else
            {
                $value = $this->helper->get($this->model, $field, $options, $dataType);
            }

            if ($dataType == 'relation')
            {
                $relatedColumnName = isset($options['options']) ? $options['options'][1] : $options['show'][1];
                $data[$field] = [$relatedColumnName => $value];
            }
            else
            {
                $data[$field] = $value;
            }

        }

        // now add the actions column
        $crudAction = new CrudAction($this->model);

        // if we need to add/hide any actions based on a single model,
        if (method_exists($this, 'getActions'))
        {
            $actions = $this->getActions();
        }
        else
        {
            $actions = $this->crudActions;
        }
        $data['action'] =  $crudAction->render($crudAction->replaceRoutesInActions($actions));

        return $data;
    }

    private function getTransformerMethodName($field)
    {
        return 'show' . studly_case($field);
    }


}