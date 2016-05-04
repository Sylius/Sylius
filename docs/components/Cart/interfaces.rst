Interfaces
==========

Model Interfaces
----------------

.. _component_cart_model_cart-interface:

CartInterface
~~~~~~~~~~~~~

This interface should be implemented by model representing a single Cart.

.. note::
    This interface extends the :ref:`component_order_model_order-interface`

    For more detailed information go to `Sylius API CartInterface`_.

.. _Sylius API CartInterface: http://api.sylius.org/Sylius/Component/Cart/Model/CartInterface.html

.. _component_cart_model_cart-item-interface:

CartItemInterface
~~~~~~~~~~~~~~~~~

This interface should be implemented by model representing a single CartItem.

.. note::
    This interface extends the :ref:`component_order_model_order-item-interface`

    For more detailed information go to `Sylius API CartItemInterface`_.

.. _Sylius API CartItemInterface: http://api.sylius.org/Sylius/Component/Cart/Model/CartItemInterface.html

Service Interfaces
------------------

.. _component_cart_provider_cart-provider-interface:

CartProviderInterface
~~~~~~~~~~~~~~~~~~~~~

A cart provider retrieves existing cart or create new one based on the storage. To characterize an object which is a **Provider**,
it needs to implement the ``CartProviderInterface``.

.. note::
    For more detailed information go to `Sylius API CartProviderInterface`_.

.. _Sylius API CartProviderInterface: http://api.sylius.org/Sylius/Component/Cart/Provider/CartProviderInterface.html

.. _component_cart_purger_purger-interface:

PurgerInterface
~~~~~~~~~~~~~~~

A cart purger purges all expired carts. To characterize an object which is a **Purger**, it needs to implement the ``PurgerInterface``.

.. note::
    For more detailed information go to `Sylius API PurgerInterface`_.

.. _Sylius API PurgerInterface: http://api.sylius.org/Sylius/Component/Cart/Purger/PurgerInterface.html

.. _component_cart_resolver_item-resolver-interface:

ItemResolverInterface
~~~~~~~~~~~~~~~~~~~~~

A cart resolver returns cart item that needs to be added based on given data. To characterize an object which is a **Resolver**,
it needs to implement the ``ItemResolverInterface``.

.. note::
    For more detailed information go to `Sylius API ItemResolverInterface`_.

.. _Sylius API ItemResolverInterface: http://api.sylius.org/Sylius/Component/Cart/Resolver/ItemResolverInterface.html

.. caution::
    This method throws `ItemResolvingException`_ if an error occurs.

.. _ItemResolvingException: http://api.sylius.org/Sylius/Component/Cart/Resolver/ItemResolvingException.html

.. _component_cart_repository_cart-repository-interface:

CartRepositoryInterface
~~~~~~~~~~~~~~~~~~~~~~~

In order to decouple from storage that provides expired carts, you should create repository class which implements this interface.

.. note::
    This interface extends the :ref:`component_order_repository_order-repository-interface`

    For more detailed information go to `Sylius API CartRepositoryInterface`_.

.. _Sylius API CartRepositoryInterface: http://api.sylius.org/Sylius/Component/Cart/Repository/CartRepositoryInterface.html

CartContextInterface
~~~~~~~~~~~~~~~~~~~~

This interface is implemented by the services responsible for setting and retrieving current cart identifier based on storage.
To characterize an object which is a **CartContext** it needs to implement the ``CartContextInterface``

.. note::
    For more detailed information go to `Sylius API CartContextInterface`_.

.. _Sylius API CartContextInterface: http://api.sylius.org/Sylius/Component/Cart/Context/CartContextInterface.html

