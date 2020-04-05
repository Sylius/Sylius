.. rst-class:: outdated

Summary
=======

.. danger::

   We're sorry but **this documentation section is outdated**. Please have that in mind when trying to use it.
   You can help us making documentation up to date via Sylius Github. Thank you!

Configuration reference
-----------------------

.. code-block:: yaml

    sylius_inventory:
        # The driver used for persistence layer.
        driver: ~
        # Enable or disbale tracking inventory
        track_inventory: true
        # The availability checker service id.
        checker: sylius.availability_checker.default
        # The inventory operator service id.
        operator: sylius.inventory_operator.default
        # Array of events for InventoryChangeListener
        events: ~
        resources:
            inventory_unit:
                classes:
                    model:      Sylius\Component\Inventory\Model\InventoryUnit
                    interface:  Sylius\Component\Inventory\Model\InventoryUnitInterface
                    controller: Sylius\Bundle\InventoryBundle\Controller\InventoryUnitController
                    repository: ~ # You can override the repository class here.
                    factory:    Sylius\Component\Resource\Factory\Factory
            stockable:
                classes:
                    model: ~ # The stockable model class.
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController

Bug tracking
------------

This bundle uses `GitHub issues <https://github.com/Sylius/Sylius/issues>`_.
If you have found bug, please create an issue.
