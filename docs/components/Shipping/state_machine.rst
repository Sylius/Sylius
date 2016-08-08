State Machine
=============

Shipment States
---------------

Sylius itself uses a state machine system to manage all states of the business domain. This component has some
sensible default states defined in **ShipmentInterface**.

All new **Shipment** instances have the state ``ready`` by default, which means they are prepared to be sent.

The following states are defined:

+-------------------+-------------+-----------------------------------------------------------------+
| Related constant  | State       | Description                                                     |
+===================+=============+=================================================================+
| STATE_READY       | ready       | Payment received, shipment has been ready to be sent            |
+-------------------+-------------+-----------------------------------------------------------------+
| STATE_CHECKOUT    | checkout    | Shipment has been created                                       |
+-------------------+-------------+-----------------------------------------------------------------+
| STATE_ONHOLD      | onhold      | Shipment has been locked and it has been waiting to payment     |
+-------------------+-------------+-----------------------------------------------------------------+
| STATE_PENDING     | pending     | Shipment has been waiting for confirmation of receiving payment |
+-------------------+-------------+-----------------------------------------------------------------+
| STATE_SHIPPED     | shipped     | Shipment has been sent to the customer                          |
+-------------------+-------------+-----------------------------------------------------------------+
| STATE_CANCELLED   | cancelled   | Shipment has been cancelled                                     |
+-------------------+-------------+-----------------------------------------------------------------+
| STATE_RETURNED    | returned    | Shipment has been returned                                      |
+-------------------+-------------+-----------------------------------------------------------------+
