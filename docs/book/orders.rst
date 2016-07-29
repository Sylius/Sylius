.. index::
   single: Orders

Orders
======

**Order** model is one of the most important in Sylius, where many concepts of e-commerce meet.
It represents an order that can be either placed or in progress (cart).

**Order** holds a collection of **OrderItem** instances, which represent products from the shop,
as its physical copies, with chosen variants and quantities.

Each Order is **assigned to the channel** in which it has been created. Moreover the **language** the customer was using
and the **currency with its exchange rate** at the moment of creation are saved.

How to create an Order programmatically?
----------------------------------------

To programmatically create an Order you will of course need a factory.

.. code-block:: php

    /** @var FactoryInterface $order */
    $orderFactory = $this->get('sylius.factory.order');

    /** @var OrderInterface $order */
    $order = $orderFactory->createNew();

Then get a channel to which you would like to add your Order. You can get it from the context or from the repository by code for example.

.. code-block:: php

    /** @var ChannelInterface $channel */
    $channel = $this->container->get('sylius.context.channel')->getChannel();

    $order->setChannel($channel);

What is more the proper Order instance should also have the **Customer** assigned.
You can get it from the repository by email.

.. code-block:: php

    /** @var CustomerInterface $customer */
    $customer = $this->container->get('sylius.repository.customer')->findOneBy(['email' => 'shop@example.com']);

    $order->setCustomer($customer);

A very important part of creating an Order is adding **OrderItems** to it.
Assuming that you have a **Product** with a **ProductVariant** assigned already in the system:

.. code-block:: php

    /** @var ProductInterface $product */
    $product = $this->container->get('sylius.repository.product')->findOneBy([]);

    $variant = $product->getFirstVariant();
    // there are different ways of getting product variants.
    // Instead of getting first variant from the collection you can get one from the repository by code
    // or use the **VariantResolver** service - either default or your own implementation.

    /** @var OrderItemInterface $orderItem */
    $orderItem = $this->container->get('sylius.factory.order_item')->createNew();
    $orderItem->setVariant($variant);

In order to change the amount of items use the **OrderItemQuantityModifier**.

.. code-block:: php

    $this->container->get('sylius.order_item_quantity_modifier')->modify($orderItem, 3);

Add the item to the order. And then call the **CompositeOrderProcessor** on the order to have everything recalculated.

.. code-block:: php

    $order->addItem($orderItem);

    $this->container->get('sylius.order_processing.order_processor')->process($order);

Finally you have to save your order using the repository.

.. code-block:: php

    /** @var OrderRepositoryInterface $orderRepository */
    $orderRepository = $this->get('sylius.repository.order');

    $orderRepository->add($order);

The Order State Machine
-----------------------

Order has also its own state, which can have the following values:

* ``cart`` - before the checkout is completed,
* ``new`` - when checkout is completed the cart is transformed into a ``new`` order,
* ``fulfilled`` - when the order payments and shipments are completed,
* ``cancelled`` - when the order was cancelled.

.. tip::

    The state machine of order is an obvious extension to the :doc:`state machine of checkout </book/checkout>`.

Learn more
----------

* :doc:`Order - Component Documentation </components/Order/index>`
* :doc:`Order - Bundle Documentation </bundles/SyliusOrderBundle/index>`
