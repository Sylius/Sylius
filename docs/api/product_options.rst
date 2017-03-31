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

+--------------+-------------------------------------------------------------------+
| Field        | Description                                                       |
+==============+===================================================================+
| id           | Id of the product option                                          |
+--------------+-------------------------------------------------------------------+
| code         | Unique product option identifier                                  |
+--------------+-------------------------------------------------------------------+
| position     | The position of the product option among other product options    |
+--------------+-------------------------------------------------------------------+
| translations | Collection of translations (each contains name in given language) |
+--------------+-------------------------------------------------------------------+
| values       | Names of options in which the product can occur                   |
+--------------+-------------------------------------------------------------------+


.. note::

    Read more about :doc:`Product Options in the component docs</components/Product/models>`.

Creating a Product Option
-------------------------

To create a new product option you will need to call the ``/api/v1/products-options/`` endpoint with the ``POST`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    POST /api/v1/product-options/

+-----------------------------------+----------------+----------------------------------------+
| Parameter                         | Parameter type | Description                            |
+===================================+================+========================================+
| Authorization                     | header         | Token received during authentication   |
+-----------------------------------+----------------+----------------------------------------+
| code                              | request        | **(unique)** Product option identifier |
+-----------------------------------+----------------+----------------------------------------+
| values                            | request        | At least two option values             |
+-----------------------------------+----------------+----------------------------------------+

Example
^^^^^^^

To create a new product option use the below method:

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/product-options/ \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X POST \
        --data '
            {
                "code": "MUG_SIZE",
                "values": [
                    {
                        "code": "MUG_SIZE_S",
                        "translations": {
                            "en_US": {
                                "value": "Small"
                            }
                        }
                    },
                    {
                        "code": "MUG_SIZE_L",
                        "translations": {
                            "en_US": {
                                "value": "Large"
                            }
                        }
                    }
                ]
            }
        '

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 201 CREATED

.. code-block:: json

    {
        "id": 1,
        "code": "MUG_SIZE",
        "position": 0,
        "translations": {},
        "values": [
            {
                "code": "MUG_SIZE_S",
                "translations": {
                    "en_US": {
                        "id": 1,
                        "locale": "en_US",
                        "value": "Small"
                    }
                }
            },
            {
                "code": "MUG_SIZE_L",
                "translations": {
                    "en_US": {
                        "id": 2,
                        "locale": "en_US",
                        "value": "Large"
                    }
                }
            }
        ],
        "_links": {
            "self": {
                "href": "\/api\/v1\/product-options\/MUG_SIZE"
            }
        }
    }

.. warning::

    If you try to create a product option without all necessary fields you will receive a ``400 Bad Request`` error, that will contain validation errors.

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/product-options/ \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X POST \

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 400 BAD REQUEST

.. code-block:: json

    {
        "code": 400,
        "message": "Validation Failed",
        "errors": {
            "errors": [
                "Please add at least 2 option values."
            ],
            "children": {
                "position": {},
                "translations": {},
                "values": {},
                "code": {
                    "errors": [
                        "Please enter option code."
                    ]
                }
            }
        }
    }


You can also create a product option with additional (not required) fields:

+------------------------------------+----------------+----------------------------------------------------------------------+
| Parameter                          | Parameter type | Description                                                          |
+====================================+================+======================================================================+
| position                           | request        | Position within sorted product option list of the new product option |
+------------------------------------+----------------+----------------------------------------------------------------------+
| translations['localeCode']['name'] | request        | Name of the product option                                           |
+------------------------------------+----------------+----------------------------------------------------------------------+
| values                             | request        | Collection of option values                                          |
+------------------------------------+----------------+----------------------------------------------------------------------+

Each product option value has the following fields:

+-------------------------------------+----------------+----------------------------------------------+
| Parameter                           | Parameter type | Description                                  |
+=====================================+================+==============================================+
| code                                | request        | **(unique)** Product option value identifier |
+-------------------------------------+----------------+----------------------------------------------+
| translations['localeCode']['value'] | request        | Translation of the value                     |
+-------------------------------------+----------------+----------------------------------------------+

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/product-options/ \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X POST \
        --data '
            {
                "code": "MUG_SIZE",
                "translations": {
                    "de_CH": {
                        "name": "Bechergröße"
                    },
                    "en_US": {
                        "name": "Mug size"
                    }
                },
                "values": [
                    {
                        "code": "MUG_SIZE_S",
                        "translations": {
                            "de_CH": {
                                "value": "Klein"
                            },
                            "en_US": {
                                "value": "Small"
                            }
                        }
                    },
                    {
                        "code": "MUG_SIZE_L",
                        "translations": {
                            "de_CH": {
                                "value": "Groß"
                            },
                            "en_US": {
                                "value": "Large"
                            }
                        }
                    }
                ]
            }
        '

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 201 CREATED

.. code-block:: json

    {
        "id": 1,
        "code": "MUG_SIZE",
        "position": 0,
        "translations": {
            "en_US": {
                "id": 1,
                "locale": "en_US",
                "name": "Mug size"
            },
            "de_CH": {
                "id": 2,
                "locale": "de_CH",
                "name": "Bechergröße"
            }
        },
        "values": [
            {
                "code": "MUG_SIZE_S",
                "translations": {
                    "en_US": {
                        "id": 1,
                        "locale": "en_US",
                        "value": "Small"
                    },
                    "de_CH": {
                        "id": 2,
                        "locale": "de_CH",
                        "value": "Klein"
                    }
                }
            },
            {
                "code": "MUG_SIZE_L",
                "translations": {
                    "de_CH": {
                        "id": 3,
                        "locale": "de_CH",
                        "value": "Groß"
                    },
                    "en_US": {
                        "id": 4,
                        "locale": "en_US",
                        "value": "Large"
                    }
                }
            }
        ],
        "_links": {
            "self": {
                "href": "\/api\/v1\/products\/MUG_SIZE"
            }
        }
    }

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

To see the details of the product option with ``code = MUG_TYPE`` use the below method:

.. code-block:: bash

     $ curl http://demo.sylius.org/api/v1/product-options/MUG_TYPE \
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
        "code": "MUG_TYPE",
        "position": 0,
        "translations": {
            "en_US": {
                "locale": "en_US",
                "id": 1,
                "value": "Mug type"
            }
        },
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
                "href": "\/api\/v1\/products\/MUG_TYPE"
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
                    "translations": {
                        "en_US": {
                            "locale": "en_US",
                            "id": 1,
                            "value": "Mug type"
                        }
                    },
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
                    "translations": {
                        "en_US": {
                            "locale": "en_US",
                            "id": 2,
                            "value": "Sticker size"
                        }
                    },
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
                    "translations": {
                        "en_US": {
                            "locale": "en_US",
                            "id": 3,
                            "value": "T-Shirt color"
                        }
                    },
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
                    "translations": {
                        "en_US": {
                            "locale": "en_US",
                            "id": 4,
                            "value": "T-Shirt size"
                        }
                    },
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

Updating a Product Option
-------------------------

To fully update a product option you will need to call the ``/api/v1/product-options/code`` endpoint with the ``PUT`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    PUT /api/v1/product-options/{code}

