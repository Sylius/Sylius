.. index::
   single: Pricing

Pricing
=======

Pricing is a part of Sylius responsible for calculating the product prices in the cart.

The **ProductVariant** implements the `PriceableInterface <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Component/Pricing/Model/PriceableInterface.php>`_
which enhances it by a **pricing calculator** with a separate **pricing configuration**.

.. note::

    All prices in Sylius are saved as integers in the **base currency**, so 14.99$ is stored as 1499 in the database.

Calculators
-----------

A calculator is a very simple service implementing the `CalculatorInterface <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Component/Pricing/Calculator/CalculatorInterface.php>`_
and has a very important method: **calculate()**.

Every time a product is added to cart or removed from cart and generally on every cart modification event, this method is used to calculate the unit price of every product.

The delegating calculator service takes the appropriate calculator configured on the product, together with the configuration and context.
Context is taken from the product as well, but context comes from the cart.

The context configuration contains the following details:

* quantity of the cart item
* customer
* groups
* channel

You can easily implement your own pricing calculator and allow store managers to define complex pricing rules for any merchandise.

Calculator types
----------------

The default, available calculator types are:

* ``standard``
* ``volume_based``
* ``channel_based``
* ``group_based``
* ``zone_based``

These types are available via constants in
`Core Calculators <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Component/Core/Pricing/Calculators.php>`_
and `Pricing Calculators <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Component/Pricing/Calculator/Calculators.php>`_.

How to calculate prices programmatically?
-----------------------------------------

First get the product variant for you would like to calculate the price. Search for it in the repository by its code.

.. code-block:: php

   /** @var ProductVariantInterface $variant */
   $variant =  $this->container->get('sylius.repository.product_variant')->findOneBy(['code' => 't-shirt-1']);

Prepare the context data for calculating the price. We need the current **channel** and the **customer**.

.. code-block:: php

   /** @var ChannelInterface $channel */
   $channel = $this->container->get('sylius.context.channel')->getChannel();
   /** @var CustomerInterface $customer */
   $customer = $this->container->get('sylius.context.customer')->getCustomer();

Finally get the calculator, which is a service accessibe via the ``sylius.price_calculator`` id, and calculate the price.

.. code-block:: php

   /** @var CalculatorInterface $calculator */
   $calculator = $this->container->get('sylius.price_calculator');

   $price = $calculator->calculate($variant, [
      'customer' => $customer,
      'groups' => [$customer->getGroup()],
      'channel' => [$channel],
      'quantity' => 5
   ]);

Learn more
----------

* :doc:`Pricing - Component Documentation </components/Pricing/index>`
* :doc:`Pricing - Bundle Documentation </bundles/SyliusPricingBundle/index>`
