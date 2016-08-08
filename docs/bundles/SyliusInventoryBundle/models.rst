Models
======

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
