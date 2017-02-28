Product Attributes API
======================

These endpoints will allow you to easily manage product attributes. Base URI is `/api/v1/product-attributes`.

Product Attribute API response structure
----------------------------------------

If you request a product attribute via API, you will receive an object with the following fields:

+--------------+----------------------------------------------------------------------+
| Field        | Description                                                          |
+==============+======================================================================+
| id           | Id of the product attribute                                          |
+--------------+----------------------------------------------------------------------+
| code         | Unique product attribute identifier                                  |
+--------------+----------------------------------------------------------------------+
| position     | The position of the product attribute among other product attributes |
+--------------+----------------------------------------------------------------------+
| type         | Type of the product attribute (for example text)                     |
+--------------+----------------------------------------------------------------------+
| translations | Collection of translations (each contains name in given language)    |
+--------------+----------------------------------------------------------------------+

If you request for more detailed data, you will receive an object with the following fields:

+--------------+----------------------------------------------------------------------+
| Field        | Description                                                          |
+==============+======================================================================+
| id           | Id of the product attribute                                          |
+--------------+----------------------------------------------------------------------+
| code         | Unique product attribute identifier                                  |
+--------------+----------------------------------------------------------------------+
| position     | The position of the product attribute among other product attributes |
+--------------+----------------------------------------------------------------------+
| type         | Type of the product attribute (for example text)                     |
+--------------+----------------------------------------------------------------------+
| translations | Collection of translations (each contains name in given language)    |
+--------------+----------------------------------------------------------------------+
| updatedAt    | Last update date of the product attribute                            |
+--------------+----------------------------------------------------------------------+
| createdAt    | Creation date of the product attribute                               |
+--------------+----------------------------------------------------------------------+
| values       | Values of the product attribute                                      |
+--------------+----------------------------------------------------------------------+

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

    The *sticker_paper* code is just an example. Your value can be different.

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

     STATUS: 200 OK

