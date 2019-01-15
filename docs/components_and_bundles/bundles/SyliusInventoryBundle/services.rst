.. rst-class:: outdated

Using the services
==================

.. danger::

   We're sorry but **this documentation section is outdated**. Please have that in mind when trying to use it.
   You can help us making documentation up to date via Sylius Github. Thank you!

When using the bundle, you have access to several handy services.

AvailabilityChecker
-------------------

The name speaks for itself, this service checks availability for given stockable object.
`AvailabilityChecker` relies on the current stock level.

There are two methods for checking availability.
``->isStockAvailable()`` just checks whether stockable object is available in stock and doesn't care about quantity.
``->isStockSufficient()`` checks if there is enough units in the stock for given quantity.

InventoryOperator
-----------------

Inventory operator is the heart of this bundle. It can be used to manage stock levels and inventory units.
Creating/destroying inventory units with a given state is also the operators job.
