State Machine
=============

Order States
------------

Sylius itself uses a complex state machine system to manage all states of the business domain.
This component has some sensible default states defined in the **OrderInterface**.

All new **Order** instances have the state ``cart`` by default, which means they are unconfirmed.

The following states are defined:

+-------------------+-------------+-----------------------------------------------+
| Related constant  | State       | Description                                   |
+===================+=============+===============================================+
| STATE_CART        | cart        | Unconfirmed order, ready to add/remove items  |
+-------------------+-------------+-----------------------------------------------+
| STATE_CART_LOCKED | cart_locked | Cart which should not be removed when expired |
+-------------------+-------------+-----------------------------------------------+
| STATE_PENDING     | pending     | Order waiting for confirmation                |
+-------------------+-------------+-----------------------------------------------+
| STATE_CONFIRMED   | confirmed   | Confirmed and completed order                 |
+-------------------+-------------+-----------------------------------------------+
| STATE_SHIPPED     | shipped     | Order has been shipped to the customer        |
+-------------------+-------------+-----------------------------------------------+
| STATE_ABANDONED   | abandoned   | Been waiting too long for confirmation        |
+-------------------+-------------+-----------------------------------------------+
| STATE_CANCELLED   | cancelled   | Cancelled by customer or manager              |
+-------------------+-------------+-----------------------------------------------+
| STATE_RETURNED    | returned    | Order has been returned and refunded          |
+-------------------+-------------+-----------------------------------------------+

.. tip::
    Please keep in mind that these states are just default, you can define and use your own.
    If you use this component with :doc:`/bundles/SyliusOrderBundle/index` and Symfony2, you will have full state machine configuration at your disposal.

.. _component_order_order-transitions:

Order Transitions
-----------------

There are following order's transitions by default:

+------------------+------------+
| Related constant | Transition |
+==================+============+
| SYLIUS_CREATE    | create     |
+------------------+------------+
| SYLIUS_RELEASE   | release    |
+------------------+------------+
| SYLIUS_CONFIRM   | confirm    |
+------------------+------------+
| SYLIUS_SHIP      | ship       |
+------------------+------------+
| SYLIUS_ABANDON   | abandon    |
+------------------+------------+
| SYLIUS_CANCEL    | cancel     |
+------------------+------------+
| SYLIUS_RETURN    | return     |
+------------------+------------+

There is also the default graph name included:

+------------------+--------------+
| Related constant | Name         |
+==================+==============+
| GRAPH            | sylius_order |
+------------------+--------------+

.. note::
    All of above transitions and the graph are constant fields in the **OrderTransitions** class.
