<?php

return [

    /*
    |--------------------------------------------------------------------------
    | configs for the show details page.
    |--------------------------------------------------------------------------
    |
    */
    'show' => [

        /*
         * class name that should be added to the table element.
         * separate multiple class names by space.
         */
        'table_class' => 'table table-borderless table-show',

        /*
         * class for the th tag of the table, this will come in handy if
         * you want to style the table headers differently.
         */
        'header_class' => 'header',

        /*
         * classes for the wrapper div of the table, set false if you
         * don't want any wrapper elements. seperate multiple class names by space.
         */
        'wrapper' => false
    ],

    /*
    |--------------------------------------------------------------------------
    | Data Table specific configuration options for the index page.
    |--------------------------------------------------------------------------
    |
    */

    'index' => [

        /*
        |--------------------------------------------------------------------------
        | Table class
        |--------------------------------------------------------------------------
        |
        | Class(es) added to the table
        | Supported: string
        |
        */

        'class' => 'table table-borderless',

        /*
        |--------------------------------------------------------------------------
        | Table ID
        |--------------------------------------------------------------------------
        |
        | ID given to the table. Used for connecting the table and the Datatables
        | jQuery plugin. If left empty a random ID will be generated.
        | Supported: string
        |
        */

        'id' => 'dataTable',

        /*
        |--------------------------------------------------------------------------
        | Default DataTable options
        |--------------------------------------------------------------------------
        |
        | jQuery dataTable plugin options. The array will be json_encoded and
        | passed through to the plugin. See https://datatables.net/usage/options
        | for more information.
        | Supported: array
        |
        */

        'options' => [

            "processing" => true,
            "responsive" => true

        ],

        /*
        |--------------------------------------------------------------------------
        | DataTable callbacks
        |--------------------------------------------------------------------------
        |
        | jQuery dataTable plugin callbacks. The array will be json_encoded and
        | passed through to the plugin. See https://datatables.net/usage/callbacks
        | for more information.
        | Supported: array
        |
        */

        'callbacks' => array(),

        /*
        |--------------------------------------------------------------------------
        | Skip javascript in table template
        |--------------------------------------------------------------------------
        |
        | Determines if the template should echo the javascript
        | Supported: boolean
        |
        */

        'script' => true,


        /*
        |--------------------------------------------------------------------------
        | Table view
        |--------------------------------------------------------------------------
        |
        | Template used to render the table
        | Supported: string
        |
        */

        'table_view' => 'raindrops::datatable.table',


        /*
        |--------------------------------------------------------------------------
        | Script view
        |--------------------------------------------------------------------------
        |
        | Template used to render the javascript
        | Supported: string
        |
        */
        'script_view' => 'raindrops::datatable.script',


    ],




];
