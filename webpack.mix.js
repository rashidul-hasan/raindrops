let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for your application, as well as bundling up your JS files.
 |
 */

mix.scripts([
    'node_modules/jquery/dist/jquery.js',
    'node_modules/moment/moment.js',
    'node_modules/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js',
    'node_modules/bootstrap/dist/js/bootstrap.js',
    'node_modules/datatables.net/js/jquery.dataTables.js',
    'node_modules/datatables.net/js/jquery.dataTables.js',
    'node_modules/datatables.net-bs/js/dataTables.bootstrap.js'
], 'public/js/raindrops.libs.js')

    .styles([
        'node_modules/font-awesome/css/font-awesome.min.css',
        'node_modules/bootstrap/dist/css/bootstrap.css',
        'node_modules/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css',
        'node_modules/datatables.net-bs/css/dataTables.bootstrap.css',
    ], 'public/css/raindrops.libs.css')

    .copy('node_modules/font-awesome/fonts/fontawesome-webfont.woff2', 'public/fonts/fontawesome-webfont.woff2')
    .copy('node_modules/bootstrap/fonts/glyphicons-halflings-regular.woff2', 'public/fonts/glyphicons-halflings-regular.woff2');