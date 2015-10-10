Models
======

.. _component_cart_model_cart:

Cart
----

The cart is represented by **Cart** instance. It inherits all the properties from :ref:`component_order_model_order` and add one property:

+-----------------+-------------------------------------+------------+
| Method          | Description                         | Type       |
+=================+=====================================+============+
| expiresAt       | Expiration time                     | \DateTime  |
+-----------------+-------------------------------------+------------+

.. note::

    This model implements the :ref:`component_cart_model_cart-interface`

.. _component_cart_model_cart-item:

CartItem
--------

The items of the cart are represented by the **CartItem** instances. **CartItem** has no properties but it inherits from :ref:`component_order_model_order-item`.

.. note::

    This model implements the :ref:`component_cart_model_cart-item-interface`