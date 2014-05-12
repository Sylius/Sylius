Order States
============

Sylius itself uses a complex state machine system to manage all states of the business domain. 
This component has some sensible default states defined in the **OrderInterface**.

Default States
--------------

All new **Order** instances have the state ``cart`` by default, which means they are unconfirmed.

The following states are defined:

+-------------------+------------------------------------------------+
| State             | Description                                    |
+===================+================================================+
| cart              | Unconfirmed order, ready to add/remove items   |
+-------------------+------------------------------------------------+
| cart_locked       | Cart which should not be removed when expired  |
+-------------------+------------------------------------------------+
| pending           | Order waiting for confirmation                 |
+-------------------+------------------------------------------------+
| confirmed         | Confirmed and completed order                  |
+-------------------+------------------------------------------------+
| shipped           | Order has been shipped to the customer         |
+-------------------+------------------------------------------------+
| abandoned         | Been waiting too long for confirmation         |
+-------------------+------------------------------------------------+
| cancelled         | Cancelled by customer or manager               |
+-------------------+------------------------------------------------+
| returned          | Order has been returned and refunded           |
+-------------------+------------------------------------------------+

.. note::

    Please keep in mind that these states are just default, you can define and use your own.
    If you use this component with **SyliusOrderBundle** and Symfony2, you will have full state machine configuration at your disposal.
