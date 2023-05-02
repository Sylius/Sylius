.. rst-class:: outdated

Interfaces
==========

.. danger::

   We're sorry but **this documentation section is outdated**. Please have that in mind when trying to use it.
   You can help us making documentation up to date via Sylius Github. Thank you!

Model Interfaces
----------------

.. _component_inventory_model_inventory-unit-interface:

InventoryUnitInterface
~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by model representing a single InventoryUnit.

.. hint::
    It also contains the default :doc:`/components_and_bundles/components/Inventory/state_machine`.

.. note::
    This interface extends `TimestampableInterface <https://github.com/Sylius/SyliusResourceBundle/blob/master/src/Component/Model/TimestampableInterface.php>`_.

.. _component_inventory_model_stockable-interface:

StockableInterface
~~~~~~~~~~~~~~~~~~

This interface provides basic operations for any model that can be stored.

Service Interfaces
------------------

.. _component_inventory_checker_availability-checker-interface:

AvailabilityCheckerInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

This interface provides methods for checking availability of stockable objects.

.. _component_inventory_factory_inventory-unit-factory-interface:

InventoryUnitFactoryInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

This interface is implemented by services responsible for creating collection of new inventory units.

.. _component_inventory_operator_inventory-operator-interface:
