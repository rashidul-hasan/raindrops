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



];
