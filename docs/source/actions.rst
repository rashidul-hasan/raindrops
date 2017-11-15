Actions & Permissions
=====================

In RainDrops, Actions are referred to the various buttons in your CRUD. Like the buttons on the top
of index page, the actions buttons on the index table for each row etc.

You can add new Actions for any CRUD and also control the access for various actions for a logged in user
based on certain logic.

There are four places where you can add new Actions.

    #. On the top of the index page
    #. For each row in the datatable on index page
    #. On the top of the details page
    #. On the top of the edit page

Add Action
~~~~~~~~~~

To add a new action, add a ``setup()`` method to your CRUD Controller and inside that add:

.. code-block:: php

   $this->crudAction->addCrudActions($action_name, $button_text, $url, $place, $button_class, $icon_class);

Parameters definition
---------------------

+-----------------+-----------+------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| Parameter       | Data type | Descriptions                                                                                                                                                                                                                                                                 |
+=================+===========+==============================================================================================================================================================================================================================================================================+
| $action_name    | string    | Name of the action. must be unique                                                                                                                                                                                                                                           |
+-----------------+-----------+------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| $button_text    | string    | Text to show on the button, leave blank if you just want to show an icon                                                                                                                                                                                                     |
+-----------------+-----------+------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| $url            | string    | Url to be linked to with the button. There are two placeholders you can use with the url text ``{route}`` which will be replaced with the CRUD base route and ``{id}`` which will be replaced with the model's primary key if this actions belongs inside the datatable      |
+-----------------+-----------+------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| $place          | string    | Place where should this button be added. supported options: ``index`` , ``table``                                                                                                                                                                                            |
+-----------------+-----------+------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| $button_class   | string    | Class to be applied to the button. you can use bootstrap's button classes to style it                                                                                                                                                                                        |
+-----------------+-----------+------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| $icon_class     | string    | Class name for the icon if you want to use icons in your button, like font awesome                                                                                                                                                                                           |
+-----------------+-----------+------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+

Example
-------

.. code-block:: php

   public function setup()
    {
        $this->crudAction->addCrudActions('import', 'Import', '{route}/import', 'index', 'btn btn-default', 'fa fa-edit');
        $this->crudAction->addCrudActions('import2', '', '{route}/{id}/import', 'table', 'btn btn-xs btn-default', 'fa fa-diamond');
    }
