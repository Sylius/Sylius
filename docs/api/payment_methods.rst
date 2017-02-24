Payment Methods API
===================

These endpoints will allow you to easily manage payment methods. Base URI is `/api/v1/payment-methods`.

Payment Method API response structure
-------------------------------------

If you request a payment method via API, you will receive an object with the following fields:

+-----------+-----------------------------------+
| Field     | Description                       |
+===========+===================================+
| id        | Unique id of the payment method   |
+-----------+-----------------------------------+
| code      | Unique code of the payment method |
+-----------+-----------------------------------+
| name      | The payment method's name         |
+-----------+-----------------------------------+
| createdAt | Date of creation                  |
+-----------+-----------------------------------+
| updatedAt | Date of the last update           |
+-----------+-----------------------------------+

.. note::

    Read more about :doc:`Payment Methods in the component docs</components/Payment/models>`.

Getting a Single Payment Method
-------------------------------

To retrieve the details of a payment method you will need to call the ``/api/v1/payment-methods/code`` endpoint with the ``GET`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/payment-methods/{code}

+---------------+----------------+--------------------------------------+
| Parameter     | Parameter type | Description                          |
+===============+================+======================================+
| Authorization | header         | Token received during authentication |
+---------------+----------------+--------------------------------------+
| code          | url attribute  | Code of the requested payment method |
+---------------+----------------+--------------------------------------+

Example
^^^^^^^

To see the details of the payment method with ``code = cash_on_delivery`` use the below method:

.. code-block:: bash

     $ curl http://demo.sylius.org/api/v1/payment-methods/cash_on_delivery \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json"

.. note::

    The *cash_on_delivery* code is just an example. Your value can be different.

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

     STATUS: 200 OK

.. code-block:: json

    {
        "id": 1,
        "code": "cash_on_delivery",
        "position": 0,
        "createdAt": "2017-02-24T16:14:03+0100",
        "updatedAt": "2017-02-24T16:14:03+0100",
        "enabled": true,
        "translations": {
            "en_US": {
                "locale": "en_US",
                "id": 1,
                "name": "Cash on delivery",
                "description": "Rerum expedita sit aut praesentium soluta sint aperiam."
            }
        },
        "channels": [
            {
                "id": 1,
                "code": "US_WEB",
                "name": "US Web Store",
                "hostname": "localhost",
                "color": "SlateBlue",
                "createdAt": "2017-02-24T16:14:03+0100",
                "updatedAt": "2017-02-24T16:14:03+0100",
                "enabled": true,
                "taxCalculationStrategy": "order_items_based",
                "_links": {
                    "self": {
                        "href": "\/api\/v1\/channels\/US_WEB"
                    }
                }
            }
        ],
        "_links": {
            "self": {
                "href": "\/api\/v1\/payment-methods\/cash_on_delivery"
            }
        }
    }
