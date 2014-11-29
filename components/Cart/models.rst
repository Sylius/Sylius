Models
======

Cart
----

The cart is represented by **Cart** instance. It inherits all the properties from ``Sylius\Component\Order\Model\Order`` and has following property:

+-----------------+-------------------------------------+------------+
| Method          | Description                         | Type       |
+=================+=====================================+============+
| expiresAt       | Expiration time                     | \DateTime  |
+-----------------+-------------------------------------+------------+

.. note::

    This model implements ``CartInterface``

CartItem
--------

The items of the cart are represented by **CartItem** instance and it has not property but it inherits all of them from ``Sylius\Component\Order\Model\OrderItem``

.. note::

    This model implements ``CartItemInterface``