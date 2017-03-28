State Machine
=============

.. _component_payment_payment-states:

Payment States
--------------

The following payment states are available by default:

+------------------+------------+---------------------------------------------------------+
| Related constant | State      | Description                                             |
+==================+============+=========================================================+
| STATE_CART       | cart       | Initial; Before the subject of payment is completed     |
+------------------+------------+---------------------------------------------------------+
| STATE_NEW        | new        | After completion of the payment subject                 |
+------------------+------------+---------------------------------------------------------+
| STATE_PROCESSING | processing | Payment which is in process of verification             |
+------------------+------------+---------------------------------------------------------+
| STATE_COMPLETED  | completed  | Completed payment                                       |
+------------------+------------+---------------------------------------------------------+
| STATE_FAILED     | failed     | Payment has failed                                      |
+------------------+------------+---------------------------------------------------------+
| STATE_CANCELLED  | cancelled  | Cancelled by a customer or manager                      |
+------------------+------------+---------------------------------------------------------+
| STATE_REFUNDED   | refunded   | A completed payment which has been refunded             |
+------------------+------------+---------------------------------------------------------+
| STATE_UNKNOWN    | unknown    | Auxiliary state for handling external states            |
+------------------+------------+---------------------------------------------------------+

.. note::

   All the above states are constant fields in the :ref:`component_payment_model_payment-interface`.

.. _component_payment_payment-transitions:

Payment Transitions
-------------------

The following payment transitions are available by default:

+------------------+------------+
| Related constant | Transition |
+==================+============+
| SYLIUS_CREATE    | create     |
+------------------+------------+
| SYLIUS_PROCESS   | process    |
+------------------+------------+
| SYLIUS_COMPLETE  | complete   |
+------------------+------------+
| SYLIUS_FAIL      | fail       |
+------------------+------------+
| SYLIUS_CANCEL    | cancel     |
+------------------+------------+
| SYLIUS_REFUND    | refund     |
+------------------+------------+

There's also the default graph name included:

+------------------+----------------+
| Related constant | Name           |
+==================+================+
| GRAPH            | sylius_payment |
+------------------+----------------+

.. note::
   All of above transitions and the graph are constant fields in the **PaymentTransitions** class.
