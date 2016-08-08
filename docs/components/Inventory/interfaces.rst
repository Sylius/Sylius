Interfaces
==========

Model Interfaces
----------------

.. _component_inventory_model_inventory-unit-interface:

InventoryUnitInterface
~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by model representing a single InventoryUnit.

.. hint::
    It also contains the default :doc:`/components/Inventory/state_machine`.

.. note::
    This interface extends :ref:`component_resource_model_timestampable-interface`.

    For more detailed information go to `Sylius API InventoryUnitInterface`_.

.. _Sylius API InventoryUnitInterface: http://api.sylius.org/Sylius/Component/Inventory/Model/InventoryUnitInterface.html

.. _component_inventory_model_stockable-interface:

StockableInterface
~~~~~~~~~~~~~~~~~~

This interface provides basic operations for any model that can be stored.

.. note::
    For more detailed information go to `Sylius API StockableInterface`_.

.. _Sylius API StockableInterface: http://api.sylius.org/Sylius/Component/Inventory/Model/StockableInterface.html

Service Interfaces
------------------

.. _component_inventory_checker_availability-checker-interface:

AvailabilityCheckerInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

This interface provides methods for checking availability of stockable objects.

.. note::
    For more detailed information go to `Sylius API AvailabilityCheckerInterface`_.

.. _Sylius API AvailabilityCheckerInterface: http://api.sylius.org/Sylius/Component/Inventory/Checker/AvailabilityCheckerInterface.html

.. _component_inventory_factory_inventory-unit-factory-interface:

InventoryUnitFactoryInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

This interface is implemented by services responsible for creating collection of new inventory units.

.. note::
    For more detailed information go to `Sylius API InventoryUnitFactoryInterface`_.

.. _Sylius API InventoryUnitFactoryInterface: http://api.sylius.org/Sylius/Component/Inventory/Factory/InventoryUnitFactoryInterface.html

.. _component_inventory_operator_inventory-operator-interface:

InventoryOperatorInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~

This interface is implemented by services responsible for managing inventory units.

.. note::
    For more detailed information go to `Sylius API InventoryOperatorInterface`_.

.. _Sylius API InventoryOperatorInterface: http://api.sylius.org/Sylius/Component/Inventory/Operator/AvailabilityCheckerInterface.html
