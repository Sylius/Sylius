Models
======

CreditCard
----------

Every credit card is represented by **CreditCard** instance and has following properties:

+-----------------+--------------------------------------------+------------+
| Method          | Description                                | Type       |
+=================+============================================+============+
| token           | Payment gateway token                      | string     |
+-----------------+--------------------------------------------+------------+
| type            | Type of credit card (VISA, MasterCard...)  | string     |
+-----------------+--------------------------------------------+------------+
| cardholderName  | Cardholder name                            | string     |
+-----------------+--------------------------------------------+------------+
| number          | Card number                                | string     |
+-----------------+--------------------------------------------+------------+
| securityCode    | Security code                              | string     |
+-----------------+--------------------------------------------+------------+
| expiryMonth     | Expiry month                               | integer    |
+-----------------+--------------------------------------------+------------+
| expiryYear      | Expiry year                                | integer    |
+-----------------+--------------------------------------------+------------+
| createdAt       | Date of creation                           | \DateTime  |
+-----------------+--------------------------------------------+------------+
| updatedAt       | Date of the last update                    | \DateTime  |
+-----------------+--------------------------------------------+------------+

.. note::

    This model implements ``CreditCardInterface``. You need to implement an extra method ``getMaskedNumber``
    witch will return the last 4 digits of card number

Payment
-------

Every payment is represented by **Payment** instance and has following properties:

+-------------+---------------------------------------------+------------------------------+
| Method      | Description                                 | Type                         |
+=============+=============================================+==============================+
| method      | payment method associated with this payment | null|PaymentMethodInterface  |
+-------------+---------------------------------------------+------------------------------+
| currency    | payment currency                            | string                       |
+-------------+---------------------------------------------+------------------------------+
| amount      | amount                                      | integer                      |
+-------------+---------------------------------------------+------------------------------+
| state       | state                                       | string                       |
+-------------+---------------------------------------------+------------------------------+
| creditCard  | Credit card as a source                     | CreditCardInterface          |
+-------------+---------------------------------------------+------------------------------+
| details     | details                                     | string                       |
+-------------+---------------------------------------------+------------------------------+
| createdAt   | Date of creation                            | \DateTime                    |
+-------------+---------------------------------------------+------------------------------+
| updatedAt   | Date of the last update                     | \DateTime                    |
+-------------+---------------------------------------------+------------------------------+


The following payment types are available :

+--------------------+--------------------------+
| Type               | Related constant         |
+====================+==========================+
| visa               | BRAND_VISA               |
+--------------------+--------------------------+
| mastercard         | BRAND_MASTERCARD         |
+--------------------+--------------------------+
| discover           | BRAND_DISCOVER           |
+--------------------+--------------------------+
| amex               | BRAND_AMEX               |
+--------------------+--------------------------+
| diners_club        | BRAND_DINERS_CLUB        |
+--------------------+--------------------------+
| jcb                | BRAND_JCB                |
+--------------------+--------------------------+
| switch             | BRAND_SWITCH             |
+--------------------+--------------------------+
| solo               | BRAND_SOLO               |
+--------------------+--------------------------+
| dankort            | BRAND_DANKORT            |
+--------------------+--------------------------+
| maestro            | BRAND_MAESTRO            |
+--------------------+--------------------------+
| forbrugsforeningen | BRAND_FORBRUGSFORENINGEN |
+--------------------+--------------------------+
| laser              | BRAND_LASER              |
+--------------------+--------------------------+

.. note::

    This model implements ``PaymentInterface``.

PaymentMethod
-------------

Every method of payment is represented by **PaymentMethod** instance and has following properties:

+-----------------+--------------------------------------------+------------+
| Method          | Description                                | Type       |
+=================+============================================+============+
| name            | Payments method name                       | string     |
+-----------------+--------------------------------------------+------------+
| type            | Enable or disable the payments method      | boolean    |
+-----------------+--------------------------------------------+------------+
| description     | Payment method description                 | string     |
+-----------------+--------------------------------------------+------------+
| gateway         | Payment gateway to use                     | string     |
+-----------------+--------------------------------------------+------------+
| environment     | Required app environment                   | string     |
+-----------------+--------------------------------------------+------------+
| createdAt       | Date of creation                           | \DateTime  |
+-----------------+--------------------------------------------+------------+
| updatedAt       | Date of the last update                    | \DateTime  |
+-----------------+--------------------------------------------+------------+

.. note::

    This model implements ``PaymentMethodInterface``.

PaymentsSubjectInterface
------------------------

To characterize an object with payment, the object class needs to implement the ``PaymentsSubjectInterface``.

+-------------------------------------------+---------------------------------------------------------------------+----------------------------+
| Method                                    | Description                                                         | Return value               |
+===========================================+=====================================================================+============================+
| getPayments()                             | Get all payments associated with the payment subject                | PaymentInterface[]         |
+-------------------------------------------+---------------------------------------------------------------------+----------------------------+
| hasPayments()                             | Check if order has any payments                                     | Boolean                    |
+-------------------------------------------+---------------------------------------------------------------------+----------------------------+
| addPayment(PaymentInterface $payment)     | Add a payment                                                       | Void                       |
+-------------------------------------------+---------------------------------------------------------------------+----------------------------+
| removePayment(PaymentInterface $payment)  | Remove a payment                                                    | Void                       |
+-------------------------------------------+---------------------------------------------------------------------+----------------------------+
| hasPayment(PaymentInterface $payment)     | Check if the payment subject has certain payment                    | Boolean                    |
+-------------------------------------------+---------------------------------------------------------------------+----------------------------+
