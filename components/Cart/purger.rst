Cart purger
===========

A cart purger purges all expired carts. To characterize an object which is a **Purger**, it needs to implement the ``PurgerInterface`` and the following method:

+---------------+---------------------------------------------+----------------+
| Method        | Description                                 | Returned value |
+===============+=============================================+================+
| purge()       | Purge all expired carts                     | boolean        |
+---------------+---------------------------------------------+----------------+