+-----------------------------------+----------------+--------------------------------------+
| Parameter                         | Parameter type | Description                          |
+===================================+================+======================================+
| Authorization                     | header         | Token received during authentication |
+-----------------------------------+----------------+--------------------------------------+
| code                              | url attribute  | Unique product option identifier     |
+-----------------------------------+----------------+--------------------------------------+

Example
^^^^^^^

 To fully update the product option with ``code = MUG_SIZE`` use the below method:

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/product-options/MUG_SIZE \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X PUT \
        --data '
            {
                "translations": {
                    "en_US": {
                        "name": "Mug size"
                    }
                }
            }
        '

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content

To update a product option partially you will need to call the ``/api/v1/product-options/code`` endpoint with the ``PATCH`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    PATCH /api/v1/product-options/{code}

+---------------+----------------+--------------------------------------+
| Parameter     | Parameter type | Description                          |
+===============+================+======================================+
| Authorization | header         | Token received during authentication |
+---------------+----------------+--------------------------------------+
| code          | url attribute  | Unique product option identifier     |
+---------------+----------------+--------------------------------------+

Example
^^^^^^^

To partially update the product option with ``code = MUG_SIZE`` use the below method:

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/product-options/MUG_SIZE \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X PATCH \
        --data '
            {
                "translations": {
                    "en_US": {
                        "name": "Mug size"
                    }
                }
            }
        '

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content

Deleting a Product Option
-------------------------

To delete a product option you will need to call the ``/api/v1/product-options/code`` endpoint with the ``DELETE`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    DELETE /api/v1/product-options/{code}

+---------------+----------------+--------------------------------------+
| Parameter     | Parameter type | Description                          |
+===============+================+======================================+
| Authorization | header         | Token received during authentication |
+---------------+----------------+--------------------------------------+
| code          | url attribute  | Unique product option identifier     |
+---------------+----------------+--------------------------------------+

Example
^^^^^^^

To delete the product option with ``code = MUG_SIZE`` use the below method:

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/product-options/MUG_SIZE \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json" \
        -X DELETE

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content
