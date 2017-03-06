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

    GET /api/v1/product-options/{code}

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
                "code": "mug_type_medium",
                "translations": {
                    "en_US": {
                        "locale": "en_US",
                        "id": 1,
                        "value": "Medium mug"
                    }
                }
            },
            {
                "code": "mug_type_double",
                "translations": {
                    "en_US": {
                        "locale": "en_US",
                        "id": 2,
                        "value": "Double mug"
                    }
                }
            },
            {
                "code": "mug_type_monster",
                "translations": {
                    "en_US": {
                        "locale": "en_US",
                        "id": 3,
                        "value": "Monster mug"
                    }
                }
            }
        ],
        "_links": {
            "self": {
                "href": "\/api\/v1\/products\/mug_type"
            }
        }
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
        "limit": 4,
        "pages": 1,
        "total": 4,
        "_links": {
            "self": {
                "href": "\/api\/v1\/product-options\/?sorting%5Bcode%5D=desc&page=1&limit=4"
            },
            "first": {
                "href": "\/api\/v1\/product-options\/?sorting%5Bcode%5D=desc&page=1&limit=4"
            },
            "last": {
                "href": "\/api\/v1\/product-options\/?sorting%5Bcode%5D=desc&page=1&limit=4"
            }
        },
        "_embedded": {
            "items": [
                {
                    "id": 1,
                    "code": "mug_type",
                    "position": 0,
                    "values": [
                        {
                            "code": "mug_type_medium",
                            "translations": {
                                "en_US": {
                                    "locale": "en_US",
                                    "id": 1,
                                    "value": "Medium mug"
                                }
                            }
                        },
                        {
                            "code": "mug_type_double",
                            "translations": {
                                "en_US": {
                                    "locale": "en_US",
                                    "id": 2,
                                    "value": "Double mug"
                                }
                            }
                        },
                        {
                            "code": "mug_type_monster",
                            "translations": {
                                "en_US": {
                                    "locale": "en_US",
                                    "id": 3,
                                    "value": "Monster mug"
                                }
                            }
                        }
                    ],
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/products\/mug_type"
                        }
                    }
                },
                {
                    "id": 2,
                    "code": "sticker_size",
                    "position": 1,
                    "values": [
                        {
                            "code": "sticker_size-3",
                            "translations": {
                                "en_US": {
                                    "locale": "en_US",
                                    "id": 4,
                                    "value": "3\""
                                }
                            }
                        },
                        {
                            "code": "sticker_size_5",
                            "translations": {
                                "en_US": {
                                    "locale": "en_US",
                                    "id": 5,
                                    "value": "5\""
                                }
                            }
                        },
                        {
                            "code": "sticker_size_7",
                            "translations": {
                                "en_US": {
                                    "locale": "en_US",
                                    "id": 6,
                                    "value": "7\""
                                }
                            }
                        }
                    ],
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/products\/sticker_size"
                        }
                    }
                },
                {
                    "id": 3,
                    "code": "t_shirt_color",
                    "position": 2,
                    "values": [
                        {
                            "code": "t_shirt_color_red",
                            "translations": {
                                "en_US": {
                                    "locale": "en_US",
                                    "id": 7,
                                    "value": "Red"
                                }
                            }
                        },
                        {
                            "code": "t_shirt_color_black",
                            "translations": {
                                "en_US": {
                                    "locale": "en_US",
                                    "id": 8,
                                    "value": "Black"
                                }
                            }
                        },
                        {
                            "code": "t_shirt_color_white",
                            "translations": {
                                "en_US": {
                                    "locale": "en_US",
                                    "id": 9,
                                    "value": "White"
                                }
                            }
                        }
                    ],
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/products\/t_shirt_color"
                        }
                    }
                },
                {
                    "id": 4,
                    "code": "t_shirt_size",
                    "position": 3,
                    "values": [
                        {
                            "code": "t_shirt_size_s",
                            "translations": {
                                "en_US": {
                                    "locale": "en_US",
                                    "id": 10,
                                    "value": "S"
                                }
                            }
                        },
                        {
                            "code": "t_shirt_size_m",
                            "translations": {
                                "en_US": {
                                    "locale": "en_US",
                                    "id": 11,
                                    "value": "M"
                                }
                            }
                        },
                        {
                            "code": "t_shirt_size_l",
                            "translations": {
                                "en_US": {
                                    "locale": "en_US",
                                    "id": 12,
                                    "value": "L"
                                }
                            }
                        },
                        {
                            "code": "t_shirt_size_xl",
                            "translations": {
                                "en_US": {
                                    "locale": "en_US",
                                    "id": 13,
                                    "value": "XL"
                                }
                            }
                        },
                        {
                            "code": "t_shirt_size_xxl",
                            "translations": {
                                "en_US": {
                                    "locale": "en_US",
                                    "id": 14,
                                    "value": "XXL"
                                }
                            }
                        }
                    ],
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/products\/t_shirt_size"
                        }
                    }
                }
            ]
        }
    }
