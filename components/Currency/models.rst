Currency
========

Every currency is represented by **Currency** instance and has following properties:

+-----------------+-------------------------------------+------------+
| Method          | Description                         | Type       |
+=================+=====================================+============+
| code            | Code of the currency                | string     |
+-----------------+-------------------------------------+------------+
| exchangeRate    | Exchange rate                       | float      |
+-----------------+-------------------------------------+------------+
| enabled         |                                     | boolean    |
+-----------------+-------------------------------------+------------+
| createdAt       | Date of creation                    | \DateTime  |
+-----------------+-------------------------------------+------------+
| updatedAt       | Date of last update                 | \DateTime  |
+-----------------+-------------------------------------+------------+

CurrencyInterface
=================

.. note::

    This interface asks you to implement a extra method named ``CurrencyInterface::getName()`` wicth will return the human-friendly
    name of the currency