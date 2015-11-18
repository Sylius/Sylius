Basic Usage
===========

.. note::
    The cart is basically an order with an appropriate state.
    Check Order's :doc:`/components/Order/state_machine`.

.. hint::
    For more examples go to Order :doc:`/components/Order/basic_usage`.

CartContext
-----------

The **CartContext** provides you with useful tools to
set and retrieve current cart identifier based on storage.

.. code-block:: php

    <?php

    use Sylius\Component\Cart\Context\CartContext;
    use Sylius\Component\Cart;

    $context = new CartContext();
    $cart = new Cart();

    $currentCartIdentifier = $context->getCurrentCartIdentifier();
    $context->setCurrentCartIdentifier($cart);
    $context->resetCurrentCartIdentifier();