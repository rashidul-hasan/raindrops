<?php
/**
 * Created by PhpStorm.
 * User: Rashidul Hasan
 * Date: 11/14/2017
 * Time: 12:52 PM
 */

namespace Rashidul\RainDrops\Crud;


class CrudAction
{

    protected $model;

    // default actions
    protected $crudActions = [
        'add' => [
            'text' => 'Add',
            'url' => '{route}/create',
            'place' => 'index',
            'btn_class' => 'btn btn-primary',
            'icon_class' => ''
        ],
        'edit' => [
            'text' => '',
            'url' => '{route}/{id}/edit',
            'place' => 'table',
            'btn_class' => 'btn btn-xs btn-primary',
            'icon_class' => 'fa fa-edit'
        ],
        'view' => [
            'text' => '',
            'url' => '{route}/{id}',
            'place' => 'table',
            'btn_class' => 'btn btn-xs btn-primary',
            'icon_class' => 'fa fa-eye'
        ],
        'delete' => [
            'text' => '',
            'url' => '{route}/{id}',
            'place' => 'table',
            'btn_class' => 'btn btn-xs btn-danger button-delete',
            'icon_class' => 'fa fa-trash-o',
            'attr' => [
                'data-method' => 'delete',
                'data-confirm' => 'Are you sure?',
                'data-toggle' => 'tooltip',
                'title' => 'Delete'
            ]
        ],

    ];

    /**
     * CrudAction constructor.
     * @param $model
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

    public function addCrudActions()
    {

        $args = func_get_args();

        switch(func_num_args()){

            case 1:
                if (is_array($args[0]))
                {
                    array_merge($this->crudActions, $args[0]);
                }

                break;

            case 2:
                if (is_string($args[0]) && is_array($args[1]))
                {
                    $this->crudActions[$args[0]] = $args[1];
                }

                break;

            case 6:
                $this->crudActions[$args[0]] = [
                    'text' => $args[1],
                    'url' => $args[2],
                    'place' => $args[3],
                    'btn_class' => $args[4],
                    'icon_class' => $args[5]
                ];

                break;

            default:

                throw new \Exception('Invalid Arguments');
        }
    }

    public function permitActions($action_ids)
    {
        $permitted_actions = [];

        foreach ($action_ids as $action_id) {
            if (isset($this->crudActions[$action_id]))
            {
                $permitted_actions[$action_id] = $this->crudActions[$action_id];
            }
        }

        $this->crudActions = $permitted_actions;
    }

    public function restrictActions($action_ids)
    {

        foreach ($action_ids as $action_id) {
            if (isset($this->crudActions[$action_id]))
            {
                unset($this->crudActions[$action_id]);
            }
        }

    }

    public function failIfNotPermitted($action_id)
    {
        if (! array_key_exists($action_id, $this->crudActions)) {
            throw new AccessDeniedException('Unauthorized access');
        }

        return true;
    }

    public function getIndexActions()
    {

        return collect($this->crudActions)->filter(function ($value, $key){

            return $value['place'] == 'index';

        })->all();
    }

    public function getTableActions()
    {

        return collect($this->crudActions)->filter(function ($value, $key){

            return $value['place'] == 'table';

        })->all();
    }

    public function replaceRoutesInActions($actions)
    {
        $id = ($this->model->exists) ? $this->model->getKey() : '';

        $model = $this->model;

        $data = collect($actions)->map(function ($item, $key) use ($model, $id){

            $item['url'] = $this->getReplacedUrl($item['url'], $this->model->getBaseUrl(false), $id);

            return $item;

        })->all();

        return $data;
    }

    private function getReplacedUrl($url, $route, $id)
    {
        $search = [
            '{route}',
            '{id}'
        ];

        $replace = [
            $route,
            $id
        ];

        return url(str_replace($search, $replace, $url));
    }

    public function render($actions)
    {
        $html = '';
        $btn_html = '<a href="%s" class="%s" %s><i class="%s"></i> %s</a>';

        if (!empty($actions))
        {
            foreach ($actions as $action) {
                $attr = $this->getAttributes($action);
                $html .= sprintf($btn_html, $action['url'], $action['btn_class'], $attr, $action['icon_class'], $action['text']);
            }
        }

        return $html;
    }

    public function renderIndexActions()
    {
        $actions = $this->replaceRoutesInActions($this->getIndexActions());

        return $this->render($actions);
    }

    public function renderTableActions()
    {
        $actions = $this->replaceRoutesInActions($this->getTableActions());

        return $this->render($actions);
    }

    private function getAttributes($action)
    {
        $html = '';
        if (isset($action['attr']) && is_array($action['attr']))
        {
            foreach ($action['attr'] as $attr => $value) {
                $html .= $attr . '="' .$value. '" ';
            }
        }

        return $html;
    }

}