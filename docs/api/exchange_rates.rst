Exchange Rates API
==================

These endpoints will allow you to easily manage exchange rates. Base URI is `/api/v1/exchange-rates`.

Exchange Rate API response structure
------------------------------------

If you request an exchange rate via API, you will receive an object with the following fields:

+----------------+-------------------------------------------------------------------------------+
| Field          | Description                                                                   |
+================+===============================================================================+
| id             | Id of the exchange rate                                                       |
+----------------+-------------------------------------------------------------------------------+
| ratio          | Exchange rate's ratio                                                         |
+----------------+-------------------------------------------------------------------------------+
| sourceCurrency | :doc:`The currency object serialized with the default data </api/currencies>` |
+----------------+-------------------------------------------------------------------------------+
| targetCurrency | :doc:`The currency object serialized with the default data </api/currencies>` |
+----------------+-------------------------------------------------------------------------------+
| updatedAt      | Last update date of the exchange rate                                         |
+----------------+-------------------------------------------------------------------------------+

If you request for more detailed data, you will receive the default data with the additional field:

+----------------+-----------------------------------------+
| Field          | Description                             |
+================+=========================================+
| createdAt      | Creation date date of the exchange rate |
+----------------+-----------------------------------------+

Creating an Exchange Rate
-------------------------

To create a new exchange rate you will need to call the ``/api/v1/exchange-rates/`` endpoint with the ``POST`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    POST /api/v1/exchange-rates/

+----------------+----------------+--------------------------------------+
| Parameter      | Parameter type | Description                          |
+================+================+======================================+
| Authorization  | header         | Token received during authentication |
+----------------+----------------+--------------------------------------+
| ratio          | request        | Ratio of the Exchange Rate           |
+----------------+----------------+--------------------------------------+
| sourceCurrency | request        | The source currency                  |
+----------------+----------------+--------------------------------------+
| targetCurrency | request        | The target currency                  |
+----------------+----------------+--------------------------------------+

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/exchange-rates/ \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X POST \
        --data '
            {
                "ratio": "0,8515706",
                "sourceCurrency": "EUR",
                "targetCurrency": "GBP"
            }
        '

.. tip::

    Remember that before you will be able to add a new exchange rate, both currencies have to be already defined.

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 201 CREATED

.. code-block:: json

    {
        "id":1,
        "ratio":0.85157,
        "sourceCurrency":{
            "id":2,
            "code":"EUR",
            "_links":{
                "self":{
                    "href":"\/api\/v1\/currencies\/EUR"
                }
            }
        },
        "targetCurrency":{
            "id":3,
            "code":"GBP",
            "_links":{
                "self":{
                    "href":"\/api\/v1\/currencies\/GBP"
                }
            }
        },
        "updatedAt":"2017-02-23T15:00:53+0100",
        "_links":{
            "self":{
                "href":"\/api\/v1\/exchange-rates\/EUR-GBP"
            }
        }
    }

If you try to create an exchange rate without required fields you will receive a ``400 Bad Request`` error.

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/exchange-rates/ \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X POST

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 400 Bad Request

.. code-block:: json

    {
        "code":400,
        "message":"Validation Failed",
        "errors":{
            "errors":[
                "The source and target currencies must differ."
            ],
            "children":{
                "ratio":{
                    "errors":[
                        "Please enter exchange rate ratio."
                    ]
                },
                "sourceCurrency":{
                    "errors":[
                        "This value is not valid."
                    ]
                },
                "targetCurrency":{
                    "errors":[
                        "This value is not valid."
                    ]
                }
            }
        }
    }

Getting a Single Exchange Rate
------------------------------

To retrieve the details of an exchange rate you will need to call the ``/api/v1/exchange-rates/{firstCurrencyCode}-{secondCurrencyCode}`` endpoint with the ``GET`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/exchange-rates/{firstCurrencyCode}-{secondCurrencyCode}

+--------------------+----------------+--------------------------------------+
| Parameter          | Parameter type | Description                          |
+====================+================+======================================+
| Authorization      | header         | Token received during authentication |
+--------------------+----------------+--------------------------------------+
| firstCurrencyCode  | url attribute  | First currency code                  |
+--------------------+----------------+--------------------------------------+
| secondCurrencyCode | url attribute  | Second currency code                 |
+--------------------+----------------+--------------------------------------+

Example
^^^^^^^

To see the details of the exchange rate between ``Euro (code = EUR)`` and ``British Pound (code = GBP)`` use the below method:

.. code-block:: bash

     $ curl http://demo.sylius.org/api/v1/exchange-rates/EUR-GBP \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json"

.. note::

    The *EUR* and *GBP* codes are just an example.

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

     STATUS: 200 OK

