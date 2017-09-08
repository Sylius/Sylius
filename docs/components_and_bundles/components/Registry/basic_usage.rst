Basic Usage
===========

A registry object acts as a collection of objects. The sylius **ServiceRegistry**
allows you to store objects which implement a specific interface.

.. _component_registry_service-registry:

ServiceRegistry
---------------

To create a new **ServiceRegistry** you need to
determine what kind of interface should be kept inside.

For the sake of examples lets use the :ref:`component_promotion_checker_promotion-rule-checker-interface`
from the :doc:`Promotion </components_and_bundles/components/Promotion/index>` component.

.. code-block:: php

   <?php

   use Sylius\Component\Registry\ServiceRegistry;

   $registry = new ServiceRegistry('Sylius\Component\Promotion\Checker\RuleCheckerInterface');

Once you've done that you can manage any object with the corresponding interface.

So for starters, lets add some services:

.. code-block:: php

   <?php

   use Sylius\Component\Promotion\Checker\Rule\ItemTotalRuleChecker;
   use Sylius\Component\Promotion\Checker\Rule\CartQuantityRuleChecker;

   $registry->register('item_total', new ItemTotalRuleChecker());
   $registry->register('cart_quantity', new CartQuantityRuleChecker());

.. hint::
   The first parameter of ``register`` is incredibly important, as we will use it for all further operations.
   Also it's the key at which our service is stored in the array returned by ``all`` method.

After specifying the interface and inserting services, we can manage them:

.. code-block:: php

   <?php

   $registry->has('item_total'); // returns true

   $registry->get('item_total'); // returns the ItemTotalRuleChecker we inserted earlier on

   $registry->all(); // returns an array containing both rule checkers

Removing a service from the registry is as easy as adding:

.. code-block:: php

   <?php

   $registry->unregister('item_total');

   $registry->has('item_total'); // now returns false

.. note::
   This service implements the :ref:`component_registry_service-registry-interface`.

.. caution::
   This service throws:

   * `\\InvalidArgumentException`_ when you try to ``register`` a service which doesn't implement the specified interface
   * :ref:`component_registry_existing-service-exception`
   * :ref:`component_registry_non-existing-service-exception`

.. _\\InvalidArgumentException: http://php.net/manual/en/class.invalidargumentexception.php
