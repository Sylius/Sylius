Models
======

.. _component_currency_model_currency:

Currency
--------

Every currency is represented by a **Currency** model which by default has the following properties:

+--------------+-------------------------------------------+
| Method       | Description                               |
+==============+===========================================+
| id           | Unique id of the currency                 |
+--------------+-------------------------------------------+
| code         | Currency's code                           |
+--------------+-------------------------------------------+
| createdAt    | Date of creation                          |
+--------------+-------------------------------------------+
| updatedAt    | Date of last update                       |
+--------------+-------------------------------------------+

.. note::
   This model implements :ref:`component_currency_model_currency-interface`.

   For more detailed information go to `Sylius API Currency`_.

.. _Sylius API Currency: http://api.sylius.org/Sylius/Component/Currency/Model/Currency.html
