Using the services
==================

When using the bundle, you have access to several handy services.
You can use them to manipulate and manage the cart.

Managers and Repositories
-------------------------

.. note::

    Sylius uses ``Doctrine\Common\Persistence`` interfaces.

You have access to following services which are used to manage and retrieve resources.

This set of default services is shared across almost all Sylius bundles, but this is just a convention.
You're interacting with them like you usually do with own entities in your project.

.. code-block:: php

    <?php

    // ...
    public function saveAction(Request $request)
    {
        // ObjectManager which is capable of managing the Cart resource.
        // For *doctrine/orm* driver it will be EntityManager.
        $this->get('sylius_cart.manager.cart'); 

        // ObjectRepository for the Cart resource, it extends the base EntityRepository.
        // You can use it like usual entity repository in project.
        $this->get('sylius_cart.repository.cart'); 

        // Same pair for CartItem resource.
        $this->get('sylius_cart.manager.item');
        $this->get('sylius_cart.repository.item');

        // Those repositories have some handy default methods, for example...
        $item = $itemRepository->createNew();
    }

Provider and Resolver
-------------------------------

There are also 3 more services for you.

You use provider to obtain the current user cart, if there is none, a new one is created and saved.
The ``->setCart()`` method also allows you to replace the current cart.
``->abandonCart()`` is resetting the current cart, a new one will be created on next ``->getCart()`` call.
This is useful, for example, when after completing an order you want to start with a brand new and clean cart.

.. code-block:: php

    <?php

    // ...
    public function saveAction(Request $request)
    {
        $provider = $this->get('sylius_cart.provider'); // Implements the CartProviderInterface.

        $currentCart = $provider->getCart();
        $provider->setCart($customCart);
        $provider->abandonCart();
    }

The resolver is used to create a new item based on user request.

.. code-block:: php

    <?php

    // ...
    public function addItemAction(Request $request)
    {
        $resolver = $this->get('sylius_cart.resolver');
        $item = $this->resolve($this->createNew(), $request);
    }

.. note::

    A more advanced example of resolver implementation is available `in Sylius Sandbox application on GitHub <https://github.com/Sylius/Sylius-Sandbox/blob/master/src/Sylius/Bundle/SandboxBundle/Resolver/ItemResolver.php>`_.
