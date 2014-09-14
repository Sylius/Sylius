Cart provider
=============

A cart provider retrieves existing cart or create new one based on storage. To characterize an object which is a **Provider**,
it needs to implement the ``CartProviderInterface`` and the following method:

+------------------------------+-------------------------------------------------------------------------------------+----------------+
| Method                       | Description                                                                         | Returned value |
+==============================+=====================================================================================+================+
| hasCart()                    | Check if the the cart exists                                                        | boolean        |
+------------------------------+-------------------------------------------------------------------------------------+----------------+
| getCart()                    | Get current cart. If none found is by storage, it should create new one and save it | CartInterface  |
+------------------------------+-------------------------------------------------------------------------------------+----------------+
| setCart(CartInterface $cart) | Sets given cart as current one                                                      | Void           |
+------------------------------+-------------------------------------------------------------------------------------+----------------+
| abandonCart()                | Abandon current cart                                                                | Void           |
+------------------------------+-------------------------------------------------------------------------------------+----------------+