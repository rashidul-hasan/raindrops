Installation
============

.. toctree::
    :maxdepth: 2
    :caption: Contents:

#. Inside your project root, run

    ``composer require rashidul/raindrops=dev-master``


#. Add this to your ``app.php`` config file's ``providers`` array

    ``Rashidul\RainDrops\RainDropsServiceProvider::class,``


#. Finally, publish the config files.

    ``php artisan vendor:publish --tag=raindrops``