.. rst-class:: outdated

Models
======

.. danger::

   We're sorry but **this documentation section is outdated**. Please have that in mind when trying to use it.
   You can help us making documentation up to date via Sylius Github. Thank you!

Here is a quick reference for the default models.

InventoryUnit
-------------

Each unit holds a reference to a stockable object and its state, which can be **sold** or **returned**.
It also provides some handy shortcut methods like `isSold`.

Stockable
---------

In order to be able to track stock levels in your application, you must implement `StockableInterface` or use the `Stockable` model.
It uses the SKU to identify stockable and need to provide display name.
It can get/set current stock level with `getOnHand` and `setOnHand` methods.
