Cart storage
============

A cart storage stores current cart id. To characterize an object which is a **Storage**, it needs to implement the ``CartProviderInterface`` and the following method:

+-----------------------------------------------+-------------------------------------------------------------------+----------------+
| Method                                        | Description                                                       | Returned value |
+===============================================+===================================================================+================+
| getCurrentCartIdentifier()                    | Returns current cart id, used then to retrieve the cart           | mixed          |
+-----------------------------------------------+-------------------------------------------------------------------+----------------+
| setCurrentCartIdentifier(CartInterface $cart) | Sets current cart id and persists it                              | CartInterface  |
+-----------------------------------------------+-------------------------------------------------------------------+----------------+
| resetCurrentCartIdentifier()                  | Resets current cart identifier, it means abandoning current cart  | Void           |
+-----------------------------------------------+-------------------------------------------------------------------+----------------+
