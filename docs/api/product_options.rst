Product Options API
===================

These endpoints will allow you to easily manage product options. Base URI is `/api/v1/product-options`.

Product Option API response structure
-------------------------------------

If you request a product option via API, you will receive an object with the following fields:

+----------+----------------------------------------------------------------+
| Field    | Description                                                    |
+==========+================================================================+
| id       | Id of the product option                                       |
+----------+----------------------------------------------------------------+
| code     | Unique product option identifier                               |
+----------+----------------------------------------------------------------+
| position | The position of the product option among other product options |
+----------+----------------------------------------------------------------+

If you request for more detailed data, you will receive an object with the following fields:

+----------+----------------------------------------------------------------+
| Field    | Description                                                    |
+==========+================================================================+
| id       | Id of the product option                                       |
+----------+----------------------------------------------------------------+
| code     | Unique product option identifier                               |
+----------+----------------------------------------------------------------+
| position | The position of the product option among other product options |
+----------+----------------------------------------------------------------+
| values   | Names of options in which the product can occur                |
+----------+----------------------------------------------------------------+


.. note::

    Read more about :doc:`Product Options in the component docs</components/Product/models>`.

Getting a Single Product Option
-------------------------------

To retrieve the details of a product option you will need to call the ``/api/v1/product-options/code`` endpoint with the ``GET`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/product-attributes/{code}

+---------------+----------------+--------------------------------------+
| Parameter     | Parameter type | Description                          |
+===============+================+======================================+
| Authorization | header         | Token received during authentication |
+---------------+----------------+--------------------------------------+
| code          | url attribute  | Code of requested the product option |
+---------------+----------------+--------------------------------------+

Example
^^^^^^^

To see the details of the product option with ``code = mug_type`` use the below method:

.. code-block:: bash

     $ curl http://demo.sylius.org/api/v1/product-options/mug_type \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json"

.. note::

    The *mug_type* is just an example. Your value can be different.

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

     STATUS: 200 OK

.. code-block:: json

    {
        "id": 1,
        "code": "mug_type",
        "position": 0,
        "values": [
            {
                "name": "Mug type",
                "code": "mug_type_medium"
            },
            {
                "name": "Mug type",
                "code": "mug_type_double"
            },
            {
                "name": "Mug type",
                "code": "mug_type_monster"
            }
        ]
    }

Collection of Product Options
-----------------------------

To retrieve a paginated list of product options you will need to call the ``/api/v1/product-options/`` endpoint with the ``GET`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/product-options/

+---------------+----------------+-------------------------------------------------------------------+
| Parameter     | Parameter type | Description                                                       |
+===============+================+===================================================================+
| Authorization | header         | Token received during authentication                              |
+---------------+----------------+-------------------------------------------------------------------+
| page          | query          | *(optional)* Number of the page, by default = 1                   |
+---------------+----------------+-------------------------------------------------------------------+
| paginate      | query          | *(optional)* Number of items to display per page, by default = 10 |
+---------------+----------------+-------------------------------------------------------------------+

To see the first page of all product options use the below method:

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/product-options/ \
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
        "total": 4,
        "_links": {
            "self": {
                "href": "\/api\/v1\/product-options\/?page=1&limit=10"
            },
            "first": {
                "href": "\/api\/v1\/product-options\/?page=1&limit=10"
            },
            "last": {
                "href": "\/api\/v1\/product-options\/?page=1&limit=10"
            }
        },
        "_embedded": {
            "items": [
                {
                    "id": 1,
                    "code": "mug_type",
                    "position": 0
                },
                {
                    "id": 2,
                    "code": "sticker_size",
                    "position": 1
                },
                {
                    "id": 3,
                    "code": "t_shirt_color",
                    "position": 2
                },
                {
                    "id": 4,
                    "code": "t_shirt_size",
                    "position": 3
                }
            ]
        }
    }
