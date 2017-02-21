Models
======

.. _component_payment_model_payment:

Payment
-------

Every payment is represented by a **Payment** instance and has the following properties:

+------------+---------------------------------------------+
| Property   | Description                                 |
+============+=============================================+
| id         | Unique id of the payment                    |
+------------+---------------------------------------------+
| method     | Payment method associated with this payment |
+------------+---------------------------------------------+
| currency   | Payment's currency                          |
+------------+---------------------------------------------+
| amount     | Payment's amount                            |
+------------+---------------------------------------------+
| state      | Payment's state                             |
+------------+---------------------------------------------+
| details    | Payment's details                           |
+------------+---------------------------------------------+
| createdAt  | Date of creation                            |
+------------+---------------------------------------------+
| updatedAt  | Date of the last update                     |
+------------+---------------------------------------------+

.. note::
   This model implements the :ref:`component_payment_model_payment-interface`
   and the :ref:`component_payment_model_payment-subject-interface`.

   For more detailed information go to `Sylius API Payment`_.

.. _Sylius API Payment: http://api.sylius.org/Sylius/Component/Payment/Model/Payment.html

.. hint::
   All default payment states are available in :ref:`component_payment_payment-states`.

.. _component_payment_model_payment-method:

PaymentMethod
-------------

Every method of payment is represented by a **PaymentMethod** instance and has the following properties:

+----------------------------+-----------------------------------------------------------------+
| Property                   | Description                                                     |
+============================+=================================================================+
| id                         | Unique id of the payment method                                 |
+----------------------------+-----------------------------------------------------------------+
| code                       | Unique code of the payment method                               |
+----------------------------+-----------------------------------------------------------------+
| name                       | Payment method's name                                           |
+----------------------------+-----------------------------------------------------------------+
| enabled                    | Indicate whether the payment method is enabled                  |
+----------------------------+-----------------------------------------------------------------+
| description                | Payment method's description                                    |
+----------------------------+-----------------------------------------------------------------+
| gatewayConfig              | Payment method's gateway (and its configuration) to use         |
+----------------------------+-----------------------------------------------------------------+
| position                   | Payment method's position among other methods                   |
+----------------------------+-----------------------------------------------------------------+
| environment                | Required app environment                                        |
+----------------------------+-----------------------------------------------------------------+
| feeCalculator              | Calculator for additional fee costs (by default set to 'fixed') |
+----------------------------+-----------------------------------------------------------------+
| feeCalculatorConfiguration | Fee calculator's configuration                                  |
+----------------------------+-----------------------------------------------------------------+
| createdAt                  | Date of creation                                                |
+----------------------------+-----------------------------------------------------------------+
| updatedAt                  | Date of the last update                                         |
+----------------------------+-----------------------------------------------------------------+

.. note::
   This model implements the :ref:`component_payment_model_payment-method-interface`.

   For more detailed information go to `Sylius API PaymentMethod`_.

.. _Sylius API PaymentMethod: http://api.sylius.org/Sylius/Component/Payment/Model/PaymentMethod.html

.. _component_payment_model_payment-method-translation:

PaymentMethodTranslation
------------------------

This model is used to ensure that different locales have the
correct representation of the following payment properties:

+-------------+---------------------------------+
| Property    | Description                     |
+=============+=================================+
| id          | Unique id of the payment method |
+-------------+---------------------------------+
| name        | Payment method's name           |
+-------------+---------------------------------+
| description | Payment method's description    |
+-------------+---------------------------------+

.. note::
   This model implements the :ref:`component_payment_model_payment-method-translation-interface`.

   For more detailed information go to `Sylius API PaymentMethodTranslation`_.

.. _Sylius API PaymentMethodTranslation: http://api.sylius.org/Sylius/Component/Payment/Model/PaymentMethodTranslation.html
