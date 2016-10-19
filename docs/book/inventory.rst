.. index::
   single: Inventory

Inventory
=========

Sylius leverages a very simple approach to inventory management. The current stock of an item is stored on the **ProductVariant** entity as the ``onHand`` value.

InventoryUnit
-------------

InventoryUnit has a relation to a `Stockable <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Component/Inventory/Model/StockableInterface.php>`_ on it,
in case of Sylius Core it will be a relation to the **ProductVariant** that implements the StockableInterface on the **OrderItemUnit** that implements the InventoryUnitInterface.

It represents a physical unit of the product variant that is in the magazine.

Inventory On Hold
-----------------

Putting inventory items ``onHold`` is a way of reserving them before the customer pays for the order. Items are put on hold when the checkout is completed.

OrderInventoryOperator
----------------------

Inventory Operator is the service responsible for managing the stock amounts of every *ProductVariant* on an Order with the following methods:

* ``hold`` - is called when the order's checkout is completed, it puts the inventory units onHold, while still not removing them from onHand,
* ``sell`` - is called when the order's payment are assigned with the state ``paid``. The inventory items are then removed from onHold and onHand,
* ``release`` - is a way of making onHold items of an order back to only onHand,
* ``giveBack`` - is a way of returning sold items back to the inventory onHand,
* ``cancel`` - this method works both when the order is paid and unpaid. It uses both ``giveBack`` and ``release`` methods.

How does Inventory work on examples?
------------------------------------

.. tip::

   You can see all use cases we have designed in Sylius in our `Behat scenarios for inventory <https://github.com/Sylius/Sylius/tree/master/features/inventory>`_.

Learn more
----------

* :doc:`Order concept documentation </book/orders>`
* :doc:`Inventory Bundle documentation </bundles/SyliusInventoryBundle/index>`
* :doc:`Inventory Component documentation </components/Inventory/index>`
