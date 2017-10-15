Models
======

TaxRate
-------

Tax rate model holds the configuration for particular tax rate.

+-----------------+------------------------------------+
| Property        | Description                        |
+=================+====================================+
| id              | Unique id of the tax rate          |
+-----------------+------------------------------------+
| code            | Unique code of the tax rate        |
+-----------------+------------------------------------+
| category        | Tax rate category                  |
+-----------------+------------------------------------+
| name            | Name of the rate                   |
+-----------------+------------------------------------+
| amount          | Amount as float (for example 0,23) |
+-----------------+------------------------------------+
| includedInPrice | Is the tax included in price?      |
+-----------------+------------------------------------+
| calculator      | Type of calculator                 |
+-----------------+------------------------------------+
| createdAt       | Date when the rate was created     |
+-----------------+------------------------------------+
| updatedAt       | Date of the last tax rate update   |
+-----------------+------------------------------------+

.. note::
    This model implements ``TaxRateInterface``.

TaxCategory
-----------

Tax category model holds the configuration for particular tax category.

+-----------------+--------------------------------------------------------+
| Property        | Description                                            |
+=================+========================================================+
| id              | Unique id of the tax category                          |
+-----------------+--------------------------------------------------------+
| code            | Unique code of the tax category                        |
+-----------------+--------------------------------------------------------+
| name            | Name of the category                                   |
+-----------------+--------------------------------------------------------+
| description     | Description of tax category                            |
+-----------------+--------------------------------------------------------+
| rates           | Collection of tax rates belonging to this tax category |
+-----------------+--------------------------------------------------------+
| createdAt       | Date when the category was created                     |
+-----------------+--------------------------------------------------------+
| updatedAt       | Date of the last tax category update                   |
+-----------------+--------------------------------------------------------+

.. note::
    This model implements ``TaxCategoryInterface``.

