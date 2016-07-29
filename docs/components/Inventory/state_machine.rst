State Machine
=============

Inventory Unit States
---------------------

Sylius itself uses a complex state machine system to manage all states of the business domain.
This component has some sensible default states defined in the **InventoryUnitInterface**.

All new **InventoryUnit** instances have the state ``checkout`` by default, which means they are in the cart and wait for verification.

The following states are defined:

+-------------------+-------------+-----------------------------------------------------------------+
| Related constant  | State       | Description                                                     |
+===================+=============+=================================================================+
| STATE_CHECKOUT    | checkout    | Item is in the cart                                             |
+-------------------+-------------+-----------------------------------------------------------------+
| STATE_ONHOLD      | onhold      | Item is hold (e.g. waiting for the payment)                     |
+-------------------+-------------+-----------------------------------------------------------------+
| STATE_SOLD        | sold        | Item has been sold and is no longer in the warehouse            |
+-------------------+-------------+-----------------------------------------------------------------+
| STATE_RETURNED    | returned    | Item has been sold, but returned and is in stock                |
+-------------------+-------------+-----------------------------------------------------------------+

.. tip::
    Please keep in mind that these states are just default, you can define and use your own.
    If you use this component with :doc:`/bundles/SyliusInventoryBundle/index` and Symfony, you will have full state machine configuration at your disposal.

.. _component_inventory_inventory-unit-transitions:

Inventory Unit Transitions
--------------------------

There are the following order's transitions by default:

+------------------+------------+
| Related constant | Transition |
+==================+============+
| SYLIUS_HOLD      | hold       |
+------------------+------------+
| SYLIUS_SELL      | sell       |
+------------------+------------+
| SYLIUS_RELEASE   | release    |
+------------------+------------+
| SYLIUS_RETURN    | return     |
+------------------+------------+

There is also the default graph name included:

+------------------+-----------------------+
| Related constant | Name                  |
+==================+=======================+
| GRAPH            | sylius_inventory_unit |
+------------------+-----------------------+

.. note::
    All of above transitions and the graph are constant fields in the **InventoryUnitTransitions** class.
