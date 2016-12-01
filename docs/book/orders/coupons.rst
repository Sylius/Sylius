.. index::
   single: Coupons

Coupons
=======

The concept of coupons is closely connected to the :doc:`Promotions Concept </book/orders/promotions>`.

Coupon Parameters
-----------------

A **Coupon** besides a ``code`` has a date when it expires, the ``usageLimit`` and it counts how many times it was already used.

How to create a coupon with a promotion programmatically?
---------------------------------------------------------

.. warning::

   The promotion has to be ``couponBased = true`` in order to be able to hold a collection of Coupons that belong to it.

Let's create a promotion that will have a single coupon that activates the free shipping promotion.

.. code-block:: php

   /** @var PromotionInterface $promotion */
   $promotion = $this->container->get('sylius.factory.promotion')->createNew();

   $promotion->setCode('free_shipping');
   $promotion->setName('Free Shipping');

Remember to set a **channel** for your promotion and to make it **couponBased**!

.. code-block:: php

   $promotion->addChannel($this->container->get('sylius.repository.channel')->findOneBy(['code' => 'US_Web_Store']));

   $promotion->setCouponBased(true);

Then create a coupon and add it to the promotion:

.. code-block:: php

   /** @var CouponInterface $coupon */
   $coupon = $this->container->get('sylius.factory.promotion_coupon')->createNew();

   $coupon->setCode('FREESHIPPING');

   $promotion->addCoupon($coupon);

Now create an PromotionAction that will take place after applying this promotion - 100% discount on shipping

.. code-block:: php

   /** @var PromotionActionFactoryInterface $actionFactory */
   $actionFactory = $this->container->get('sylius.factory.promotion_action');

   // Provide the amount in float ( 1 = 100%, 0.1 = 10% )
   $action = $actionFactory->createShippingPercentageDiscount(1);

   $promotion->addAction($action);

   $this->container->get('sylius.repository.promotion')->add($promotion);

Finally to see the effects of your promotion with couponyou need to **apply a coupon on the Order**.

How to apply a coupon to an Order?
----------------------------------

To apply you promotion with coupon that gives 100% discount on the shipping costs
you need an order that has shipments. Set your promotion coupon on that order -
this is what happens when a customer provides a coupon code during checkout.

And after that call the OrderProcessor on the order to have the promotion applied.

.. code-block:: php

   $order->setPromotionCoupon($coupon);

   $this->container->get('sylius.order_processing.order_processor')->process($order);

Promotion Coupon Generator
--------------------------

Making up new codes might become difficult if you would like to prepare a lot of coupons at once. That is why Sylius
provides a service that generates random codes for you - `CouponGenerator <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Component/Promotion/Generator/CouponGenerator.php>`_.
In its **PromotionCouponGeneratorInstruction** you can define the amount of coupons that will be generated, length of their codes, expiration date and usage limit.

.. code-block:: php

   // Find a promotion you desire in the repository
   $promotion = $this->container->get('sylius.repository.promotion')->findOneBy(['code' => 'simple_promotion']);

   // Get the CouponGenerator service
   /** @var CouponGeneratorInterface $generator */
   $generator = $this->container->get('sylius.promotion_coupon_generator');

   // Then create a new empty PromotionCouponGeneratorInstruction
   /** @var PromotionCouponGeneratorInstructionInterface $instruction */
   $instruction = new PromotionCouponGeneratorInstruction();

   // By default the instruction will generate 5 coupons with codes of length equal to 6
   // You can easily change it with the ``setAmount()`` and ``setLength()`` methods
   $instruction->setAmount(10);

   // Now use the ``generate()`` method with your instruction on the promotion where you want to have Coupons
   $generator->generate($promotion, $instruction);

The above piece of code will result in a set of 10 coupons that will work with the promotion identified by the ``simple_promotion`` code.

Learn more
----------

* :doc:`Promotions Concept Documentation </book/orders/promotions>`
* :doc:`promotion - Component Documentation </components/Promotion/index>`
* :doc:`promotion - Bundle Documentation </bundles/SyliusPromotionBundle/index>`
