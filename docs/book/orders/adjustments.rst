.. index::
   single: Adjustments

Adjustments
===========

**Adjustment** is a resource closely connected to the :doc:`Orders' concept</book/orders/orders>`. It **influences the order's total**.

Adjustments may appear on the Order, the OrderItems and the OrderItemUnits.

There are a few types of adjustments in Sylius:

* Order Promotion Adjustments,
* OrderItem Promotion Adjustments,
* OrderItemUnit Promotion Adjustments,
* Shipping Adjustments,
* Shipping Promotion Adjustments,
* Tax Adjustments

And they can be generally divided into three *groups*: **promotion adjustments**, **shipping adjustments** and **taxes adjustments**.

Also note that adjustments can be either **positive**: charges (with a ``+``)  or **negative**: discounts (with a ``-``).

How to create an Adjustment programmatically?
---------------------------------------------

The Adjustments alone are a bit useless. They should be created alongside Orders.

As usually get a factory and create an adjustment.
Then give it a ``type`` - you can find all the available types on the `AdjustmentInterface <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Component/Core/Model/AdjustmentInterface.php>`_.
The adjustment needs also the ``amount`` - which is the amount of money that will be **added to the orders total**.

.. note::

   The ``amount`` is always saved in the **base currency**.

Additionally you can set the ``label`` that will be displayed on the order view and whether your adjustment is ``neutral`` -
**neutral adjustments** do not affect the order's total (like for example taxes included in price).

.. code-block:: php

   /** @var AdjustmentInterface $adjustment */
   $adjustment = $this->container->get('sylius.factory.adjustment')->createNew();

   $adjustment->setType(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT);
   $adjustment->setAmount(200);
   $adjustment->setNeutral(false);
   $adjustment->setLabel('Test Promotion Adjustment');

   $order->addAdjustment($adjustment);

.. note::

   Remember that if you are creating OrderItem adjustments you have to add them on the OrderItem level.
   The same happens with the OrderItemUnit adjustments, which have to be added on the OrderItemUnit level.

To see changes on the order you need to update it in the database.

.. code-block:: php

   $this->container->get('sylius.manager.order')->flush();

Learn more
----------

* :doc:`Promotions - Concept Documentation </book/orders/promotions>`
* :doc:`Taxation - Concept Documentation </book/orders/taxation>`
* :doc:`Shipments - Concept Documentation </book/orders/shipments>`
