Using the services
==================

When using the bundle, you have access to several handy services.

AvailabilityChecker
-------------------

The name speaks for itself, this service checks availability for given stockable object.
It takes backorders setting into account, so if backorders are enabled, stockable will always be available.
Backorders can be enabled per stockable if it is available on demand.
If none of this is the case, it means that backorders are not enabled for given stockable and `AvailabilityChecker` will rely on current stock level.

There are two methods for checking availability.
``->isStockAvailable()`` just checks whether stockable object is available in stock and doesn't care about quantity.
``->isStockSufficient()`` checks if there is enough units in the stock for given quantity.

InventoryOperator
-----------------

Inventory operator is the hearth of this bundle. It can be used to manage stock levels and inventory units.
It can also fill backorders for given stockable, this is very powerful feature in combination with `InventoryChangeListener`.
Creating/destroying inventory units with given state is also operators job.

InventoryChangeListener
-----------------------

It simply triggers ``InventoryOperatorInterface::fillBackorders()``. This can be extended by implementing `InventoryChangeListenerInterface`.
Events can be configured like explained in :doc:`summary`.