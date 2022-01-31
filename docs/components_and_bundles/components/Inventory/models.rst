.. rst-class:: outdated

Models
======

.. danger::

   We're sorry but **this documentation section is outdated**. Please have that in mind when trying to use it.
   You can help us making documentation up to date via Sylius Github. Thank you!

.. _component_inventory_model_inventory-unit:

InventoryUnit
-------------

**InventoryUnit** object represents an inventory unit.
InventoryUnits have the following properties:

+----------------+----------------------------------------------------------------------------------------------------+
| Property       | Description                                                                                        |
+================+====================================================================================================+
| id             | Unique id of the inventory unit                                                                    |
+----------------+----------------------------------------------------------------------------------------------------+
| stockable      | Reference to any stockable unit. (Implements :ref:`component_inventory_model_stockable-interface`) |
+----------------+----------------------------------------------------------------------------------------------------+
| inventoryState | State of the inventory unit (e.g. "checkout", "sold")                                              |
+----------------+----------------------------------------------------------------------------------------------------+
| createdAt      | Date when inventory unit was created                                                               |
+----------------+----------------------------------------------------------------------------------------------------+
| updatedAt      | Date of last change                                                                                |
+----------------+----------------------------------------------------------------------------------------------------+

.. note::
    This model implements the :ref:`component_inventory_model_inventory-unit-interface`
