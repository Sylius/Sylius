.. index::
   single: Promotions

Promotions
==========

The system of **Promotions** in **Sylius** is really flexible. It is a combination of promotion rules and actions.

Promotions have a few parameters - a unique ``code``, ``name``, ``usageLimit``,
the period of time when it works.
There is a possibility to define **exclusive promotions** (no other can be applied if an exclusive promotion was applied)
and **priority** that is useful for them, because the exclusive promotion should get the top priority.

.. tip::

   The ``usageLimit`` of a promotion is the **total number of times this promotion can be used**.

How to create a Promotion programmatically?
-------------------------------------------

Just as usually, use a factory. The promotion needs a ``code`` and a ``name``.

.. code-block:: php

   /** @var PromotionInterface $promotion */
   $promotion = $this->container->get('sylius.factory.promotion')->createNew();

   $promotion->setCode('simple_promotion_1');
   $promotion->setName('Simple Promotion');

**Of course an empty promotion would be useless** - it is just a base for adding **Rules** and **Actions**.
Let's see how to make it functional.

Promotion Rules
---------------

The promotion **Rules** restrict in what circumstances a promotion will be applied.
An appropriate **RuleChecker** (each Rule type has its own RuleChecker) may check if the Order:

* Contains a number of items from a specified taxon (for example: *contains 4 products that are categorized as t-shirts*)
* Has a specified total price of items from a given taxon (for example: *all mugs in the order cost 20$ in total*)
* Has total price of at least a defined value (for example: *the orders' items total price is equal at least 50$*)

And many more similar, suitable to your needs.

Rule Types
''''''''''

The types of rules that are configured in **Sylius** by default are:

* **Cart Quantity** - checks if there is a given amount of items in the cart,
* **Item Total** - checks if items in the cart cost a given amount of money,
* **Taxon** - checks if there is at least one item from given taxons in the cart,
* **Items From Taxon Total** - checks in the cart if items from a given taxon cost a given amount of money,
* **Nth Order** - checks if this is for example the second order made by the customer,
* **Shipping Country** - checks if the order's shipping address is in a given country.

How to create a new PromotionRule programmatically?
'''''''''''''''''''''''''''''''''''''''''''''''''''

Creating a **PromotionRule** is really simple since we have the `PromotionRuleFactory <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Component/Core/Factory/PromotionRuleFactory.php>`_.
It has dedicated methods for creating all types of rules available by default.

In the example you can see how to create a simple Cart Quantity rule. It will check if there are at least 5 items in the cart.

.. code-block:: php

   /** @var PromotionRuleFactoryInterface $ruleFactory */
   $ruleFactory = $this->container->get('sylius.factory.promotion_rule');

   $quantityRule = $ruleFactory->createCartQuantity('5');

   // add your rule to the previously created Promotion
   $promotion->addRule($quantityRule);

.. note::

   **Rules** are just constraints that have to be fulfilled by an order to make the promotion **eligible**.
   To make something happen to the order you will need **Actions**.

PromotionRules configuration reference
''''''''''''''''''''''''''''''''''''''

Each PromotionRule type has a very specific structure of its configuration array:

+-------------------------------+--------------------------------------------------------------------+
| PromotionRule type            | Rule Configuration Array                                           |
+===============================+====================================================================+
| ``cart_quantity``             | ``['count' => $count]``                                            |
+-------------------------------+--------------------------------------------------------------------+
| ``item_total``                | ``[$channelCode => ['amount' => $amount]]``                        |
+-------------------------------+--------------------------------------------------------------------+
| ``has_taxon``                 | ``['taxons' => $taxons]``                                          |
+-------------------------------+--------------------------------------------------------------------+
| ``total_of_items_from_taxon`` | ``[$channelCode => ['taxon' => $taxonCode, 'amount' => $amount]]`` |
+-------------------------------+--------------------------------------------------------------------+
| ``nth_order``                 | ``['nth' => $nth]``                                                |
+-------------------------------+--------------------------------------------------------------------+
| ``contains_product``          | ``['product_code' => $productCode]``                               |
+-------------------------------+--------------------------------------------------------------------+

Promotion Actions
-----------------

What happens with the Order when the rules of a Promotion are fulfilled - this is an **PromotionAction**.

There are a few kinds of actions in **Sylius**:

* fixed discount on the order (for example: -5$ discount)
* percentage discount on the order (for example: -10% on the whole order)
* fixed unit discount (for example: -1$ for one specific Mug)
* percentage unit discount (for example: -10% on one specific T-Shirt)
* add product (for example: gives a free bonus sticker)
* shipping discount (for example: - 6$ on the costs of shipping)

How to create an PromotionAction programmatically?
''''''''''''''''''''''''''''''''''''''''''''''''''

In order to create a new PromotionAction we can use the dedicated `PromotionActionFactory <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Component/Core/Factory/PromotionActionFactory.php>`_.

It has special methods for creating all types of actions available by default.
In the example below you can how to create a simple Fixed Discount action, that reduces the total of an order by 10$.

.. code-block:: php

   /** @var PromotionActionFactoryInterface $actionFactory */
   $actionFactory = $this->container->get('sylius.factory.promotion_action');

   $action = $actionFactory->createFixedDiscount(10);

   // add your action to the previously created Promotion
   $promotion->addAction($action);

.. note::

   All **Actions** are assigned to a Promotion and are executed while the Promotion is applied.
   This happens via the `CompositeOrderProcessor <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Component/Core/OrderProcessing/CompositeOrderProcessor.php>`_ service.
   See details of **applying Promotions** below.

And finally after you have an **PromotionAction** and a **PromotionRule** assigned to the **Promotion** add it to the repository.

.. code-block:: php

   $this->container->get('sylius.repository.promotion')->add($promotion);

PromotionActions configuration reference
''''''''''''''''''''''''''''''''''''''''

Each PromotionAction type has a very specific structure of its configuration array:

+----------------------------------+-----------------------------------------------------+
| PromotionAction type             | Action Configuration Array                          |
+==================================+=====================================================+
| ``order_fixed_discount``         | ``[$channelCode => ['amount' => $amount]]``         |
+----------------------------------+-----------------------------------------------------+
| ``unit_fixed_discount``          | ``[$channelCode => ['amount' => $amount]]``         |
+----------------------------------+-----------------------------------------------------+
| ``order_percentage_discount``    | ``['percentage' => $percentage]``                   |
+----------------------------------+-----------------------------------------------------+
| ``unit_percentage_discount``     | ``[$channelCode => ['percentage' => $percentage]]`` |
+----------------------------------+-----------------------------------------------------+
| ``shipping_percentage_discount`` | ``['percentage' => $percentage]``                   |
+----------------------------------+-----------------------------------------------------+

Applying Promotions
-------------------

Promotions in Sylius are handled by the `PromotionProcessor <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Component/Promotion/Processor/PromotionProcessor.php>`_
which inside uses the `PromotionApplicator <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Component/Promotion/Action/PromotionApplicator.php>`_.

The **PromotionProcessor**'s method ``process()`` is executed on the subject of promotions - an Order:

* firstly it iterates over the promotions of a given Order and first **reverts** them all,
* then it checks the eligibility of all promotions available in the system on the given Order
* and finally it applies all the eligible promotions to that order.

How to apply a Promotion manually?
''''''''''''''''''''''''''''''''''

Let's assume that you would like to **apply a 10% discount on everything** somewhere in your code.

To achieve that, create a Promotion with an PromotionAction that gives 10% discount. You don't need rules.

.. code-block:: php

   /** @var PromotionInterface $promotion */
   $promotion = $this->container->get('sylius.factory.promotion')->createNew();

   $promotion->setCode('discount_10%');
   $promotion->setName('10% discount');

   /** @var PromotionActionFactoryInterface $actionFactory */
   $actionFactory = $this->container->get('sylius.factory.promotion_action');

   $action = $actionFactory->createPercentageDiscount(10);

   $promotion->addAction($action);

   $this->container->get('sylius.repository.promotion')->add($promotion);

   // and now get the PromotionApplicator and use it on an Order (assuming that you have one)
   $this->container->get('sylius.promotion_applicator')->apply($order, $promotion);

Promotion Filters
-----------------

Filters are really handy when you want to apply promotion's actions to groups of products in an Order.
For example if you would like to apply actions only on products from a desired taxon - use the available by default
`TaxonFilter <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Component/Core/Promotion/Filter/TaxonFilter.php>`_.

Learn more
----------

* :doc:`Promotion - Component Documentation </components/Promotion/index>`
* :doc:`Promotion - Bundle Documentation </bundles/SyliusPromotionBundle/index>`
