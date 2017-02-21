Payment Methods API
===================

These endpoints will allow you to easily manage payment methods. Base URI is `/api/v1/payment-methods`.

Payment Method structure
------------------------

Payment Method API response structure
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

If you request a payment method via API, you will receive an object with the following fields:

+-----------+-----------------------------------+
| Field     | Description                       |
+===========+===================================+
| id        | Unique id of the payment method   |
+-----------+-----------------------------------+
| code      | Unique code of the payment method |
+-----------+-----------------------------------+
| name      | Payment method's name             |
+-----------+-----------------------------------+
| createdAt | Date of creation                  |
+-----------+-----------------------------------+
| updatedAt | Date of the last update           |
+-----------+-----------------------------------+

.. note::

    Read more about :doc:`Payment Method in the component docs</components/Payment/models>`.

Getting a Single Payment Method
-------------------------------

To retrieve the details of the payment method you will need to call the ``/api/v1/payment-methods/code`` endpoint with the ``GET`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/payment-methods/{code}

+---------------+----------------+--------------------------------------+
| Parameter     | Parameter type | Description                          |
+===============+================+======================================+
| Authorization | header         | Token received during authentication |
+---------------+----------------+--------------------------------------+
| code          | url attribute  | Code of requested payment method     |
+---------------+----------------+--------------------------------------+

Example
^^^^^^^

To see the details for the the payment method with ``code = cash_on_delivery`` use the below method:

.. code-block:: bash

     $ curl http://demo.sylius.org/api/v1/payment-methods/cash_on_delivery \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json"

.. note::

    *cash_on_delivery* is just an example. Your value can be different.

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

     STATUS: 200 OK

.. code-block:: json

    {
        "id": 1,
        "code": "cash_on_delivery",
        "created_at": "2017-02-21T14:50:08+0100",
        "updated_at": "2017-02-21T14:50:08+0100",
        "channels": [
            {
                "id": 1,
                "code": "US_WEB",
                "name": "US Web Store",
                "hostname": "localhost",
                "color": "Violet",
                "created_at": "2017-02-21T14:50:07+0100",
                "updated_at": "2017-02-21T14:50:07+0100",
                "enabled": true,
                "tax_calculation_strategy": "order_items_based",
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
