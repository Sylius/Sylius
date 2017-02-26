Shipments API
=============

These endpoints will allow you to easily present shipments. Base URI is `/api/v1/shipments`.

Shipment API response structure
-------------------------------

If you request a shipping via API, you will receive an object with the following fields:

+-------------------------+-------------------------------------------------------------------------------+
| Field                   | Description                                                                   |
+=========================+===============================================================================+
| id                      | Unique id of the shipment                                                     |
+-------------------------+-------------------------------------------------------------------------------+
| state                   | :doc:`State of the shipping process </book/orders/shipments>`                 |
+-------------------------+-------------------------------------------------------------------------------+
| method                  | :doc:`The shipping method object serialized </api/shipping_methods>` for cart |
+-------------------------+-------------------------------------------------------------------------------+
| _links[self]            | Link to itself                                                                |
+-------------------------+-------------------------------------------------------------------------------+
| _links[shipping-method] | Link to related shipping method                                               |
+-------------------------+-------------------------------------------------------------------------------+
| _links[order]           | Link to related order                                                         |
+-------------------------+-------------------------------------------------------------------------------+

Getting a Single Shipment
-------------------------

To retrieve the details of a shipment you will need to call the ``/api/v1/shipments/{id}`` endpoint with the ``GET`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/shipments/{id}

+---------------+----------------+--------------------------------------+
| Parameter     | Parameter type | Description                          |
+===============+================+======================================+
| Authorization | header         | Token received during authentication |
+---------------+----------------+--------------------------------------+
| id            | url attribute  | Id of the requested shipment         |
+---------------+----------------+--------------------------------------+

Example
^^^^^^^

To see the details of the shipment method with ``id = 20`` use the below method:

.. code-block:: bash

     $ curl http://demo.sylius.org/api/v1/shipments/20 \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json"

.. note::

    The *id = 20* is just an example. Your value can be different.

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

     STATUS: 200 OK

.. code-block:: json

    {
        "id":20,
        "state":"ready",
        "method":{
            "id":1,
            "code":"ups",
            "enabled":true,
            "_links":{
                "self":{
                    "href":"\/api\/v1\/shipping-methods\/ups"
                },
                "zone":{
                    "href":"\/api\/v1\/zones\/US"
                }
            }
        },
        "_links":{
            "self":{
                "href":"\/api\/v1\/shipments\/20"
            },
            "shipping-method":{
                "href":"\/api\/v1\/shipping-methods\/ups"
            },
            "order":{
                "href":"\/api\/v1\/orders\/20"
            }
        }
    }

Collection of Shipments
-----------------------

To retrieve a paginated list of shipments you will need to call the ``/api/v1/shipments/`` endpoint with the ``GET`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/shipments/{id}

+--------------------+----------------+--------------------------------------------------------------------+
| Parameter          | Parameter type | Description                                                        |
+====================+================+====================================================================+
| Authorization      | header         | Token received during authentication                               |
+--------------------+----------------+--------------------------------------------------------------------+
| page               | query          | *(optional)* Number of the page, by default = 1                    |
+--------------------+----------------+--------------------------------------------------------------------+
| limit              | query          | *(optional)* Number of items to display per page, by default = 10  |
+--------------------+----------------+--------------------------------------------------------------------+
| sorting[createdAt] | query          | *(optional)* Order of sorting on created at field (asc by default) |
+--------------------+----------------+--------------------------------------------------------------------+
| sorting[updatedAt] | query          | *(optional)* Order of sorting on updated at field (desc/asc)       |
+--------------------+----------------+--------------------------------------------------------------------+

Example
^^^^^^^

To see first page of paginated list of shipments with two shipments on each page use the below snippet:

.. code-block:: bash

     $ curl http://demo.sylius.org/api/v1/shipments/\?limit\=2 \
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
                "href":"\/api\/v1\/shipments\/?page=1&limit=2"
            },
            "first":{
                "href":"\/api\/v1\/shipments\/?page=1&limit=2"
            },
            "last":{
                "href":"\/api\/v1\/shipments\/?page=10&limit=2"
            },
            "next":{
                "href":"\/api\/v1\/shipments\/?page=2&limit=2"
            }
        },
        "_embedded":{
            "items":[
                {
                    "id":1,
                    "state":"ready",
                    "method":{
                        "id":2,
                        "code":"dhl_express",
                        "enabled":true,
                        "_links":{
                            "self":{
                                "href":"\/api\/v1\/shipping-methods\/dhl_express"
                            },
                            "zone":{
                                "href":"\/api\/v1\/zones\/US"
                            }
                        }
                    },
                    "_links":{
                        "self":{
                            "href":"\/api\/v1\/shipments\/1"
                        },
                        "shipping-method":{
                            "href":"\/api\/v1\/shipping-methods\/dhl_express"
                        },
                        "order":{
                            "href":"\/api\/v1\/orders\/1"
                        }
                    }
                },
                {
                    "id":2,
                    "state":"ready",
                    "method":{
                        "id":2,
                        "code":"dhl_express",
                        "enabled":true,
                        "_links":{
                            "self":{
                                "href":"\/api\/v1\/shipping-methods\/dhl_express"
                            },
                            "zone":{
                                "href":"\/api\/v1\/zones\/US"
                            }
                        }
                    },
                    "_links":{
                        "self":{
                            "href":"\/api\/v1\/shipments\/2"
                        },
                        "shipping-method":{
                            "href":"\/api\/v1\/shipping-methods\/dhl_express"
                        },
                        "order":{
                            "href":"\/api\/v1\/orders\/2"
                        }
                    }
                }
            ]
        }
    }
