.. index::
   single: Inventory

Inventory
=========

Sylius leverages a very simple approach to inventory management. The current stock of an item is stored on the *ProductVariant* entity.

It is always accessible via simple API:

.. code-block:: php

    <?php

    echo $productVariant->getOnHand(); // Prints current inventory.

InventoryUnit
-------------

Every item sold in the store is represented by *InventoryUnit*, which has many different states:

* checkout - When item is in the cart.
* onhold - When checkout is completed, but we are waiting for the payment.
* sold - When item has been sold and is no longer in the warehouse.
* returned - Item has been sold, but returned and is in stock.

For example, if someone puts a product "Book" with quantity "4" in the cart, 4 inventory units are created. This allows us for very precise tracking of all sold/returned items.

InventoryUnitFactory
--------------------

Normally, inventory units are created automatically by Sylius and you do not need to bother. If you want to create some inventory units yourself, you should use the ``sylius.inventory_unit_factory`` service.

.. code-block:: php

    <?php

    use Sylius\Component\Inventory\Model\InventoryUnitInterface;

    $variant = // Get variant from product.
    $inventoryUnits = $this->get('sylius.inventory_unit_factory')->create($variant, 6, InventoryUnitInterface::STATE_ONHOLD);

``$inventoryUnits`` is now ArrayCollection with 6 instances of InventoryUnit, referencing the *ProductVariant* and with state `returned`.

InventoryOperator
-----------------

Inventory operator is the service responsible for managing the stock amounts of every *ProductVariant* with following methods:

* increase(variant, quantity)
* hold(variant, quantity)
* release(variant, quantity)
* decrease(InventoryUnit[])

Inventory On Hold
-----------------

Final Thoughts
--------------

...

Learn more
----------

* ...
