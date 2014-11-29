The Order and OrderItem
=======================

The library comes with 2 basic models representing the **Order** and it **OrderItems**.

Order Basics
------------

Order is constructed with the following attributes:

+-------------------+-----------------------------------------+-----------------------+
| Attribute         | Description                             | Returned value        |
+===================+=========================================+=======================+
| id                | Unique id of the order                  | mixed                 |
+-------------------+-----------------------------------------+-----------------------+
| number            | Human-friendly number                   | string                |
+-------------------+-----------------------------------------+-----------------------+
| state             | String represeting the status           | string                |
+-------------------+-----------------------------------------+-----------------------+
| items             | Collection of OrderItems                | OrderItemInterface[]  |
+-------------------+-----------------------------------------+-----------------------+
| itemsTotal        | Total value of the items                | integer               |
+-------------------+-----------------------------------------+-----------------------+
| adjustments       | Collection of Adjustments               | AdjustmentInterface[] |
+-------------------+-----------------------------------------+-----------------------+
| adjustmentsTotal  | Total value of adjustments              | integer               |
+-------------------+-----------------------------------------+-----------------------+
| total             | Order grand total                       | integer               |
+-------------------+-----------------------------------------+-----------------------+
| confirmed         | Boolean indicator of order confirmation | boolean               |
+-------------------+-----------------------------------------+-----------------------+
| confirmationToken | Random string for order confirmation    | string                |
+-------------------+-----------------------------------------+-----------------------+
| createdAt         | Date when order was created             | \DateTime             |
+-------------------+-----------------------------------------+-----------------------+
| updatedAt         | Date of last change                     | \DateTime             |
+-------------------+-----------------------------------------+-----------------------+
| completedAt       | Checkout completion time                | \DateTime             |
+-------------------+-----------------------------------------+-----------------------+
| deletedAt         | Date of deletion                        | \DateTime             |
+-------------------+-----------------------------------------+-----------------------+

Each order has 2 main identifiers, an *ID* and a human-friendly *number*.
You can access those by calling ``->getId()`` and ``->getNumber()`` respectively.
The number is mutable, so you can change it by calling ``->setNumber('E001')`` on the order instance.

.. code-block:: php

    <?php

    $order->getId();
    $order->getNumber();

    $order->setNumber('E001');

Confirmation Status
-------------------

To check whether the order is confirmed or not, you can use the ``isConfirmed()`` method, which returns a *true/false* value.

To change that status, you can use the confirmation setter, ``setConfirmed(false)``. All orders are confirmed by default.
Order can contain a confirmation token, accessible by the appropriate getter and setter.

.. code-block:: php

    <?php

    if ($order->isConfirmed()) {
        echo 'This one is confirmed, great!';
    }

Order Totals
------------

.. note::

    All money amounts in Sylius are represented as "cents" - integers.

An order has 3 basic totals, which are all persisted together with the order.

The first total is the *items total*, it is calculated as the sum of all item totals.

The second total is the *adjustments total*, you can read more about this in next chapter.

.. code-block:: php

    <?php

    echo $order->getItemsTotal(); // 1900.
    echo $order->getAdjustmentsTotal(); // -250.

    $order->calculateTotal();
    echo $order->getTotal(); // 1650.

The main order total is a sum of the previously mentioned values.
You can access the order total value using the ``->getTotal()`` method.

Recalculation of totals can happen by calling ``->calculateTotal()`` method, using the simplest math. It will also update the item totals.

Items Management
----------------

The collection of items (Implementing the ``Doctrine\Common\Collections\Collection`` interface) can be obtained using the ``->getItems()``.
To add or remove items, you can simply use the ``addItem`` and ``removeItem`` methods.

.. code-block:: php

    <?php

    use Sylius\Component\Order\Model\Order;
    use Sylius\Component\Order\Model\OrderItem;

    $order = new Order();

    $item1 = new OrderItem();
    $item1
        ->setName('Super cool product')
        ->setUnitPrice(1999) // 19.99!
        ->setQuantity(2)
    ;
    $item1 = new OrderItem();
    $item1
        ->setName('Interesting t-shirt')
        ->setUnitPrice(2549) // 25.49!
    ;

    $order
        ->addItem($item1)
        ->addItem($item2)
        ->removeItem($item1)
    ;

OrderItem Basics
----------------

**OrderItem** model has the attributes listed below:

+------------------+-----------------------------+-----------------------+
| Attribute        | Description                 | Returned value        |
+==================+=============================+=======================+
| id               | Unique id of the item       | mixed                 |
+------------------+-----------------------------+-----------------------+
| order            | Reference to an Order       | OrderInterface        |
+------------------+-----------------------------+-----------------------+
| unitPrice        | The price of a single unit  | integer               |
+------------------+-----------------------------+-----------------------+
| quantity         | Quantity of sold item       | integer               |
+------------------+-----------------------------+-----------------------+
| adjustments      | Collection of Adjustments   | adjustmentInterface[] |
+------------------+-----------------------------+-----------------------+
| adjustmentsTotal | Total value of adjustments  | integer               |
+------------------+-----------------------------+-----------------------+
| total            | Order grand total           | integer               |
+------------------+-----------------------------+-----------------------+
| createdAt        | Date when order was created | \DateTime             |
+------------------+-----------------------------+-----------------------+
| updatedAt        | Date of last change         | \DateTime             |
+------------------+-----------------------------+-----------------------+

An order item model has only the **id** property as identifier and it has the order reference, accessible via ``->getOrder()`` method.

.. code-block:: php

    <?php

    echo $item->getId(); / Prints e.g. 12.
    $item->setName($book);

Just like for the order, the total is available via the same method, but the unit price is accessible using the ``->getUnitPrice()`` 
Each item also can calculate its total, using the quantity (``->getQuantity()``) and the unit price.

.. code-block:: php

    <?php

    use Sylius\Component\Order\Model\OrderItem;

    $item = new OrderItem();
    $item
        ->setName('Game of Thrones')
        ->setUnitPrice(2000)
        ->setQuantity(4)
        ->calculateTotal()
    ;

    echo $item->getTotal(); // 8000.

An OrderItem can also hold adjustments.
