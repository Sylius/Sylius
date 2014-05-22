Summary
=======

Configuration reference
-----------------------

.. code-block:: yaml

    sylius_inventory:
        driver: ~ # The driver used for persistence layer.
        engine: twig # Templating engine to use by default.
        backorders: true # Enable/disable backorders.
        events: ~ # Array of events for InventoryChangeListener 
        classes:
            unit:
                model: ~ # The inventory unit model class.
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~ # You can override the repository class here.
            stockable:
                model: ~ # The stockable model class.
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~ # You can override the repository class here.
        checker: sylius_inventory.checker.default # The availability checker service id.
        operator: sylius_inventory.operator.default # The inventory operator service id.

`phpspec2 <http://phpspec.net>`_ examples
-----------------------------------------

.. code-block:: bash

    $ composer install --dev --prefer-dist
    $ bin/phpspec run -f pretty

Working examples
----------------

If you want to see working implementation, try out the `Sylius sandbox application <http://github.com/Sylius/Sylius-Sandbox>`_.

Bug tracking
------------

This bundle uses `GitHub issues <https://github.com/Sylius/Sylius/issues>`_.
If you have found bug, please create an issue.
