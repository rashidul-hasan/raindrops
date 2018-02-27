<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Master layout
    |--------------------------------------------------------------------------
    | Here you set the master layout file for your project. This should have
    | a `raindrops` section where all the CRUD related stuffs will be attached to
    */
    'layout' => 'layouts.app',

    /*
    |--------------------------------------------------------------------------
    | Show title
    |--------------------------------------------------------------------------
    | Should the title text be displayed on the top of the table and form
    | set it false if you need to display the title in some other places
    | other than the default place
    */
    'show_title' => true,

    /*
    |--------------------------------------------------------------------------
    | Generator related configs
    |--------------------------------------------------------------------------
    |
    */
    'generator' => [

        /*
        |--------------------------------------------------------------------------
        | Path for the stub files
        |--------------------------------------------------------------------------
        |
        */
        'stubs' => base_path('resources/crud-generator/'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Labels
    |--------------------------------------------------------------------------
    |
    */
    'labels' => [

        'success' => 'span.label.bg-green',

        'error' => 'span.label.bg-red',
    ],

    /*
    |--------------------------------------------------------------------------
    | File System disk to be used for uploading files
    |--------------------------------------------------------------------------
    |
    */
    'disk' => 'public',

    /*
    |--------------------------------------------------------------------------
    | Root path to be used for showing files in table
    |--------------------------------------------------------------------------
    |
    */
    'filesystem_root' => 'storage',

    /*
    |--------------------------------------------------------------------------
    | Currency formats
    |--------------------------------------------------------------------------
    |
    */
    'currency_formats' => [

        // BDT
        'bdt' => [
            'symbol' => 'BDT',
            'place' => 'left'
        ]

    ],

    /*
    |-------------------------------------------------------------------------
    | Default formats for date, datetime & date types.
    | ref: http://php.net/manual/en/function.date.php
    |-------------------------------------------------------------------------
    |
    */
    'datetime_formats' => [

        'time' => 'g:i A',

        'date' => 'F j, Y',

        'datetime' => 'F j, Y, g:i A',

        'timestamp' => 'F j, Y, g:i A',

    ],

    /*
    |--------------------------------------------------------------------------
    | Classes that will be added to various fields on forms & tables
    |--------------------------------------------------------------------------
    |
    */
    'classes' => [

        /*
        |--------------------------------------------------------------------------
        | Image on details table
        |--------------------------------------------------------------------------
        |
        */
        'image_details' => 'image-details',

        /*
        |--------------------------------------------------------------------------
        | Image on index/list table
        |--------------------------------------------------------------------------
        |
        */
        'image_index' => 'image-list',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default CRUD actions
    |--------------------------------------------------------------------------
    |
    */
    'default_actions' => [
        'index' => [
            'place' => 'permission',
        ],

        'add' => [
            'text' => 'Add',
            'url' => '{route}/create',
            'place' => 'index|show|edit',
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
        'edit_icon' => [
            'text' => 'Edit',
            'url' => '{route}/{id}/edit',
            'place' => 'show',
            'btn_class' => 'btn btn-default',
            'icon_class' => 'fa fa-edit'
        ],
        'view' => [
            'text' => '',
            'url' => '{route}/{id}',
            'place' => 'table',
            'btn_class' => 'btn btn-xs btn-primary',
            'icon_class' => 'fa fa-eye'
        ],
        'view_icon' => [
            'text' => 'Details',
            'url' => '{route}/{id}',
            'place' => 'edit',
            'btn_class' => 'btn btn-default',
            'icon_class' => 'fa fa-eye'
        ],
        'list' => [
            'text' => 'List',
            'url' => '{route}',
            'place' => 'edit|create|show',
            'btn_class' => 'btn btn-default',
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

    ]



];
