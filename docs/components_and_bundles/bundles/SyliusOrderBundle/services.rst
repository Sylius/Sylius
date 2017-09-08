Using the services
==================

When using the bundle, you have access to several handy services.
You can use them to retrieve and persist orders.

Managers and Repositories
-------------------------

.. note::

    Sylius uses ``Doctrine\Common\Persistence`` interfaces.

You have access to following services which are used to manage and retrieve resources.

This set of default services is shared across almost all Sylius bundles, but this is just a convention.
You're interacting with them like you usually do with own entities in your project.

.. code-block:: php

    <?php

    // ObjectManager which is capable of managing the resources.
    // For *doctrine/orm* driver it will be EntityManager.
    $this->get('sylius.manager.order');
    $this->get('sylius.manager.order_item');
    $this->get('sylius.manager.order_item_unit');
    $this->get('sylius.manager.adjustment');

    // ObjectRepository for the Order resource, it extends the base EntityRepository.
    // You can use it like usual entity repository in project.
    $this->get('sylius.repository.order');
    $this->get('sylius.repository.order_item');
    $this->get('sylius.repository.order_item_unit');
    $this->get('sylius.repository.adjustment');

    // Those repositories have some handy default methods, for example...
    $item = $itemRepository->createNew();
    $orderRepository->find(4);
    $paginator = $orderRepository->createPaginator(array('confirmed' => false)); // Get Pagerfanta instance for all unconfirmed orders.


.. _bundle_order_order-item-quantity-modifier:

OrderItemQuantityModifier
-------------------------

``OrderItemQuantityModifier`` should be used to modify ``OrderItem`` quantity, because of whole background units' logic,
that needs to be done. This service handles this task, adding and removing proper amounts of units to ``OrderItem``.

.. code-block:: php

   <?php

   $orderItemFactory = $this->get('sylius.factory.order_item');
   $orderItemQuantityModifier = $this->get('sylius.order_item_quantity_modifier');

   $orderItem = $orderItemFactory->createNew();
   $orderItem->getQuantity(); // default quantity of order item is "0"

   $orderItem->setUnitPrice(1000);

   $orderItemQuantityModifier->modify($orderItem, 4);

   $orderItem->getQuantity(); // after using modifier, quantity is as expected
   $orderItem->getTotal();    // item's total is sum of all units' total (units has been created by modifier)

   $orderItemQuantityModifier->modify($orderItem, 2);

   // OrderItemQuantityModifier can also reduce item's quantity and remove unnecessary units

   $orderItem->getQuantity(); // "2"
   $orderItem->getTotal();    // 4000
