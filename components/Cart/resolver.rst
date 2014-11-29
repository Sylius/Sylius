Cart resolver
=============

A cart resolver returns cart item that needs to be added based on given data. To characterize an object which is a **Resolver**,
it needs to implement the ``ItemResolverInterface`` and the following method:

+-----------------------------------------+-------------------------------------------------+----------------------------+
| Method                                  | Description                                     | Returned value             |
+=========================================+=================================================+============================+
| resolve(CartItemInterface $item, $data) | Returns item that will be added into the cart   | CartItemInterface          |
+-----------------------------------------+-------------------------------------------------+----------------------------+

.. note::

    This method throw ``ItemResolvingException`` if an error occurs