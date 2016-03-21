.. index::
   single: Orders

Orders
======

*Order* model is one of the most important in Sylius, it represents the order placed via your store! It has a very consistent and clear API, which allows you to easily manipulate and process orders.

Customer Reference
------------------

*Order* holds a reference to specific *User*, which is available through ``getUser()`` method:

.. code-block:: php

    echo $order->getUser()->getEmail(); // john@example.com

When creating order programatically, you can define the user yourself:

.. code-block:: php

    $order = $this->get('sylius.repository.order')->createNew();
    $john = $this->get('sylius.repository.user')->find(3);

    $order->setUser($john);

*Order* may not have reference to *User* in case when *Order* was created by guest.

Billing and Shipping Address
----------------------------

By default, every order has its own *billing* and *shipping* address, which are heavily used through whole process. Both of them are represented by *Address* model.

.. code-block:: php

    $shippingAddress = $order->getShippingAddress();

    echo 'Shipped to: '.$shippingAddress->getCountry();

Order Contents
--------------

*Order* holds a collection of  *OrderItem* instances.

**OrderItem** model has the attributes listed below:

+------------------+-----------------------------+
| Attribute        | Description                 |
+==================+=============================+
| id               | Unique id of the item       |
+------------------+-----------------------------+
| order            | Reference to an Order       |
+------------------+-----------------------------+
| variant          | Reference to Variant        |
+------------------+-----------------------------+
| product          | Product loaded via Variant  |
+------------------+-----------------------------+
| unitPrice        | The price of a single unit  |
+------------------+-----------------------------+
| quantity         | Quantity of sold item       |
+------------------+-----------------------------+
| adjustments      | Collection of Adjustments   |
+------------------+-----------------------------+
| adjustmentsTotal | Total value of adjustments  |
+------------------+-----------------------------+
| total            | Order grand total           |
+------------------+-----------------------------+
| createdAt        | Date when order was created |
+------------------+-----------------------------+
| updatedAt        | Date of last change         |
+------------------+-----------------------------+

Taxes and Shipping Fees as Adjustments
--------------------------------------

...

Shipments
---------

...

Shipping State
~~~~~~~~~~~~~~

...

Payments
--------

...

Payment State
~~~~~~~~~~~~~

...

The Order State Machine
-----------------------

Order has also its general state, which can have the following values:

* cart
* pending
* released
* confirmed
* shipped
* abandoned
* cancelled
* returned

Final Thoughts
--------------

...

Learn more
----------

* ...
