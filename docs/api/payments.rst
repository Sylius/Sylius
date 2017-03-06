Payments API
============

These endpoints will allow you to easily present payments. Base URI is `/api/v1/payments`.

Payment API response structure
------------------------------

If you request a payment via API, you will receive an object with the following fields:

+------------------------+---------------------------------------------------------------------------------+
| Field                  | Description                                                                     |
+========================+=================================================================================+
| id                     | Unique id of the payment                                                        |
+------------------------+---------------------------------------------------------------------------------+
| method                 | :doc:`The payment method object serialized </api/payment_methods>` for the cart |
+------------------------+---------------------------------------------------------------------------------+
| amount                 | The amount of payment                                                           |
+------------------------+---------------------------------------------------------------------------------+
| state                  | :doc:`State of the payment process </book/orders/payments>`                     |
+------------------------+---------------------------------------------------------------------------------+
| _links[self]           | Link to itself                                                                  |
+------------------------+---------------------------------------------------------------------------------+
| _links[payment-method] | Link to the related payment method                                              |
+------------------------+---------------------------------------------------------------------------------+
| _links[order]          | Link to the related order                                                       |
+------------------------+---------------------------------------------------------------------------------+

.. note::

    Read more about :doc:`Payments in the component docs</components/Payment/models>`.

Getting a Single Payment
------------------------

To retrieve the details of a payment you will need to call the ``/api/v1/payments/{id}`` endpoint with the ``GET`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/payments/{id}

+---------------+----------------+--------------------------------------+
| Parameter     | Parameter type | Description                          |
+===============+================+======================================+
| Authorization | header         | Token received during authentication |
+---------------+----------------+--------------------------------------+
| id            | url attribute  | Id of the requested payment          |
+---------------+----------------+--------------------------------------+

Example
^^^^^^^

To see the details of the payment with ``id = 20`` use the below method:

.. code-block:: bash

     $ curl http://demo.sylius.org/api/v1/payments/20 \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json"

.. note::

    The *20* id is just an example. Your value can be different.

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "id":20,
        "method":{
            "id":2,
            "code":"bank_transfer",
            "channels":[
                {
                    "id":1,
                    "code":"US_WEB",
                    "name":"US Web Store",
                    "hostname":"localhost",
                    "color":"DeepSkyBlue",
                    "createdAt":"2017-02-26T11:31:19+0100",
                    "updatedAt":"2017-02-26T11:31:19+0100",
                    "enabled":true,
                    "taxCalculationStrategy":"order_items_based",
                    "_links":{
                        "self":{
                            "href":"\/api\/v1\/channels\/US_WEB"
                        }
                    }
                }
            ],
            "_links":{
                "self":{
                    "href":"\/api\/v1\/payment-methods\/bank_transfer"
                }
            }
        },
        "amount":4507,
        "state":"new",
        "_links":{
            "self":{
                "href":"\/api\/v1\/payments\/20"
            },
            "payment-method":{
                "href":"\/api\/v1\/payment-methods\/bank_transfer"
            },
            "order":{
                "href":"\/api\/v1\/orders\/20"
            }
        }
    }

Collection of Payments
----------------------

To retrieve a paginated list of payments you will need to call the ``/api/v1/payments/`` endpoint with the ``GET`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/payments/

+--------------------+----------------+--------------------------------------------------------------------------------+
| Parameter          | Parameter type | Description                                                                    |
+====================+================+================================================================================+
| Authorization      | header         | Token received during authentication                                           |
+--------------------+----------------+--------------------------------------------------------------------------------+
| page               | query          | *(optional)* Number of the page, by default = 1                                |
+--------------------+----------------+--------------------------------------------------------------------------------+
| limit              | query          | *(optional)* Number of items to display per page, by default = 10              |
+--------------------+----------------+--------------------------------------------------------------------------------+
| sorting[amount]    | query          | *(optional)* Sorting direction on the ``amount`` field (``DESC``/``ASC``)      |
+--------------------+----------------+--------------------------------------------------------------------------------+
| sorting[createdAt] | query          | *(optional)* Sorting direction on the ``createdAt`` field (``ASC`` by default) |
+--------------------+----------------+--------------------------------------------------------------------------------+
| sorting[updatedAt] | query          | *(optional)* Sorting direction on the ``updatedAt`` field (``DESC``/``ASC``)   |
+--------------------+----------------+--------------------------------------------------------------------------------+

Example
^^^^^^^

To see the first page of the paginated list of payments with two payments on each page use the below snippet:

.. code-block:: bash

     $ curl http://demo.sylius.org/api/v1/payments/\?limit\=2 \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json"

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "page":1,
        "limit":2,
        "pages":10,
        "total":20,
        "_links":{
            "self":{
                "href":"\/api\/v1\/payments\/?page=1&limit=2"
            },
            "first":{
                "href":"\/api\/v1\/payments\/?page=1&limit=2"
            },
            "last":{
                "href":"\/api\/v1\/payments\/?page=10&limit=2"
            },
            "next":{
                "href":"\/api\/v1\/payments\/?page=2&limit=2"
            }
        },
        "_embedded":{
            "items":[
                {
                    "id":1,
                    "method":{
                        "id":2,
                        "code":"bank_transfer",
                        "_links":{
                            "self":{
                                "href":"\/api\/v1\/payment-methods\/bank_transfer"
                            }
                        }
                    },
                    "amount":3812,
                    "state":"new",
                    "_links":{
                        "self":{
                            "href":"\/api\/v1\/payments\/1"
                        },
                        "payment-method":{
                            "href":"\/api\/v1\/payment-methods\/bank_transfer"
                        },
                        "order":{
                            "href":"\/api\/v1\/orders\/1"
                        }
                    }
                },
                {
                    "id":2,
                    "method":{
                        "id":2,
                        "code":"bank_transfer",
                        "_links":{
                            "self":{
                                "href":"\/api\/v1\/payment-methods\/bank_transfer"
                            }
                        }
                    },
                    "amount":3915,
                    "state":"new",
                    "_links":{
                        "self":{
                            "href":"\/api\/v1\/payments\/2"
                        },
                        "payment-method":{
                            "href":"\/api\/v1\/payment-methods\/bank_transfer"
                        },
                        "order":{
                            "href":"\/api\/v1\/orders\/2"
                        }
                    }
                }
            ]
        }
    }
