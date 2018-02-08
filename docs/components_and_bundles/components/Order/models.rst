Models
======

.. _component_order_model_order:

Order
-----

**Order** object represents order.
Orders have the following properties:

+--------------------------+---------------------------------------------+
| Property                 | Description                                 |
+==========================+=============================================+
| id                       | Unique id of the order                      |
+--------------------------+---------------------------------------------+
| checkoutCompletedAt      | The time at which checkout was completed    |
+--------------------------+---------------------------------------------+
| number                   | Number is human-friendly identifier         |
+--------------------------+---------------------------------------------+
| notes                    | Additional information about order          |
+--------------------------+---------------------------------------------+
| items                    | Collection of items                         |
+--------------------------+---------------------------------------------+
| itemsTotal               | Total value of items in order (default 0)   |
+--------------------------+---------------------------------------------+
| adjustments              | Collection of adjustments                   |
+--------------------------+---------------------------------------------+
| adjustmentsTotal         | Total value of adjustments (default 0)      |
+--------------------------+---------------------------------------------+
| total                    | Calculated total (items + adjustments)      |
+--------------------------+---------------------------------------------+
| state                    | State of the order (e.g. "cart", "pending") |
+--------------------------+---------------------------------------------+
| createdAt                | Date when order was created                 |
+--------------------------+---------------------------------------------+
| updatedAt                | Date of last change                         |
+--------------------------+---------------------------------------------+

.. note::
    This model implements the :ref:`component_order_model_order-interface`
    For more detailed information go to `Sylius API Order`_.

.. _Sylius API Order: http://api.sylius.com/Sylius/Component/Order/Model/Order.html

.. _component_order_model_order-item:

OrderItem
---------

**OrderItem** object represents items in order.
OrderItems have the following properties:

+------------------+-----------------------------------------------------------------+
| Property         | Description                                                     |
+==================+=================================================================+
| id               | Unique id of the orderItem                                      |
+------------------+-----------------------------------------------------------------+
| order            | Reference to Order                                              |
+------------------+-----------------------------------------------------------------+
| quantity         | Items quantity                                                  |
+------------------+-----------------------------------------------------------------+
| unitPrice        | The price of a single unit                                      |
+------------------+-----------------------------------------------------------------+
| adjustments      | Collection of adjustments                                       |
+------------------+-----------------------------------------------------------------+
| adjustmentsTotal | Total of the adjustments in orderItem                           |
+------------------+-----------------------------------------------------------------+
| total            | Total of the orderItem (unitPrice * quantity + adjustmentsTotal)|
+------------------+-----------------------------------------------------------------+
| immutable        | Boolean flag of immutability                                    |
+------------------+-----------------------------------------------------------------+

.. note::
    This model implements the :ref:`component_order_model_order-item-interface`
    For more detailed information go to `Sylius API OrderItem`_.

.. _Sylius API OrderItem: http://api.sylius.com/Sylius/Component/Order/Model/OrderItem.html

.. _component_order_model_order-item-unit:

OrderItemUnit
-------------

**OrderItemUnit** object represents every single unit of order (for example ``OrderItem`` with quantity 5 should have 5 units).
OrderItemUnits have the following properties:

+------------------+--------------------------------------------------------------------+
| Property         | Description                                                        |
+==================+====================================================================+
| id               | Unique id of the orderItem                                         |
+------------------+--------------------------------------------------------------------+
| total            | Total of the orderItemUnit (orderItem unitPrice + adjustmentsTotal)|
+------------------+--------------------------------------------------------------------+
| orderItem        | Reference to OrderItem                                             |
+------------------+--------------------------------------------------------------------+
| adjustments      | Collection of adjustments                                          |
+------------------+--------------------------------------------------------------------+
| adjustmentsTotal | Total of the adjustments in orderItem                              |
+------------------+--------------------------------------------------------------------+

.. note::
    This model implements the :ref:`component_order_model_order-item-unit-interface`
    For more detailed information go to `Sylius API OrderItemUnit`_.

.. _Sylius API OrderItemUnit: http://api.sylius.com/Sylius/Component/Order/Model/OrderItem.html

.. _component_order_model_adjustment:

Adjustment
----------

**Adjustment** object represents an adjustment to the order's or order item's total.
Their amount can be positive (charges - taxes, shipping fees etc.) or negative (discounts etc.).
Adjustments have the following properties:

+-----------------+-----------------------------------------+
| Property        | Description                             |
+=================+=========================================+
| id              | Unique id of the adjustment             |
+-----------------+-----------------------------------------+
| order           | Reference to Order                      |
+-----------------+-----------------------------------------+
| orderItem       | Reference to OrderItem                  |
+-----------------+-----------------------------------------+
| orderItemUnit   | Reference to OrderItemUnit              |
+-----------------+-----------------------------------------+
| type            | Type of the adjustment (e.g. "tax")     |
+-----------------+-----------------------------------------+
| label           | e.g. "Clothing Tax 9%"                  |
+-----------------+-----------------------------------------+
| amount          | Adjustment amount                       |
+-----------------+-----------------------------------------+
| neutral         | Boolean flag of neutrality              |
+-----------------+-----------------------------------------+
| locked          | Adjustment lock (prevent from deletion) |
+-----------------+-----------------------------------------+
| originId        | Origin id of the adjustment             |
+-----------------+-----------------------------------------+
| originType      | Origin type of the adjustment           |
+-----------------+-----------------------------------------+
| createdAt       | Date when adjustment was created        |
+-----------------+-----------------------------------------+
| updatedAt       | Date of last change                     |
+-----------------+-----------------------------------------+

.. note::
    This model implements the :ref:`component_order_model_adjustment-interface`
    For more detailed information go to `Sylius API Adjustment`_.

.. _Sylius API Adjustment: http://api.sylius.com/Sylius/Component/Order/Model/Adjustment.html

