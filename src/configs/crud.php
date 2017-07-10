<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Master layout
    |--------------------------------------------------------------------------
    | Here you set the master layout file for your project. This should have
    | a `raindrops` section where all the CRUD related stuffs will be attached to
    */
    'layout' => 'layouts.master',

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
    ]


];