.. code-block:: json

    {
        "id": 2,
        "code": "sticker_paper",
        "type": "text",
        "values": [
            {
                "code": "sticker_paper",
                "name": "Sticker paper",
                "value": "Paper from tree Me-Gusta",
                "type": "text",
                "id": 16
            },
            {
                "code": "sticker_paper",
                "name": "Sticker paper",
                "value": "Paper from tree Lemon-San",
                "type": "text",
                "id": 18
            },
            {
                "code": "sticker_paper",
                "name": "Sticker paper",
                "value": "Paper from tree Wung",
                "type": "text",
                "id": 20
            },
            {
                "code": "sticker_paper",
                "name": "Sticker paper",
                "value": "Paper from tree Tanajno",
                "type": "text",
                "id": 22
            },
            {
                "code": "sticker_paper",
                "name": "Sticker paper",
                "value": "Paper from tree Tanajno",
                "type": "text",
                "id": 24
            },
            {
                "code": "sticker_paper",
                "name": "Sticker paper",
                "value": "Paper from tree Wung",
                "type": "text",
                "id": 26
            },
            {
                "code": "sticker_paper",
                "name": "Sticker paper",
                "value": "Paper from tree Wung",
                "type": "text",
                "id": 28
            },
            {
                "code": "sticker_paper",
                "name": "Sticker paper",
                "value": "Paper from tree Wung",
                "type": "text",
                "id": 30
            },
            {
                "code": "sticker_paper",
                "name": "Sticker paper",
                "value": "Paper from tree Me-Gusta",
                "type": "text",
                "id": 32
            },
            {
                "code": "sticker_paper",
                "name": "Sticker paper",
                "value": "Paper from tree Wung",
                "type": "text",
                "id": 34
            },
            {
                "code": "sticker_paper",
                "name": "Sticker paper",
                "value": "Paper from tree Wung",
                "type": "text",
                "id": 36
            },
            {
                "code": "sticker_paper",
                "name": "Sticker paper",
                "value": "Paper from tree Tanajno",
                "type": "text",
                "id": 38
            },
            {
                "code": "sticker_paper",
                "name": "Sticker paper",
                "value": "Paper from tree Lemon-San",
                "type": "text",
                "id": 40
            },
            {
                "code": "sticker_paper",
                "name": "Sticker paper",
                "value": "Paper from tree Lemon-San",
                "type": "text",
                "id": 42
            },
            {
                "code": "sticker_paper",
                "name": "Sticker paper",
                "value": "Paper from tree Me-Gusta",
                "type": "text",
                "id": 44
            }
        ],
        "position": 1,
        "createdAt": "2017-02-24T16:14:05+0100",
        "updatedAt": "2017-02-24T16:14:05+0100",
        "translations": {
            "en_US": {
                "locale": "en_US",
                "id": 2,
                "name": "Sticker paper"
            }
        },
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
        "total": 10,
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
                    "code": "mug_material",
                    "type": "select",
                    "position": 0,
                    "translations": {
                        "en_US": {
                            "locale": "en_US",
                            "id": 1,
                            "name": "Mug material"
                        }
                    },
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/product-attributes\/mug_material"
                        }
                    }
                },
                {
                    "id": 2,
                    "code": "sticker_paper",
                    "type": "text",
                    "position": 1,
                    "translations": {
                        "en_US": {
                            "locale": "en_US",
                            "id": 2,
                            "name": "Sticker paper"
                        }
                    },
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/product-attributes\/sticker_paper"
                        }
                    }
                },
                {
                    "id": 3,
                    "code": "sticker_resolution",
                    "type": "text",
                    "position": 2,
                    "translations": {
                        "en_US": {
                            "locale": "en_US",
                            "id": 3,
                            "name": "Sticker resolution"
                        }
                    },
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/product-attributes\/sticker_resolution"
                        }
                    }
                },
                {
                    "id": 4,
                    "code": "book_author",
                    "type": "text",
                    "position": 3,
                    "translations": {
                        "en_US": {
                            "locale": "en_US",
                            "id": 4,
                            "name": "Book author"
                        }
                    },
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/product-attributes\/book_author"
                        }
                    }
                },
                {
                    "id": 5,
                    "code": "book_isbn",
                    "type": "text",
                    "position": 4,
                    "translations": {
                        "en_US": {
                            "locale": "en_US",
                            "id": 5,
                            "name": "Book ISBN"
                        }
                    },
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/product-attributes\/book_isbn"
                        }
                    }
                },
                {
                    "id": 6,
                    "code": "book_pages",
                    "type": "integer",
                    "position": 5,
                    "translations": {
                        "en_US": {
                            "locale": "en_US",
                            "id": 6,
                            "name": "Book pages"
                        }
                    },
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/product-attributes\/book_pages"
                        }
                    }
                },
                {
                    "id": 7,
                    "code": "book_genre",
                    "type": "select",
                    "position": 6,
                    "translations": {
                        "en_US": {
                            "locale": "en_US",
                            "id": 7,
                            "name": "Book genre"
                        }
                    },
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/product-attributes\/book_genre"
                        }
                    }
                },
                {
                    "id": 8,
                    "code": "t_shirt_brand",
                    "type": "text",
                    "position": 7,
                    "translations": {
                        "en_US": {
                            "locale": "en_US",
                            "id": 8,
                            "name": "T-Shirt brand"
                        }
                    },
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/product-attributes\/t_shirt_brand"
                        }
                    }
                },
                {
                    "id": 9,
                    "code": "t_shirt_collection",
                    "type": "text",
                    "position": 8,
                    "translations": {
                        "en_US": {
                            "locale": "en_US",
                            "id": 9,
                            "name": "T-Shirt collection"
                        }
                    },
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/product-attributes\/t_shirt_collection"
                        }
                    }
                },
                {
                    "id": 10,
                    "code": "t_shirt_material",
                    "type": "text",
                    "position": 9,
                    "translations": {
                        "en_US": {
                            "locale": "en_US",
                            "id": 10,
                            "name": "T-Shirt material"
                        }
                    },
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/product-attributes\/t_shirt_material"
                        }
                    }
                }
            ]
        }
    }
