Models
======

Here is a quick reference for the default models.

InventoryUnit
-------------

Each unit holds reference to stockable object and state, which can be **sold** or **backordered**.
It also provides some handy shortcut methods like `isSold`, `isBackordered` and `getSku`.

Stockable
---------

In order to be able to track stock levels in your application, you must implement `StockableInterface` or use `Stockable` model.
It uses SKU to identify stockable, need to provide display name and to check if stockable is available on demand.
It can get/set current stock level with `getOnHand` and `setOnHand` methods.
