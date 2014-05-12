Adjustments
===========

**Adjustment** object represents an adjustment to the order's or order item's total.

Their amount can be positive (charges - taxes, shipping fees etc.) or negative (discounts etc.).

Adjustment Basics
------------

Adjustments have the following properties:

+-------------------+-----------------------------------------+
| Attribute         | Description                             |
+===================+=========================================+
| id                | Unique id of the adjustment             |
+-------------------+-----------------------------------------+
| adjustable        | Reference to Order or OrderItem         |
+-------------------+-----------------------------------------+
| label             | Type of the adjustment (e.g. "tax"")    |
+-------------------+-----------------------------------------+
| description       | e.g. "Clothing Tax 9%"                  |
+-------------------+-----------------------------------------+
| amount            | Integer amount                          |
+-------------------+-----------------------------------------+
| neutral           | Boolean flag of neutrality              |
+-------------------+-----------------------------------------+
| createdAt         | Date when adjustment was created        |
+-------------------+-----------------------------------------+
| updatedAt         | Date of last change                     |
+-------------------+-----------------------------------------+

Neutral Adjustments
-------------------

In some cases, you may want to use **Adjustment** just for displaying purposes.
For example, when your order items have the tax already included in the price.

Every **Adjustment** instance has the `neutral` property, which indicates if it should be counted against object total.

.. code-block:: php

    <?php

    use Sylius\Component\Order\Order;
    use Sylius\Component\Order\OrderItem;
    use Sylius\Component\Order\Adjustment;

    $order = new Order();
    $tshirt = new OrderItem();
    $tshirt
        ->setName('Awesome T-Shirt')
        ->setUnitPrice(4999)
    ;

    $shippingFees = new Adjustment();
    $shippingFees->setAmount(1000);

    $tax = new Adjustment();
    $tax
        ->setAmount(1150)
        ->setLabel
        ->setNeutral(true)
    ;

    echo $order
        ->addItem($tshirt)
        ->addAdjustment($shippingFees)
        ->addAdjustment($tax)
        ->calculateTotal()
        ->getTotal()
    ;

    // Output will be 5999.

Negative Adjustments
--------------------

**Adjustments** can also have negative amounts, which means that they will decrease the order total by certain amount.
Let's add a 5$ discount to the previous example.

.. code-block:: php

    <?php

    use Sylius\Component\Order\Order;
    use Sylius\Component\Order\OrderItem;
    use Sylius\Component\Order\Adjustment;

    $order = new Order();
    $tshirt = new OrderItem();
    $tshirt
        ->setName('Awesome T-Shirt')
        ->setUnitPrice(4999)
    ;

    $shippingFees = new Adjustment();
    $shippingFees->setAmount(1000);

    $tax = new Adjustment();
    $tax
        ->setAmount(1150)
        ->setLabel
        ->setNeutral(true)
    ;

    $discount = new Adjustment();
    $discount->setAmount(500);

    echo $order
        ->addItem($tshirt)
        ->addAdjustment($shippingFees)
        ->addAdjustment($tax)
        ->addAdjustment($discount)
        ->calculateTotal()
        ->getTotal()
    ;

    // Output will be 5499.
