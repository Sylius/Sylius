The Order, OrderItem and OrderItemUnit
======================================

Here is a quick reference of what the default models can do for you.

Order basics
------------

Each order has 2 main identifiers, an *ID* and a human-friendly *number*.
You can access those by calling ``->getId()`` and ``->getNumber()`` respectively.
The number is mutable, so you can change it by calling ``->setNumber('E001')`` on the order instance.

.. code-block:: php

    <?php

    $order->getId();
    $order->getNumber();

    $order->setNumber('E001');

Order totals
------------

.. note::

    All money amounts in Sylius are represented as "cents" - integers.

An order has 3 basic totals, which are all persisted together with the order.

The first total is the *items total*, it is calculated as the sum of all item totals (including theirs adjustments).

The second total is the *adjustments total*, you can read more about this in next chapter.

.. code-block:: php

    <?php

    echo $order->getItemsTotal(); // 1900.
    echo $order->getAdjustmentsTotal(); // -250.

    $order->calculateTotal();
    echo $order->getTotal(); // 1650.

The main order total is a sum of the previously mentioned values.
You can access the order total value using the ``->getTotal()`` method.

.. note::

   It's not needed to call ``calculateTotal()`` method, as both ``itemsTotal`` and ``adjustmentsTotal`` are automatically updated after each operation that can influence their values.

Items management
----------------

The collection of items (Implementing the ``Doctrine\Common\Collections\Collection`` interface) can be obtained using the ``->getItems()``.
To add or remove items, you can simply use the ``addItem`` and ``removeItem`` methods.

.. code-block:: php

    <?php

    // $item1 and $item2 are instances of OrderItemInterface.
    $order
        ->addItem($item)
        ->removeItem($item2)
    ;

OrderItem basics
----------------

An order item model has only the id as identifier, also it has the order to which it belongs, accessible via ``->getOrder()`` method.

The sellable object can be retrieved and set, using the following setter and getter - ``->getProduct()`` & ``->setVariant(ProductVariantInterface $variant)``.

.. code-block:: php

    <?php

    $item->setVariant($book);

.. note::

    In most cases you'll use the **OrderBuilder** service to create your orders.

Just like for the order, the total is available via the same method, but the unit price is accessible using the ``->getUnitPrice()`` 
Each item also can calculate its total, using the quantity (``->getQuantity()``) and the unit price.

.. warning::

   Concept of ``OrderItemUnit`` allows better management of ``OrderItem``'s quantity. Because of that, it's needed to use :ref:`bundle_order_order-item-quantity-modifier` to handle
   quantity modification properly.

.. code-block:: php

    <?php

    $item = $itemRepository->createNew();
    $item->setVariant($book);
    $item->setUnitPrice(2000)

    $orderItemQuantityModifier->modify($item, 4); //modifies item's quantity to 4

    echo $item->getTotal(); // 8000.

An OrderItem can also hold adjustments.

Units management
----------------

Each element from ``units`` collection in ``OrderItem`` represents single, separate unit from order. It's total is sum of its ``item`` unit price and totals' of each adjustments. Unit's can be added
and removed using ``addUnit`` and ``removeUnit`` methods from ``OrderItem``, but it's highly recommended to use :ref:`bundle_order_order-item-quantity-modifier`.
