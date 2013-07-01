The Order and OrderItem
=======================

Here is a quick reference of what the default models can do for you.

Order basics
------------

Each order has 2 main identifiers, an *ID* and a human-friendly *number*.
You can access those by calling ``->getId()`` and ``-getNumber()`` respectively.
The number is mutable, so you can change it by calling ``->setNumber('E001')`` on the order instance.

.. code-block:: php

    <?php

    $order->getId();
    $order->getNumber();

    $order->setNumber('E001');

Confirmation status
-------------------

To check whether the order is confirmed or not, you can use the ``isConfirmed()`` method, which returns *true/false* value.
To change that status, you can use the confirmation setter, ``setConfirmed(false)``. All orders are confirmed by default, unless you enabled e-mail confirmation feature.
Order also can contain a confirmation token, accessible by appropriate getter and setter.

.. code-block:: php

    <?php

    if ($order->isConfirmed()) {
        echo 'This one is confirmed, great!';
    }

Order totals
------------

.. note::

    All money amount in Sylius are represented as "cents" - integers.

Order has 3 basic totals, which are all persisted together with the order.

First total is the *items total*, it's calculated as a sum of all item totals.

Second total is the *adjustments total*, you can read more about those in next chapter.

.. code-block:: php

    <?php

    echo $order->getItemsTotal(); // 1900.
    echo $order->getAdjustmentsTotal(); // -250.

    $order->calculateTotal();
    echo $order->getTotal(); // 1650.

Main order total is a sum of the previously mentioned values.
You can access the order total value using the ``->getTotal()`` method.
Recalculation of totals can happen by calling ``->calculateTotal()`` method, using the simplest possible math. It will also update the item totals.

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

Order item model has only the id as identifier, also it has the order to which it belongs, accessible via ``->getOrder()`` method.

The sellable object can be retrieved and set, using the following setter and getter - ``->getSellable()`` & ``->setSellable(SellableInterface $sellable)``.

.. code-block:: php

    <?php

    $item->setSellable($book);

.. note::

    In most cases you'll use the **OrderBuilder** service to create your orders.

Just like for order, the total is available via the same method, but the unit price is accessible using the ``->getUnitPrice()`` 
Each item also can calculate its total, using the quantity (``->getQuantity()``) and the unit price.

.. code-block:: php

    <?php

    $item = $itemRepository->createNew();
    $item
        ->setSellable($book)
        ->setUnitPrice(2000)
        ->setQuantity(4)
        ->calculateTotal()
    ;

    echo $item->getTotal(); // 8000.

OrderItem can also hold adjustments.
