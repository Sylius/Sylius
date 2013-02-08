Routing and default actions
===========================

Bundle provides quite simple default routing with several handy and common actions.
You can see usage guide below.

Cart summary page
-----------------

To point user to the cart summary page, you can use the ``sylius_cart_show`` route.
It will render the page with the `cart` and `form` variables by default.

The `cart` is the current cart and `form` is the view of cart form.

Adding cart item
----------------

In our simple example, we only need to add following link in the places where we need the "add to cart button".

.. code-block:: html

    <a href="{{ path('sylius_cart_item_add', {'productId': product.id}) }}">Add product to cart</a>

Clicking this link will add the selected product to cart.

Removing item
-------------

On cart summary page you have access to all cart items, so another simple link will allow user to remove items from cart.

.. code-block:: html

    <a href="{{ path('sylius_cart_item_remove', {'id': item.id}) }}">Remove from cart</a>

Where `item` variable represents one of `cart.items` collection item.

Clearing the whole cart
-----------------------

Clearing the cart is simple as clicking the following link.

.. code-block:: html

    <a href="{{ path('sylius_cart_clear')}}">Clear cart</a>

Basic cart update
-----------------

On cart summary page, you have access to the cart form, if you want to save it, simply submit the form
with following action.

.. code-block:: html

    <form action="{{ path('sylius_cart_save') }}" method="post">Clear cart</a>

You cart will be validated and saved if everything is alright.
