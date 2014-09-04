Summary
=======

Configuration reference
-----------------------

.. code-block:: yaml

    sylius_inventory:
        # The driver used for persistence layer.
        driver: ~
        # Enable/disable backorders.
        backorders: true
        # Array of events for InventoryChangeListener
        events: ~
        # Enable or disbale tracking inventory
        track_inventory: true
        # The availability checker service id.
        checker: sylius.availability_checker.default
        # The inventory operator service id.
        operator: sylius_inventory.operator.default
        classes:
            inventory_unit:
                model: Sylius\Component\Inventory\Model\InventoryUnit
                controller: Sylius\Bundle\InventoryBundle\Controller\InventoryUnitController
                repository: ~ # You can override the repository class here.
            stockable:
                model: ~ # The stockable model class.
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController

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
