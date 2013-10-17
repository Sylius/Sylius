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
    $this->get('sylius.manager.adjustment');

    // ObjectRepository for the Order resource, it extends the base EntityRepository.
    // You can use it like usual entity repository in project.
    $this->get('sylius.repository.order');
    $this->get('sylius.repository.order_item');
    $this->get('sylius.repository.adjustment');

    // Those repositories have some handy default methods, for example...
    $item = $itemRepository->createNew();
    $orderRepository->find(4);
    $paginator = $orderRepository->createPaginator(array('confirmed' => false)); // Get Pagerfanta instance for all unconfirmed orders.