.. code-block:: json

   {
        "id":1,
        "ratio":0.85157,
        "sourceCurrency":{
            "id":2,
            "code":"EUR",
            "_links":{
                "self":{
                    "href":"\/api\/v1\/currencies\/EUR"
                }
            }
        },
        "targetCurrency":{
            "id":3,
            "code":"GBP",
            "_links":{
                "self":{
                    "href":"\/api\/v1\/currencies\/GBP"
                }
            }
        },
        "updatedAt":"2017-02-23T15:00:53+0100",
        "_links":{
            "self":{
                "href":"\/api\/v1\/exchange-rates\/EUR-GBP"
            }
        }
    }

.. warning::

    The order of currencies in a request is not important. It doesn't matter if you will request the exchange rate for ``EUR-GBP`` or ``GBP-EUR``
    the response will always be the same (including source and target currencies).

Collection of Currencies
------------------------

To retrieve a paginated list of exchange rates you will need to call the ``/api/v1/exchange-rates/`` endpoint with the ``GET`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/exchange-rates/

+---------------+----------------+-------------------------------------------------------------------+
| Parameter     | Parameter type | Description                                                       |
+===============+================+===================================================================+
| Authorization | header         | Token received during authentication                              |
+---------------+----------------+-------------------------------------------------------------------+
| page          | query          | *(optional)* Number of the page, by default = 1                   |
+---------------+----------------+-------------------------------------------------------------------+
| paginate      | query          | *(optional)* Number of items to display per page, by default = 10 |
+---------------+----------------+-------------------------------------------------------------------+

To see the first page of all exchange rates use the below method:

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/exchange-rates/ \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json"

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "page":1,
        "limit":10,
        "pages":1,
        "total":1,
        "_links":{
            "self":{
                "href":"\/api\/v1\/exchange-rates\/?page=1&limit=10"
            },
            "first":{
                "href":"\/api\/v1\/exchange-rates\/?page=1&limit=10"
            },
            "last":{
                "href":"\/api\/v1\/exchange-rates\/?page=1&limit=10"
            }
        },
        "_embedded":{
            "items":[
                {
                    "id":1,
                    "ratio":0.85157,
                    "sourceCurrency":{
                        "id":2,
                        "code":"EUR",
                        "_links":{
                            "self":{
                                "href":"\/api\/v1\/currencies\/EUR"
                            }
                        }
                    },
                    "targetCurrency":{
                        "id":3,
                        "code":"GBP",
                        "_links":{
                            "self":{
                                "href":"\/api\/v1\/currencies\/GBP"
                            }
                        }
                    },
                    "updatedAt":"2017-02-23T15:00:53+0100",
                    "_links":{
                        "self":{
                            "href":"\/api\/v1\/exchange-rates\/EUR-GBP"
                        }
                    }
                }
            ]
        }
    }

Updating an Exchange Rate
-------------------------

To update an exchange rate you will need to call the ``/api/v1/exchange-rates/firstCurrencyCode-secondCurrencyCode`` endpoint with the ``PUT`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    PUT /api/v1/exchange-rates/{firstCurrencyCode}-{secondCurrencyCode}

+--------------------+----------------+--------------------------------------+
| Parameter          | Parameter type | Description                          |
+====================+================+======================================+
| Authorization      | header         | Token received during authentication |
+--------------------+----------------+--------------------------------------+
| firstCurrencyCode  | url attribute  | First currency code                  |
+--------------------+----------------+--------------------------------------+
| secondCurrencyCode | url attribute  | Second currency code                 |
+--------------------+----------------+--------------------------------------+
| ratio              | request        | Ratio of the Exchange Rate           |
+--------------------+----------------+--------------------------------------+

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/exchange-rates/EUR-GBP \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X PUT \
        --data '
            {
                "ratio": "0,9515706"
            }
        '

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content

If you try to update an exchange rate without the required fields you will receive a ``400 Bad Request`` error.

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/exchange-rates/EUR-GBP \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X PUT

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 400 Bad Request

.. code-block:: json

    {
        "code":400,
        "message":"Validation Failed",
        "errors":{
            "children":{
                "ratio":{
                    "errors":[
                        "Please enter exchange rate ratio."
                    ]
                },
                "sourceCurrency":{

                },
                "targetCurrency":{

                }
            }
        }
    }

Deleting an Exchange Rate
-------------------------

To delete an exchange rate you will need to call the ``/api/v1/exchange-rates/firstCurrencyCode-secondCurrencyCode`` endpoint with the ``DELETE`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    DELETE /api/v1/exchange-rates/{firstCurrencyCode}-{secondCurrencyCode}

+--------------------+----------------+--------------------------------------+
| Parameter          | Parameter type | Description                          |
+====================+================+======================================+
| Authorization      | header         | Token received during authentication |
+--------------------+----------------+--------------------------------------+
| firstCurrencyCode  | url attribute  | First currency code                  |
+--------------------+----------------+--------------------------------------+
| secondCurrencyCode | url attribute  | Second currency code                 |
+--------------------+----------------+--------------------------------------+

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/exchange-rates/EUR-GBP \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json" \
        -X DELETE

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content
