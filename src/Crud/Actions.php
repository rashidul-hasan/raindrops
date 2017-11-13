<?php
/**
 * Created by PhpStorm.
 * User: Rashidul Hasan
 * Date: 11/13/2017
 * Time: 2:44 PM
 */

namespace Rashidul\RainDrops\Crud;


trait Actions
{

    // actions
    protected $defaultActions = [
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
            'place' => 'model',
            'btn_class' => 'btn btn-primary',
            'icon_class' => 'fa fa-edit'
        ],
        'view' => [
            'text' => '',
            'url' => '{route}/{id}',
            'place' => 'model',
            'btn_class' => 'btn btn-primary',
            'icon_class' => 'fa fa-eye'
        ],
        'delete' => [
            'text' => '',
            'url' => '{route}/{id}',
            'place' => 'model',
            'btn_class' => 'btn btn-danger',
            'icon_class' => 'fa fa-trash-o'
        ],

    ];

    protected $customActions = [];

    protected function addCrudActions()
    {

        $args = func_get_args();

        switch(func_num_args()){

            case 1:
                if (is_array($args[0]))
                {
                    array_merge($this->customActions, $args[0]);
                }

                break;

            case 2:
                if (is_string($args[0]) && is_array($args[1]))
                {
                    $this->customActions[$args[0]] = $args[1];
                }

                break;

            case 6:
                $this->customActions[$args[0]] = [
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

    protected function getIndexActions()
    {
        $actions = $this->getAllActions();

        return collect($actions)->filter(function ($value, $key){

            return $value['place'] == 'index';

        })->all();
    }

    private function getAllActions()
    {
        return array_merge($this->defaultActions, $this->customActions);
    }

    protected function replaceRoutesInActions($model, $actions)
    {
        $id = ($model->exists) ? $model->getKey() : '';

        $data = collect($actions)->map(function ($item, $key) use ($model, $id){

            $item['url'] = $this->getReplacedUrl($item['url'], $model->getBaseUrl(false), $id);

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

}

class CrudActions
{
    public static function render($actions)
    {
        $html = '';
        $btn_html = '<a href="%s" class="%s"><i class="%s"></i> %s</a>';

        if (!empty($actions))
        {
            foreach ($actions as $action) {
                $html .= sprintf($btn_html, $action['url'], $action['btn_class'], $action['icon_class'], $action['text']);
            }
        }

        return $html;
    }
}