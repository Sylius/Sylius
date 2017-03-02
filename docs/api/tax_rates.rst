Tax Rates API
=============

These endpoints will allow you to easily manage tax rates. Base URI is `/api/v1/tax-rates`.

Tax Rate structure
------------------

Tax Rate API response structure
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

If you request a tax rate via API, you will receive an object with the following fields:

+-----------------+------------------------------------+
| Field           | Description                        |
+=================+====================================+
| id              | Id of the tax rate                 |
+-----------------+------------------------------------+
| code            | Unique tax rate identifier         |
+-----------------+------------------------------------+
| name            | The name of the tax rate           |
+-----------------+------------------------------------+
| amount          | Amount as float (for example 0,23) |
+-----------------+------------------------------------+
| includedInPrice | Is the tax included in price?      |
+-----------------+------------------------------------+
| calculator      | Type of calculator                 |
+-----------------+------------------------------------+
| createdAt       | Date of creation                   |
+-----------------+------------------------------------+
| updatedAt       | Date of last update                |
+-----------------+------------------------------------+

.. note::

    Read more about :doc:`Tax Rates in the component docs</components/Taxation/models>`.

Getting a Single Tax Rate
--------------------------

To retrieve the details of a tax rate you will need to call the ``/api/v1/tax-rates/{code}`` endpoint with the ``GET`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/tax-rates/{code}

+---------------+----------------+--------------------------------------+
| Parameter     | Parameter type | Description                          |
+===============+================+======================================+
| Authorization | header         | Token received during authentication |
+---------------+----------------+--------------------------------------+
| code          | url attribute  | Code of the requested tax rate       |
+---------------+----------------+--------------------------------------+

Example
^^^^^^^

To see the details of the tax rate with ``code = clothing_sales_tax_7`` use the below method:

.. code-block:: bash

     $ curl http://demo.sylius.org/api/v1/tax-rates/clothing_sales_tax_7 \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json"

.. note::

    The *clothing_sales_tax_7* code is just an example. Your value can be different.

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

     STATUS: 200 OK

.. code-block:: json

    {
        "id": 1,
        "code": "clothing_sales_tax_7",
        "name": "Clothing Sales Tax 7%",
        "amount": 0.07,
        "includedInPrice": false,
        "calculator": "default",
        "createdAt": "2017-02-17T15:01:15+0100",
        "updatedAt": "2017-02-17T15:01:15+0100",
        "_links": {
            "self": {
                "href": "\/api\/v1\/tax-rates\/clothing_sales_tax_7"
            },
            "category": {
                "href": "\/api\/v1\/tax-categories\/clothing"
            },
            "zone": {
                "href": "\/api\/v1\/zones\/US"
            }
        }
    }

Collection of Tax Rates
-----------------------

To retrieve a paginated list of tax rates you will need to call the ``/api/v1/tax-rates/`` endpoint with the ``GET`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/tax-rates/

+---------------+----------------+-------------------------------------------------------------------+
| Parameter     | Parameter type | Description                                                       |
+===============+================+===================================================================+
| Authorization | header         | Token received during authentication                              |
+---------------+----------------+-------------------------------------------------------------------+
| page          | query          | *(optional)* Number of the page, by default = 1                   |
+---------------+----------------+-------------------------------------------------------------------+
| paginate      | query          | *(optional)* Number of items to display per page, by default = 10 |
+---------------+----------------+-------------------------------------------------------------------+

To see the first page of all tax rates use the below method:

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/tax-rates/ \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json"

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "page": 1,
        "limit": 10,
        "pages": 1,
        "total": 3,
        "_links": {
            "self": {
                "href": "\/api\/v1\/tax-rates\/?page=1&limit=10"
            },
            "first": {
                "href": "\/api\/v1\/tax-rates\/?page=1&limit=10"
            },
            "last": {
                "href": "\/api\/v1\/tax-rates\/?page=1&limit=10"
            }
        },
        "_embedded": {
            "items": [
                {
                    "id": 1,
                    "code": "clothing_sales_tax_7",
                    "name": "Clothing Sales Tax 7%",
                    "amount": 0.07,
                    "includedInPrice": false,
                    "calculator": "default",
                    "createdAt": "2017-02-17T15:01:15+0100",
                    "updatedAt": "2017-02-17T15:01:15+0100",
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/tax-rates\/clothing_sales_tax_7"
                        },
                        "category": {
                            "href": "\/api\/v1\/tax-categories\/clothing"
                        },
                        "zone": {
                            "href": "\/api\/v1\/zones\/US"
                        }
                    }
                },
                {
                    "id": 2,
                    "code": "books_sales_tax_2",
                    "name": "Books Sales Tax 2%",
                    "amount": 0.02,
                    "includedInPrice": true,
                    "calculator": "default",
                    "createdAt": "2017-02-17T15:01:15+0100",
                    "updatedAt": "2017-02-17T15:01:15+0100",
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/tax-rates\/books_sales_tax_2"
                        },
                        "category": {
                            "href": "\/api\/v1\/tax-categories\/books"
                        },
                        "zone": {
                            "href": "\/api\/v1\/zones\/US"
                        }
                    }
                },
                {
                    "id": 3,
                    "code": "sales_tax_20",
                    "name": "Sales Tax 20%",
                    "amount": 0.2,
                    "includedInPrice": true,
                    "calculator": "default",
                    "createdAt": "2017-02-17T15:01:15+0100",
                    "updatedAt": "2017-02-17T15:01:15+0100",
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/tax-rates\/sales_tax_20"
                        },
                        "category": {
                            "href": "\/api\/v1\/tax-categories\/other"
                        },
                        "zone": {
                            "href": "\/api\/v1\/zones\/US"
                        }
                    }
                }
            ]
        }
    }
