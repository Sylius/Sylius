Basic Usage
===========

Order
-----

Every order has 2 main identifiers, an ID and a human-friendly number. You can access those by calling ``->getId()`` and ``->getNumber()`` respectively.
The number is mutable, so you can change it by calling ``->setNumber('E001')`` on the order instance.

Order Totals
~~~~~~~~~~~~

.. note::
    All money amounts in Sylius are represented as "cents" - integers.
    An order has 3 basic totals, which are all persisted together with the order.
    The first total is the *items total*, it is calculated as the sum of all item totals.
    The second total is the *adjustments total*, you can read more about this in next chapter.

.. code-block:: php

    <?php

    echo $order->getItemsTotal(); //Output will be 1900.
    echo $order->getAdjustmentsTotal(); //Output will be -250.

    $order->calculateTotal();
    echo $order->getTotal(); //Output will be 1650.

The main order total is a sum of the previously mentioned values.
You can access the order total value using the ``->getTotal()`` method.

Recalculation of totals can happen by calling ``->calculateTotal()`` method, using the simplest math. It will also update the item totals.

Items Management
~~~~~~~~~~~~~~~~

The collection of items (Implementing the ``Doctrine\Common\Collections\Collection`` interface) can be obtained using the ``->getItems()``.
To add or remove items, you can simply use the ``addItem`` and ``removeItem`` methods.

.. code-block:: php

    <?php

    use Sylius\Component\Order\Model\Order;
    use Sylius\Component\Order\Model\OrderItem;

    $order = new Order();

    $item1 = new OrderItem();
    $item1->setName('Super cool product');
    $item1->setUnitPrice(1999); // 19.99!
    $item1->setQuantity(2);

    $item2 = new OrderItem();
    $item2->setName('Interesting t-shirt');
    $item2->setUnitPrice(2549); // 25.49!

    $order->addItem($item1);
    $order->addItem($item2);
    $order->removeItem($item1);

Order Item
----------

An order item model has only the **id** property as identifier and it has the order reference, accessible via ``->getOrder()`` method.

Order Item totals
~~~~~~~~~~~~~~~~~

Just like for the order, the total is available via the same method, but the unit price is accessible using the ``->getUnitPrice()``
Each item also can calculate its total, using the quantity (``->getQuantity()``) and the unit price.

.. code-block:: php

    <?php

    use Sylius\Component\Order\Model\OrderItem;

    $item = new OrderItem();
    $item->setUnitPrice(2000);
    $item->setQuantity(4);
    $item->calculateTotal();

    $item->getTotal(); //Output will be 8000.

Applying adjustments to OrderItem
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

An OrderItem can also hold adjustments.

.. code-block:: php

    <?php

    use Sylius\Component\Order\Model\OrderItem;
    use Sylius\Component\Order\Model\Adjustment;

    $adjustment = new Adjustment();
    $adjustment->setAmount(1200);
    $adjustment->setType('tax');

    $item = new OrderItem();
    $item->addAdjustment($adjustment);
    $item->setUnitPrice(2000);
    $item->setQuantity(2);
    $item->calculateTotal();

    $item->getTotal(); //Output will be 5200.

Adjustments
-----------

Neutral Adjustments
~~~~~~~~~~~~~~~~~~~

In some cases, you may want to use **Adjustment** just for displaying purposes.
For example, when your order items have the tax already included in the price.

Every **Adjustment** instance has the ``neutral`` property, which indicates if it should be counted against object total.

.. code-block:: php

    <?php

    use Sylius\Component\Order\Order;
    use Sylius\Component\Order\OrderItem;
    use Sylius\Component\Order\Adjustment;

    $order = new Order();
    $tshirt = new OrderItem();
    $tshirt->setUnitPrice(4999);

    $shippingFees = new Adjustment();
    $shippingFees->setAmount(1000);

    $tax = new Adjustment();
    $tax->setAmount(1150);
    $tax->setNeutral(true);

    $order->addItem($tshirt);
    $order->addAdjustment($shippingFees);
    $order->addAdjustment($tax);

    $order->calculateTotal();
    $order->getTotal();  // Output will be 5999.

Negative Adjustments
~~~~~~~~~~~~~~~~~~~~

**Adjustments** can also have negative amounts, which means that they will decrease the order total by certain amount.
Let's add a 5$ discount to the previous example.

.. code-block:: php

    <?php

    use Sylius\Component\Order\Order;
    use Sylius\Component\Order\OrderItem;
    use Sylius\Component\Order\Adjustment;

    $order = new Order();
    $tshirt = new OrderItem();
    $tshirt->setUnitPrice(4999);

    $shippingFees = new Adjustment();
    $shippingFees->setAmount(1000);

    $tax = new Adjustment();
    $tax->setAmount(1150);
    $tax->setNeutral(true);

    $discount = new Adjustment();
    $discount->setAmount(-500);

    $order->addItem($tshirt);
    $order->addAdjustment($shippingFees);
    $order->addAdjustment($tax);
    $order->addAdjustment($discount);
    $order->calculateTotal();
    $order->getTotal(); // Output will be 5499.

Locked Adjustments
~~~~~~~~~~~~~~~~~~

You can also lock an adjustment, this will ensure that it won't be deleted from order or order item.

.. code-block:: php

    <?php

    use Sylius\Component\Order\Order;
    use Sylius\Component\Order\OrderItem;
    use Sylius\Component\Order\Adjustment;

    $order = new Order();
    $tshirt = new OrderItem();
    $tshirt->setUnitPrice(4999);

    $shippingFees = new Adjustment();
    $shippingFees->setAmount(1000);
    $shippingFees->lock();

    $discount = new Adjustment();
    $discount->setAmount(-500);

    $order->addItem($tshirt);
    $order->addAdjustment($shippingFees);
    $order->addAdjustment($discount);
    $order->removeAdjustment($shippingFees);
    $order->calculateTotal();
    $order->getTotal(); // Output will be 5499.
