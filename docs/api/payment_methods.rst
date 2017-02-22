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

    The *cash_on_delivery* is just an example. Your value can be different.

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

     STATUS: 200 OK

.. code-block:: json

    {
        "id": 1,
        "code": "cash_on_delivery",
        "channels": [
            {
                "id": 1,
                "code": "US_WEB",
                "name": "US Web Store",
                "hostname": "localhost",
                "color": "LightGreen",
                "createdAt": "2017-02-22T15:06:16+0100",
                "updatedAt": "2017-02-22T15:06:16+0100",
                "enabled": true,
                "taxCalculationStrategy": "order_items_based",
                "_links": {
                    "self": {
                        "href": "\/api\/v1\/channels\/US_WEB"
                    }
                }
            }
        ],
        "gatewayConfig": {
            "id": 1,
            "gatewayName": "Offline",
            "factoryName": "offline",
            "config": []
        },
        "_links": {
            "self": {
                "href": "\/api\/v1\/payment-methods\/cash_on_delivery"
            }
        }
    }
