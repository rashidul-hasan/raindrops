<?php

return [

    'default_template_group' => 'horizontal',

    'template_groups' => [

        'horizontal' => 'raindrops::fields.horizontal',

        'two-column' => 'raindrops::fields.two-column'

    ],


    'stubs' => [
        'basic' => base_path() . '/vendor/rashidul/raindrops/src/Form/stubs/basic.stub',
        'checkbox' => base_path() . '/vendor/rashidul/raindrops/src/Form/stubs/checkbox.stub',
    ],

    'columns' => 2,


];
