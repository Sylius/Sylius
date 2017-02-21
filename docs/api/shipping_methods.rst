Shipping Methods API
====================

These endpoints will allow you to easily manage shipping methods. Base URI is `/api/v1/shipping-methods`.

Shipping Method structure
-------------------------

Shipping Method API response structure
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

If you request a shipping method via API, you will receive an object with the following fields:

+---------------------+----------------------------------------------------+
| Field               | Description                                        |
+=====================+====================================================+
| id                  | Id of the shipping method                          |
+---------------------+----------------------------------------------------+
| code                | Unique shipping method identifier                  |
+---------------------+----------------------------------------------------+
| name                | The name of the shipping method                    |
+---------------------+----------------------------------------------------+
| enabled             | Determine if shipping method is enabled            |
+---------------------+----------------------------------------------------+
| categoryRequirement | Reference to constant from ShippingMethodInterface |
+---------------------+----------------------------------------------------+
| calculator          | Reference to constant from DefaultCalculators      |
+---------------------+----------------------------------------------------+
| configuration       | Extra configuration for calculator                 |
+---------------------+----------------------------------------------------+
| createdAt           | Date of creation                                   |
+---------------------+----------------------------------------------------+
| updatedAt           | Date of last update                                |
+---------------------+----------------------------------------------------+

.. note::

    Read more about :doc:`Shipping Method in the component docs</components/Shipping/models>`.

Getting a Single Shipping Method
--------------------------------

To retrieve the details of the shipping method you will need to call the ``/api/v1/shipping-methods/code`` endpoint with the ``GET`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/shipping-methods/{code}

+---------------+----------------+--------------------------------------+
| Parameter     | Parameter type | Description                          |
+===============+================+======================================+
| Authorization | header         | Token received during authentication |
+---------------+----------------+--------------------------------------+
| code          | url attribute  | Code of requested shipping method    |
+---------------+----------------+--------------------------------------+

Example
^^^^^^^

To see the details for the the shipping method with ``code = ups`` use the below method:

.. code-block:: bash

     $ curl http://demo.sylius.org/api/v1/shipping-methods/ups \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json"

.. note::

    *ups* is just an example. Your value can be different.

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

     STATUS: 200 OK

.. code-block:: json

    {
        "id": 1,
        "code": "ups",
        "category_requirement": 1,
        "calculator": "flat_rate",
        "configuration": {
            "US_WEB": {
                "amount": 1080
            }
        },
        "created_at": "2017-02-17T15:01:15+0100",
        "updated_at": "2017-02-17T15:01:15+0100",
        "enabled": true,
        "_links": {
            "self": {
                "href": "\/api\/v1\/shipping-methods\/ups"
            },
            "zone": {
                "href": "\/api\/v1\/zones\/US"
            }
        }
    }
