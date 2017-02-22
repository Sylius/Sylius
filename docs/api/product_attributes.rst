Product Attributes API
======================

These endpoints will allow you to easily manage product attributes. Base URI is `/api/v1/product-attributes`.

Product Attribute API response structure
----------------------------------------

If you request a product attribute via API, you will receive an object with the following fields:

+----------+----------------------------------------------------------------------+
| Field    | Description                                                          |
+==========+======================================================================+
| id       | Id of the product attribute                                          |
+----------+----------------------------------------------------------------------+
| code     | Unique product attribute identifier                                  |
+----------+----------------------------------------------------------------------+
| position | The position of the product attribute among other product attributes |
+----------+----------------------------------------------------------------------+
| name     | Name of the product attribute                                        |
+----------+----------------------------------------------------------------------+

If you request for more detailed data, you will receive an object with the following fields:

+-----------+----------------------------------------------------------------------+
| Field     | Description                                                          |
+===========+======================================================================+
| id        | Id of the product attribute                                          |
+-----------+----------------------------------------------------------------------+
| code      | Unique product attribute identifier                                  |
+-----------+----------------------------------------------------------------------+
| position  | The position of the product attribute among other product attributes |
+-----------+----------------------------------------------------------------------+
| name      | Name of the product attribute                                        |
+-----------+----------------------------------------------------------------------+
| updatedAt | Last update date of the product attribute                            |
+-----------+----------------------------------------------------------------------+
| createdAt | Creation date of the product attribute                               |
+-----------+----------------------------------------------------------------------+

.. note::

    Read more about :doc:`Product Attributes in the component docs</components/Product/models>`.

Getting a Single Product Attribute
----------------------------------

To retrieve the details of a product attribute you will need to call the ``/api/v1/product-attributes/code`` endpoint with the ``GET`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/product-attributes/{code}

+---------------+----------------+------------------------------------------+
| Parameter     | Parameter type | Description                              |
+===============+================+==========================================+
| Authorization | header         | Token received during authentication     |
+---------------+----------------+------------------------------------------+
| code          | url attribute  | Code of the requested product attribute  |
+---------------+----------------+------------------------------------------+

Example
^^^^^^^

To see the details of the product attribute with ``code = sticker_paper`` use the below method:

.. code-block:: bash

     $ curl http://demo.sylius.org/api/v1/product-attributes/sticker_paper \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json"

.. note::

    The *sticker_paper* is just an example. Your value can be different.

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

     STATUS: 200 OK

.. code-block:: json

   {
        "id": 2,
        "createdAt": "2017-02-17T15:01:18+0100",
        "updatedAt": "2017-02-20T12:46:12+0100",
        "_links": {
            "self": {
                "href": "\/api\/v1\/product-attributes\/sticker_paper"
            }
        }
    }

Collection of Product Attributes
--------------------------------

To retrieve a paginated list of product attributes you will need to call the ``/api/v1/product-attributes/`` endpoint with the ``GET`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/product-attributes/

+---------------+----------------+-------------------------------------------------------------------+
| Parameter     | Parameter type | Description                                                       |
+===============+================+===================================================================+
| Authorization | header         | Token received during authentication                              |
+---------------+----------------+-------------------------------------------------------------------+
| page          | query          | *(optional)* Number of the page, by default = 1                   |
+---------------+----------------+-------------------------------------------------------------------+
| paginate      | query          | *(optional)* Number of items to display per page, by default = 10 |
+---------------+----------------+-------------------------------------------------------------------+

To see the first page of all product attributes use the below method:

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/product-attributes/ \
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
        "total": 9,
        "_links": {
            "self": {
                "href": "\/api\/v1\/product-attributes\/?page=1&limit=10"
            },
            "first": {
                "href": "\/api\/v1\/product-attributes\/?page=1&limit=10"
            },
            "last": {
                "href": "\/api\/v1\/product-attributes\/?page=1&limit=10"
            }
        },
        "_embedded": {
            "items": [
                {
                    "id": 1,
                    "createdAt": "2017-02-17T15:01:16+0100",
                    "updatedAt": "2017-02-17T15:01:16+0100",
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/product-attributes\/mug_material"
                        }
                    }
                },
                {
                    "id": 2,
                    "createdAt": "2017-02-17T15:01:18+0100",
                    "updatedAt": "2017-02-20T12:46:12+0100",
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/product-attributes\/sticker_paper"
                        }
                    }
                },
                {
                    "id": 3,
                    "createdAt": "2017-02-17T15:01:18+0100",
                    "updatedAt": "2017-02-17T15:01:18+0100",
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/product-attributes\/sticker_resolution"
                        }
                    }
                },
                {
                    "id": 4,
                    "createdAt": "2017-02-17T15:01:20+0100",
                    "updatedAt": "2017-02-17T15:01:20+0100",
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/product-attributes\/book_author"
                        }
                    }
                },
                {
                    "id": 5,
                    "createdAt": "2017-02-17T15:01:20+0100",
                    "updatedAt": "2017-02-17T15:01:20+0100",
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/product-attributes\/book_isbn"
                        }
                    }
                },
                {
                    "id": 6,
                    "createdAt": "2017-02-17T15:01:20+0100",
                    "updatedAt": "2017-02-17T15:01:20+0100",
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/product-attributes\/book_pages"
                        }
                    }
                },
                {
                    "id": 7,
                    "createdAt": "2017-02-17T15:01:22+0100",
                    "updatedAt": "2017-02-17T15:01:22+0100",
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/product-attributes\/t_shirt_brand"
                        }
                    }
                },
                {
                    "id": 8,
                    "createdAt": "2017-02-17T15:01:22+0100",
                    "updatedAt": "2017-02-17T15:01:22+0100",
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/product-attributes\/t_shirt_collection"
                        }
                    }
                },
                {
                    "id": 9,
                    "createdAt": "2017-02-17T15:01:22+0100",
                    "updatedAt": "2017-02-17T15:01:22+0100",
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/product-attributes\/t_shirt_material"
                        }
                    }
                }
            ]
        }
    }
