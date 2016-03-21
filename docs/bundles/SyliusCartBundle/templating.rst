In templates
============

When using Twig as your template engine, you have access to 2 handy functions.

The ``sylius_cart_get`` function uses the provider to get the current cart.

.. code-block:: jinja

    {% set cart = sylius_cart_get() %}

    Current cart totals: {{ cart.total }} for {{ cart.totalItems }} items!

The ``sylius_cart_form`` returns the form view for the CartItem form. It allows you to easily build more complex actions for
adding items to cart. In this simple example we allow to provide the quantity of item. You'll need to process this form in your resolver.

.. code-block:: jinja

    {% set form = sylius_cart_form({'product': product}) %} {# You can pass options as an argument. #}

    <form action="{{ path('sylius_cart_item_add', {'productId': product.id}) }}" method="post">
        {{ form_row(form.quantity)}}
        {{ form_widget(form._token) }}
        <input type="submit" value="Add to cart">
    </form>

.. note::

     An example with multiple variants of this form `can be found in Sylius Sandbox app <https://github.com/Sylius/Sylius-Sandbox/blob/master/src/Sylius/Bundle/SandboxBundle/Form/Type/CartItemType.php>`_.
     It allows for selecting a variation/options/quantity of the product. It also adapts to the product type.
